<?php

namespace App\Services\WhatsApp;

use App\Models\CalendarEvent;
use App\Models\City;
use App\Models\Day;
use App\Models\DirectOnlineGroup;
use App\Models\Meeting;
use App\Models\WhatsAppBroadcastLog;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppSubscriber;
use App\Services\JftService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WhatsAppBotService
{
    protected WhatsAppClient $client;
    protected JftService $jftService;

    public function __construct(WhatsAppClient $client, JftService $jftService)
    {
        $this->client = $client;
        $this->jftService = $jftService;
    }

    /**
     * Handle an incoming message webhook payload.
     */
    public function handleIncomingMessage(array $payload): void
    {
        // Unwrap nested 'payload' or 'data' if present in the Go microservice payload
        $inner = (isset($payload['payload']) && is_array($payload['payload']))
            ? $payload['payload']
            : ((isset($payload['data']) && is_array($payload['data'])) ? $payload['data'] : $payload);

        $isFromMe = !empty($inner['is_from_me']) || !empty($inner['from_me']) || !empty($payload['is_from_me']) || !empty($payload['from_me']);

        $senderJid = $inner['sender'] ?? $inner['from'] ?? $inner['phone'] ?? $payload['sender'] ?? $payload['from'] ?? $payload['phone'] ?? null;
        $chatJid = $inner['chat'] ?? $inner['to'] ?? $inner['remote_jid'] ?? $payload['chat'] ?? $payload['to'] ?? null;

        // If the message is outgoing from our phone, the contact is in chatJid or senderJid
        $targetJid = ($isFromMe && !empty($chatJid)) ? $chatJid : ($senderJid ?: $chatJid);

        $body = trim((string) ($inner['message'] ?? $inner['text'] ?? $inner['body'] ?? $payload['message'] ?? $payload['text'] ?? $payload['body'] ?? ''));
        $messageId = $inner['message_id'] ?? $inner['id'] ?? $payload['message_id'] ?? $payload['id'] ?? null;
        $pushName = $inner['name'] ?? $inner['push_name'] ?? $payload['name'] ?? $payload['push_name'] ?? null;

        if (empty($targetJid)) {
            Log::warning('[WhatsAppBot] Ignored webhook payload without sender/target: ' . json_encode($payload));
            return;
        }

        $phone = $this->client->normalizePhone($targetJid);
        $formattedJid = $this->client->formatJid($targetJid);

        $isDevTest = (bool) config('whatsapp.dev_mode', false);

        // Find or create conversation record
        $conversation = WhatsAppConversation::firstOrCreate(
            ['jid' => $formattedJid],
            [
                'phone' => $phone,
                'name' => $pushName,
                'is_dev_test' => $isDevTest,
                'last_interaction_at' => Carbon::now(),
            ]
        );

        // Update push name and interaction timestamp
        $conversation->update([
            'name' => $pushName ?: $conversation->name,
            'phone' => $phone ?: $conversation->phone,
            'last_interaction_at' => Carbon::now(),
        ]);

        // If the message is sent from our paired phone directly by a human volunteer
        if ($isFromMe) {
            // If already logged by Laravel bot/inbox, ignore duplicate echo
            if ($messageId && WhatsAppMessage::where('message_id', $messageId)->exists()) {
                return;
            }

            WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'message_id' => $messageId,
                'direction' => 'outgoing',
                'sender_type' => 'agent',
                'category' => 'phone_reply',
                'message_type' => 'text',
                'body' => $body,
                'status' => 'sent',
                'raw_payload' => $payload,
            ]);

            // Automatically pause the bot (Live Agent active for 30 minutes)
            $conversation->enableLiveAgent();
            $conversation->update(['last_interaction_at' => Carbon::now()]);

            Log::info("[WhatsAppBot] Recorded volunteer message from phone to {$phone}; Live Agent active.");
            return;
        }

        // Avoid logging duplicate incoming message
        if ($messageId && WhatsAppMessage::where('message_id', $messageId)->exists()) {
            return;
        }

        // Record incoming message in database
        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'message_id' => $messageId,
            'direction' => 'incoming',
            'sender_type' => 'user',
            'category' => null,
            'message_type' => 'text',
            'body' => $body,
            'status' => 'received',
            'raw_payload' => $payload,
        ]);

        // If master bot automation switch is disabled, skip autoreply
        if (!config('whatsapp.bot_enabled', true)) {
            Log::info("[WhatsAppBot] Bot automation disabled; message recorded for {$phone}");
            return;
        }

        $normalizedBody = $this->normalizeArabicIndicDigits($body);
        $lowerBody = mb_strtolower(trim($normalizedBody));

        // 1. Check for command to resume bot mode from live agent takeover
        if (in_array($lowerBody, ['إنهاء المحادثة', 'انهاء المحادثة', 'انهاء', 'إنهاء', 'bot', 'الروبوت', 'خروج'])) {
            $conversation->disableLiveAgent();
            $conversation->update(['current_step' => null, 'step_data' => null]);
            $this->replyAndLog(
                $conversation,
                "تم إعادة تفعيل الرد الآلي بنجاح 🤖\nشكراً لتواصلك مع زمالة المدمنين المجهولين - مصر.\n\n" . $this->getMainMenuText(),
                'menu'
            );
            return;
        }

        // 2. Check if Live Agent Mode is actively suppressing the bot
        if ($conversation->isLiveAgentActive()) {
            Log::info("[WhatsAppBot] Suppressed autoreply for {$phone}: Live Agent active until {$conversation->live_agent_until}");
            return;
        }

        // 3. Handle Active Multi-Step Flow (HIGHEST PRIORITY before keyword routing)
        if ($conversation->current_step === 'awaiting_city') {
            $this->handleMeetingCitySelection($conversation, $lowerBody);
            return;
        }

        // 4. Check for Live Agent Request
        if (in_array($lowerBody, ['0', 'متطوع', 'تحدث مع متطوع', 'تحدث مع شخص', 'agent', 'volunteer', 'مساعدة'])) {
            $conversation->enableLiveAgent();
            $this->replyAndLog(
                $conversation,
                "تم تحويل محادثتك إلى أحد متطوعي خدمة خط المساعدة 🤝\n\nسيتواصل معك متطوع في أقرب وقت ممكن بسرية تامة لمساعدتك في التعافي.\n(للعودة إلى الرد الآلي في أي وقت، أرسل كلمة: *انهاء*)",
                'helpline'
            );
            return;
        }

        // 5. Check for Broadcast Subscription commands
        if (in_array($lowerBody, ['7', 'اشتراك', 'اشتراك يومي', 'subscribe', 'اشتراك لليوم فقط', 'اشتراك فقط لليوم'])) {
            WhatsAppSubscriber::updateOrCreate(
                ['jid' => $formattedJid],
                [
                    'phone' => $phone,
                    'name' => $pushName,
                    'channel' => 'jft',
                    'is_active' => true,
                    'is_dev_subscriber' => $isDevTest,
                    'subscribed_at' => Carbon::now(),
                    'unsubscribed_at' => null,
                ]
            );

            $this->replyAndLog(
                $conversation,
                "تم اشتراكك بنجاح في خدمة رسائل \"لليوم فقط\" اليومية 🌅\n\nستصلك القراءة اليومية كل صباح في تمام الساعة 7:00 بتوقيت القاهرة.\n(لإلغاء الاشتراك في أي وقت، أرسل: *الغاء الاشتراك*)",
                'subscription'
            );
            return;
        }

        if (in_array($lowerBody, ['الغاء الاشتراك', 'إلغاء الاشتراك', 'unsubscribe', 'الغاء'])) {
            $subscriber = WhatsAppSubscriber::where('jid', $formattedJid)->first();
            if ($subscriber) {
                $subscriber->update([
                    'is_active' => false,
                    'unsubscribed_at' => Carbon::now(),
                ]);
            }

            $this->replyAndLog(
                $conversation,
                "تم إلغاء اشتراكك من خدمة رسائل \"لليوم فقط\" بنجاح 🕊️\n\nيمكنك إعادة الاشتراك في أي وقت بإرسال كلمة: *اشتراك* أو رقم *7*.",
                'subscription'
            );
            return;
        }

        // 6. Direct Keyword / Numeric Option Triggers
        if (in_array($lowerBody, ['1', 'jft', 'فقط لليوم', 'قراءة اليوم', 'قراءه اليوم', 'today'])) {
            $this->handleJftReading($conversation);
            return;
        }

        if (in_array($lowerBody, ['2', 'اجتماعات', 'اجتماع', 'meetings', 'مواعيد', 'meeting'])) {
            $this->promptMeetingCitySelection($conversation);
            return;
        }

        if (in_array($lowerBody, ['3', 'خط المساعدة', 'طوارئ', 'ارقام', 'أرقام', 'helpline', 'تليفون', 'اتصال'])) {
            $this->handleHelplineInfo($conversation);
            return;
        }

        if (in_array($lowerBody, ['4', 'فعاليات', 'مؤتمرات', 'مؤتمر', 'events', 'حدث'])) {
            $this->handleEventsInfo($conversation);
            return;
        }

        if (in_array($lowerBody, ['5', 'نماذج', 'مطبوعات', 'استمارات', 'طلب ادبيات', 'forms'])) {
            $this->handleFormsInfo($conversation);
            return;
        }

        if (in_array($lowerBody, ['6', 'سوشيال', 'social', 'موقع', 'فيس بوك', 'فيسبوك', 'روابط', 'links'])) {
            $this->handleSocialMediaInfo($conversation);
            return;
        }

        // 7. Option 9: Convention Invitation Retrieval (مسار يجمعنا)
        if (in_array($lowerBody, [
            '9', 'دعوة', 'دعوه', 'دعوة المؤتمر', 'دعوه المؤتمر', 'دعوتي', 'دعوتى',
            'تذكرة', 'تذكره', 'تذكرتي', 'تذكرتى', 'ticket', 'مؤتمر', 'المؤتمر', 'مسار يجمعنا'
        ])) {
            $this->handleCampaign11InvitationLookup($conversation);
            return;
        }

        // Default: Show main interactive menu
        $conversation->update(['current_step' => null, 'step_data' => null]);
        $this->replyAndLog($conversation, $this->getMainMenuText(), 'menu');
    }

    /**
     * Get the formatted main menu text.
     */
    protected function getMainMenuText(): string
    {
        $menu = "أهلاً بك في واتساب زمالة المدمنين المجهولين - مصر 🇪🇬\n"
            . "Welcome to NA Egypt WhatsApp Service\n\n"
            . "الرجاء اختيار رقم الخدمة أو إرسال الكلمة المطلوبة:\n"
            . "1️⃣ *قراءة لليوم فقط* (Just For Today)\n"
            . "2️⃣ *البحث عن اجتماعات التعافي* (Find Meetings)\n"
            . "3️⃣ *أرقام خط المساعدة* (Helpline Numbers)\n"
            . "4️⃣ *الفعاليات والمؤتمرات* (Upcoming Events)\n"
            . "5️⃣ *النماذج الإلكترونية وطلب الأدبيات* (Forms & Literature)\n"
            . "6️⃣ *وسائل التواصل والموقع الرسمي* (Social Media & Website)\n"
            . "7️⃣ *الاشتراك في رسائل لليوم فقط يومياً* (Daily JFT Broadcast)\n";

        if ($this->isCampaign11LookupActive()) {
            $menu .= "9️⃣ *استلام دعوة المؤتمر (مسار يجمعنا)* (Get Conference Invitation)\n";
        }

        $menu .= "0️⃣ *التحدث مع متطوع خط المساعدة* (Talk to Volunteer)\n\n"
            . "🌐 موقعنا الرسمي: https://naegypt.org";

        return $menu;
    }

    /**
     * Handle Just For Today reading request.
     */
    protected function handleJftReading(WhatsAppConversation $conversation): void
    {
        try {
            $reading = $this->jftService->getReading();
            $title = !empty($reading['title']) ? $reading['title'] : 'لليوم فقط';
            $pageDate = !empty($reading['page_date']) ? $reading['page_date'] : Carbon::now('Africa/Cairo')->translatedFormat('j F');
            $thought = $reading['thought'] ?? ($reading['thought_for_the_day'] ?? '');
            $quote = $reading['quote'] ?? '';
            $content = $reading['content'] ?? '';

            if (is_array($content)) {
                $content = implode("\n\n", $content);
            }

            // Clean HTML tags from content
            $cleanContent = strip_tags(str_replace(['<br>', '<br/>', '<p>'], ["\n", "\n", "\n\n"], (string) $content));
            $cleanContent = trim(preg_replace("/\n{3,}/", "\n\n", $cleanContent));

            if (empty($cleanContent)) {
                $cleanContent = "يمكنك متابعة قراءة لليوم فقط كاملة عبر موقعنا الرسمي.";
            }

            // Truncate cleanly for WhatsApp if very long
            if (mb_strlen($cleanContent) > 1500) {
                $cleanContent = mb_substr($cleanContent, 0, 1500) . "...\n\n(لقراءة النص كاملاً يرجى زيارة موقعنا)";
            }

            $message = "📖 *لليوم فقط - {$pageDate}*\n\n"
                . "📌 *{$title}*\n\n";

            if ($quote) {
                $cleanQuote = strip_tags($quote);
                $message .= "💬 _{$cleanQuote}_\n\n";
            }

            $message .= "{$cleanContent}\n\n";

            if ($thought) {
                $cleanThought = strip_tags($thought);
                $message .= "✨ *لليوم فقط:* {$cleanThought}\n\n";
            }

            $message .= "🔗 للقراءة كاملة على الموقع: https://naegypt.org/jft";

            $this->replyAndLog($conversation, $message, 'jft');
        } catch (\Throwable $e) {
            Log::error('[WhatsAppBot] Error fetching JFT: ' . $e->getMessage());
            $this->replyAndLog(
                $conversation,
                "📖 يمكنك متابعة قراءة لليوم فقط عبر موقعنا الرسمي:\nhttps://naegypt.org/jft",
                'jft'
            );
        }
    }

    /**
     * Prompt user to select meeting city or region.
     */
    protected function promptMeetingCitySelection(WhatsAppConversation $conversation): void
    {
        $conversation->update(['current_step' => 'awaiting_city']);

        $message = "🔍 *البحث عن اجتماعات التعافي اليوم*\n\n"
            . "الرجاء اختيار رقم المحافظة أو نوع الاجتماع:\n"
            . "1️⃣ القاهرة (Cairo)\n"
            . "2️⃣ الجيزة (Giza)\n"
            . "3️⃣ الإسكندرية (Alexandria)\n"
            . "4️⃣ اجتماعات عبر الإنترنت (Online Meetings)\n"
            . "5️⃣ باقي المحافظات (Other Governorates)\n"
            . "0️⃣ رجوع للقائمة الرئيسية\n\n"
            . "🔗 دليل الاجتماعات الكامل: https://naegypt.org/meetings";

        $this->replyAndLog($conversation, $message, 'meetings');
    }

    /**
     * Process city choice and return today's meetings.
     */
    protected function handleMeetingCitySelection(WhatsAppConversation $conversation, string $input): void
    {
        if ($input === '0' || $input === 'رجوع' || $input === 'خروج' || $input === 'انهاء' || $input === 'إنهاء') {
            $conversation->update(['current_step' => null, 'step_data' => null]);
            $this->replyAndLog($conversation, $this->getMainMenuText(), 'menu');
            return;
        }

        $todayName = Carbon::now('Africa/Cairo')->format('l'); // Monday, Tuesday...
        $day = Day::where('en_name', $todayName)->first();
        $dayId = $day ? $day->id : null;

        $targetCityName = null;
        $isOnline = false;
        $isOther = false;

        if ($input === '1' || str_contains($input, 'قاهرة') || str_contains($input, 'cairo')) {
            $targetCityName = 'القاهرة';
        } elseif ($input === '2' || str_contains($input, 'جيزة') || str_contains($input, 'giza')) {
            $targetCityName = 'الجيزة';
        } elseif ($input === '3' || str_contains($input, 'إسكندرية') || str_contains($input, 'اسكندرية') || str_contains($input, 'alex')) {
            $targetCityName = 'الإسكندرية';
        } elseif ($input === '4' || str_contains($input, 'اونلاين') || str_contains($input, 'أونلاين') || str_contains($input, 'online')) {
            $isOnline = true;
        } elseif ($input === '5' || str_contains($input, 'باقي') || str_contains($input, 'محافظات') || str_contains($input, 'other')) {
            $isOther = true;
        }

        $conversation->update(['current_step' => null, 'step_data' => null]);

        if ($isOnline) {
            $this->handleOnlineMeetings($conversation, $dayId);
            return;
        }

        $query = Meeting::with(['group.neighborhood.city', 'day'])
            ->where('status', 'active');

        if ($dayId) {
            $query->where('day_id', $dayId);
        }

        if ($targetCityName) {
            $query->whereHas('group.neighborhood.city', function ($q) use ($targetCityName) {
                $q->where('ar_name', 'like', "%{$targetCityName}%")
                    ->orWhere('en_name', 'like', "%{$targetCityName}%");
            });
        } elseif ($isOther) {
            $query->whereHas('group.neighborhood.city', function ($q) {
                $q->where('ar_name', 'not like', '%القاهرة%')
                    ->where('ar_name', 'not like', '%الجيزة%')
                    ->where('ar_name', 'not like', '%الإسكندرية%')
                    ->where('ar_name', 'not like', '%اسكندرية%');
            });
        }

        $meetings = $query->take(8)->get();

        if ($meetings->isEmpty()) {
            $cityNameLabel = $targetCityName ?: ($isOther ? 'باقي المحافظات' : 'المحافظة المختارة');
            $this->replyAndLog(
                $conversation,
                "لم يتم العثور على اجتماعات مسجلة اليوم في {$cityNameLabel}.\n\nيمكنك البحث في دليل الاجتماعات المحدث بالكامل عبر الرابط:\nhttps://naegypt.org/meetings",
                'meetings'
            );
            return;
        }

        $cityNameLabel = $targetCityName ?: ($isOther ? 'باقي المحافظات' : 'الاجتماعات اليومية');
        $message = "📍 *اجتماعات التعافي اليوم في {$cityNameLabel}*\n\n";

        foreach ($meetings as $idx => $m) {
            $num = $idx + 1;
            $groupName = $m->group ? ($m->group->ar_name ?: $m->group->en_name) : 'اجتماع';
            $neighborhood = $m->group && $m->group->neighborhood ? $m->group->neighborhood->ar_name : '';
            $time = $m->start_time ? substr($m->start_time, 0, 5) : '';
            $address = $m->group ? ($m->group->ar_address ?: $m->group->en_address ?: $m->group->location) : '';

            $message .= "{$num}️⃣ *مجموعة {$groupName}* ({$neighborhood})\n";
            $message .= "⏰ الميعاد: {$time}\n";
            if ($address) {
                $message .= "🏢 العنوان: {$address}\n";
            }
            if ($m->group && $m->group->location && (str_starts_with($m->group->location, 'http://') || str_starts_with($m->group->location, 'https://'))) {
                $message .= "🗺️ الموقع: {$m->group->location}\n";
            }
            $message .= "───────────────\n";
        }

        $message .= "\n🔗 لدليل الاجتماعات الكامل والخريطة التفاعلية:\nhttps://naegypt.org/meetings";

        $this->replyAndLog($conversation, $message, 'meetings');
    }

    /**
     * Handle online meetings lookup.
     */
    protected function handleOnlineMeetings(WhatsAppConversation $conversation, ?int $dayId): void
    {
        $onlineMeetings = Meeting::with(['directOnlineGroup', 'day'])
            ->whereNotNull('direct_online_group_id')
            ->where('status', 'active');

        if ($dayId) {
            $onlineMeetings->where('day_id', $dayId);
        }

        $results = $onlineMeetings->take(6)->get();

        if ($results->isEmpty()) {
            $this->replyAndLog(
                $conversation,
                "🌐 يمكنك متابعة الاجتماعات الأونلاين عبر الإنترنت من خلال الدليل المحدث:\nhttps://naegypt.org/meetings?type=online",
                'meetings'
            );
            return;
        }

        $message = "💻 *اجتماعات الأونلاين اليوم (عبر الإنترنت)*\n\n";
        foreach ($results as $idx => $m) {
            $num = $idx + 1;
            $name = $m->directOnlineGroup ? $m->directOnlineGroup->name : 'اجتماع أونلاين';
            $time = $m->start_time ? substr($m->start_time, 0, 5) : '';
            $link = $m->directOnlineGroup ? $m->directOnlineGroup->link : '';

            $message .= "{$num}️⃣ *{$name}*\n⏰ الميعاد: {$time}\n";
            if ($link) {
                $message .= "🔗 رابط الحضور: {$link}\n";
            }
            $message .= "───────────────\n";
        }

        $message .= "\n🌐 لمزيد من اجتماعات الأونلاين:\nhttps://naegypt.org/meetings?type=online";

        $this->replyAndLog($conversation, $message, 'meetings');
    }

    /**
     * Send Helpline phone numbers and emergency contacts.
     */
    protected function handleHelplineInfo(WhatsAppConversation $conversation): void
    {
        $message = "📞 *أرقام خط المساعدة لزمالة المدمنين المجهولين - مصر*\n"
            . "متاح للمساعدة والرد على جميع الاستفسارات:\n\n"
            . "📞 *خطوط المساعدة (Regional Helplines):*\n"
            . "📱 +201006979198\n"
            . "📱 +201060933888\n\n"
            . "📞 *خط مساعدة الاسكندرية (Alexandria Helpline):*\n"
            . "📱 +201503884411\n\n"
            . "💬 للتحدث مع أحد متطوعي الخدمة عبر واتساب الآن، أرسل رقم: *0* أو كلمة: *متطوع*\n\n"
            . "🔗 صفحة التواصل بالموقع: https://naegypt.org/contactus";

        $this->replyAndLog($conversation, $message, 'helpline');
    }

    /**
     * Send upcoming events.
     */
    protected function handleEventsInfo(WhatsAppConversation $conversation): void
    {
        $events = CalendarEvent::where('end', '>=', Carbon::now())
            ->orderBy('start')
            ->take(4)
            ->get();

        if ($events->isEmpty()) {
            $this->replyAndLog(
                $conversation,
                "📅 لا توجد فعاليات أو مؤتمرات قادمة مسجلة حالياً.\n\n🔗 يمكنك دائماً متابعة جدول الفعاليات والمؤتمرات المحدث عبر الموقع الرسمي:\nhttps://naegypt.org/events",
                'events'
            );
            return;
        }

        $message = "🎉 *الفعاليات والمؤتمرات القادمة لزمالة المدمنين المجهولين - مصر:*\n\n";
        foreach ($events as $idx => $ev) {
            $num = $idx + 1;
            $startStr = $ev->start ? $ev->start->translatedFormat('j F Y - h:i A') : '';
            $message .= "{$num}️⃣ *{$ev->title}*\n";
            $message .= "🗓️ التاريخ: {$startStr}\n";
            if ($ev->location) {
                $message .= "📍 المكان: {$ev->location}\n";
            }
            if ($ev->description) {
                $message .= "📝 " . mb_substr(strip_tags($ev->description), 0, 100) . "...\n";
            }
            $message .= "───────────────\n";
        }

        $message .= "\n🔗 لمتابعة تفاصيل جدول الفعاليات والمؤتمرات بالكامل عبر الموقع:\nhttps://naegypt.org/events";
        $this->replyAndLog($conversation, $message, 'events');
    }

    /**
     * Send links to public forms and literature requests.
     */
    protected function handleFormsInfo(WhatsAppConversation $conversation): void
    {
        $message = "📋 *النماذج الإلكترونية وطلب المطبوعات لزمالة المدمنين المجهولين - مصر:*\n\n"
            . "1️⃣ *طلب التعاون مع الزمالة (للمؤسسات والجهات الطبية والإعلامية):*\n"
            . "🔗 https://naegypt.org/cooperation\n\n"
            . "2️⃣ *استمارة طلب الأدبيات والمطبوعات (للمجموعات واللجان الخدمية):*\n"
            . "🔗 https://naegypt.org/literature-requests/create\n\n"
            . "3️⃣ *قراءة وتحميل الأدبيات المعتمدة مجاناً:*\n"
            . "🔗 https://naegypt.org/literature\n\n"
            . "4️⃣ *استمارة التواصل العامة (اتصل بنا):*\n"
            . "🔗 https://naegypt.org/contactus";

        $this->replyAndLog($conversation, $message, 'forms');
    }

    /**
     * Send social media and official links.
     */
    protected function handleSocialMediaInfo(WhatsAppConversation $conversation): void
    {
        $message = "🌐 *وسائل التواصل الرسمي لزمالة المدمنين المجهولين - مصر:*\n\n"
            . "💻 *الموقع الإلكتروني الرسمي:*\n"
            . "https://naegypt.org\n\n"
            . "📘 *صفحة فيسبوك الرسمية (Facebook):*\n"
            . "https://www.facebook.com/naegypt.org\n\n"
            . "📺 *قناة يوتيوب (YouTube):*\n"
            . "https://www.youtube.com/@naegypt\n\n"
            . "✉️ *البريد الإلكتروني العام:*\n"
            . "info@naegypt.org";

        $this->replyAndLog($conversation, $message, 'social');
    }

    /**
     * Check if the convention invitation lookup (Option 9) window is currently active.
     */
    public function isCampaign11LookupActive(): bool
    {
        $expiresAt = config('whatsapp.convention_lookup_expires_at', '2026-10-10 23:59:59');
        try {
            return Carbon::now('Africa/Cairo')->lessThanOrEqualTo(Carbon::parse($expiresAt, 'Africa/Cairo'));
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * Handle on-demand retrieval of convention invitation for Campaign #11 / attendee dataset.
     */
    protected function handleCampaign11InvitationLookup(WhatsAppConversation $conversation): void
    {
        if (!$this->isCampaign11LookupActive()) {
            $this->replyAndLog(
                $conversation,
                "انتهت فترة الاستلام الآلي لدعوات المؤتمر عبر خدمة الرد الآلي 🕊️\n\nللحصول على المساعدة بخصوص دعوة مؤتمر \"مسار يجمعنا\"، يرجى إرسال رقم (0) للتواصل مع أحد متطوعي الخدمة، أو زيارة موقعنا الرسمي: https://naegypt.org",
                'campaign_11_invitation'
            );
            return;
        }

        $logId = (int) config('whatsapp.campaign_11_log_id', 11);

        // Prefer active convention_attendees dataset, otherwise fallback to configured Campaign 11
        $log = WhatsAppBroadcastLog::where('channel', 'convention_attendees')->latest()->first()
            ?: WhatsAppBroadcastLog::find($logId);

        if (!$log || empty($log->metadata['recipients'])) {
            Log::warning("[WhatsAppBot] Convention attendee campaign #{$logId} has no recipients data.");
            $this->replyAndLog(
                $conversation,
                "عفواً، قاعدة بيانات دعوات المؤتمر غير متاحة حالياً، يرجى إرسال رقم (0) للتواصل مع متطوع من فريق التنظيم 🤝",
                'campaign_11_invitation'
            );
            return;
        }

        $recipients = $log->metadata['recipients'];
        $recipient = $this->findCampaignRecipientByPhone($conversation->phone ?: $conversation->jid, $recipients);

        if (!$recipient) {
            $userDisplayPhone = $conversation->phone ?: $this->client->normalizePhone($conversation->jid);
            $notFoundMsg = "عفواً، لم نتمكن من العثور على دعوة مؤتمر مسجلة مرتبطة بهذا الرقم ({$userDisplayPhone}) 🔍\n\n"
                . "📌 يرجى التأكد من مراسلتنا من نفس رقم الهاتف المسجل به في استمارة المؤتمر، أو إرسال رقم *0* للتحدث مع أحد متطوعي الخدمة لمساعدتك.\n\n"
                . "🌐 رابط موقع المؤتمر: https://egypt30convention.org";

            $this->replyAndLog($conversation, $notFoundMsg, 'campaign_11_invitation_not_found');
            return;
        }

        $attendeeName = !empty($recipient['name']) ? $recipient['name'] : 'عضو الزمالة العزيز';
        $messageText = trim($recipient['message'] ?? '');

        // Replace placeholders {name}, {phone}, {الاسم}, {الهاتف}
        $messageText = str_replace(
            ['{name}', '{phone}', '{الاسم}', '{الهاتف}'],
            [$attendeeName, $recipient['phone'] ?? '', $attendeeName, $recipient['phone'] ?? ''],
            $messageText
        );

        if (empty($messageText)) {
            $messageText = "أهلاً بك {$attendeeName} 🌸\nتم تأكيد تسجيلك في مؤتمر \"مسار يجمعنا\".\nوجودك يكمل الصورة.. معًا يصبح للمسار معنى.";
        }

        $this->replyAndLog($conversation, $messageText, 'campaign_11_invitation');

        // Track claimed invitation in campaign metadata
        try {
            $meta = $log->metadata ?? [];
            $meta['claimed_invitations'] = $meta['claimed_invitations'] ?? [];
            $claimedKey = $this->client->normalizePhone($recipient['phone'] ?? $conversation->phone);
            $meta['claimed_invitations'][$claimedKey] = Carbon::now()->toIso8601String();
            $log->update(['metadata' => $meta]);
        } catch (\Throwable $e) {
            Log::warning("[WhatsAppBot] Failed to update claimed_invitations metadata: " . $e->getMessage());
        }
    }

    /**
     * Cross-reference a user phone or JID against convention attendees with zero-exception tolerance.
     */
    public function findCampaignRecipientByPhone(string $userPhoneOrJid, array $recipients): ?array
    {
        $userPhone = $this->client->normalizePhone($userPhoneOrJid);
        if (empty($userPhone)) {
            return null;
        }

        // Strip leading country code or zero for Egyptian mobile suffix (e.g. '20' or '0')
        $userEgSuffix = preg_replace('/^(20|0)/', '', $userPhone);

        // 1. Exact normalized digits match
        foreach ($recipients as $recipient) {
            $recPhone = $this->client->normalizePhone($recipient['phone'] ?? '');
            if ($recPhone === $userPhone) {
                return $recipient;
            }
        }

        // 2. Egyptian 10-digit mobile core match (e.g. 10xxxxxxxx, 11xxxxxxxx, 12xxxxxxxx, 15xxxxxxxx)
        if (strlen($userEgSuffix) >= 9) {
            foreach ($recipients as $recipient) {
                $recPhone = $this->client->normalizePhone($recipient['phone'] ?? '');
                $recEgSuffix = preg_replace('/^(20|0)/', '', $recPhone);
                if (!empty($recEgSuffix) && $recEgSuffix === $userEgSuffix) {
                    return $recipient;
                }
            }
        }

        // 3. Right-aligned digit suffix match for minimum 8 digits
        $minSuffixLen = min(9, strlen($userPhone));
        if ($minSuffixLen >= 8) {
            $userTail = substr($userPhone, -$minSuffixLen);
            foreach ($recipients as $recipient) {
                $recPhone = $this->client->normalizePhone($recipient['phone'] ?? '');
                if (strlen($recPhone) >= $minSuffixLen && substr($recPhone, -$minSuffixLen) === $userTail) {
                    return $recipient;
                }
            }
        }

        return null;
    }

    /**
     * Normalize Eastern Arabic-Indic numerals (٠-٩) to standard ASCII digits (0-9).
     */
    public function normalizeArabicIndicDigits(string $str): string
    {
        $arabicIndic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $standard = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($arabicIndic, $standard, $str);
    }

    /**
     * Send response via WhatsAppClient and log outbound message in database.
     */
    protected function replyAndLog(WhatsAppConversation $conversation, string $text, string $category, ?string $deviceId = null): void
    {
        $response = $this->client->sendTextMessage($conversation->jid, $text, $deviceId);

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'message_id' => $response['data']['results']['message_id'] ?? null,
            'direction' => 'outgoing',
            'sender_type' => 'bot',
            'category' => $category,
            'message_type' => 'text',
            'body' => $text,
            'status' => ($response['success'] ?? false) ? 'sent' : 'failed',
            'raw_payload' => $response,
        ]);
    }
}
