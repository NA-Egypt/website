<?php

namespace App\Services\WhatsApp;

use App\Models\CalendarEvent;
use App\Models\City;
use App\Models\Day;
use App\Models\DirectOnlineGroup;
use App\Models\Meeting;
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
        $senderJid = $payload['sender'] ?? $payload['from'] ?? $payload['phone'] ?? null;
        $body = trim((string) ($payload['message'] ?? $payload['text'] ?? $payload['body'] ?? ''));
        $messageId = $payload['message_id'] ?? $payload['id'] ?? null;
        $pushName = $payload['name'] ?? $payload['push_name'] ?? null;

        if (empty($senderJid)) {
            Log::warning('[WhatsAppBot] Ignored webhook payload without sender: ' . json_encode($payload));
            return;
        }

        $phone = $this->client->normalizePhone($senderJid);
        $formattedJid = $this->client->formatJid($senderJid);

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

        $lowerBody = mb_strtolower($body);

        // 1. Check for command to resume bot mode from live agent takeover
        if (in_array($lowerBody, ['إنهاء المحادثة', 'انهاء المحادثة', 'انهاء', 'إنهاء', 'bot', 'الروبوت', 'خروج'])) {
            $conversation->disableLiveAgent();
            $this->replyAndLog(
                $conversation,
                "تم إعادة تفعيل الرد الآلي بنجاح 🤖\nشكراً لتواصلك مع زمالة المدمنين المجهولين في مصر.\n\n" . $this->getMainMenuText(),
                'menu'
            );
            return;
        }

        // 2. Check if Live Agent Mode is actively suppressing the bot
        if ($conversation->isLiveAgentActive()) {
            Log::info("[WhatsAppBot] Suppressed autoreply for {$phone}: Live Agent active until {$conversation->live_agent_until}");
            return;
        }

        // 3. Check for Live Agent Request
        if (in_array($lowerBody, ['0', 'متطوع', 'تحدث مع متطوع', 'تحدث مع شخص', 'agent', 'volunteer', 'مساعدة'])) {
            $conversation->enableLiveAgent();
            $this->replyAndLog(
                $conversation,
                "تم تحويل محادثتك إلى أحد متطوعي خط المساعدة 🤝\n\nسيتواصل معك متطوع في أقرب وقت ممكن بمشيئة الله.\n(للعودة إلى الرد الآلي في أي وقت، أرسل كلمة: *انهاء*)",
                'helpline'
            );
            return;
        }

        // 4. Check for Broadcast Subscription commands
        if (in_array($lowerBody, ['7', 'اشتراك', 'اشتراك يومي', 'subscribe', 'اشتراك فقط لليوم'])) {
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
                "تم اشتراكك بنجاح في خدمة رسائل \"فقط لليوم\" اليومية 🌅\n\nستصلك القراءة اليومية كل صباح في تمام الساعة 7:00 بتوقيت القاهرة.\n(لإلغاء الاشتراك في أي وقت، أرسل: *الغاء الاشتراك*)",
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
                "تم إلغاء اشتراكك من خدمة الرسائل اليومية بنجاح 🕊️\nيمكنك إعادة الاشتراك في أي وقت بإرسال كلمة: *اشتراك*",
                'subscription'
            );
            return;
        }

        // 5. Handle Multi-Step City Selection for Meetings
        if ($conversation->current_step === 'awaiting_city') {
            $this->handleMeetingCitySelection($conversation, $lowerBody);
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

        // Default: Show main interactive menu
        $conversation->update(['current_step' => null, 'step_data' => null]);
        $this->replyAndLog($conversation, $this->getMainMenuText(), 'menu');
    }

    /**
     * Get the formatted main menu text.
     */
    protected function getMainMenuText(): string
    {
        return "أهلاً بك في خدمة واتساب زمالة المدمنين المجهولين - مصر 🇪🇬\n"
            . "Welcome to Narcotics Anonymous Egypt WhatsApp Service\n\n"
            . "الرجاء اختيار رقم الخدمة أو إرسال الكلمة المطلوبة:\n"
            . "1️⃣ *قراءة فقط لليوم* (Just For Today)\n"
            . "2️⃣ *البحث عن اجتماعات التعافي* (Find Meetings)\n"
            . "3️⃣ *أرقام خط المساعدة والتواصل* (Helpline Numbers)\n"
            . "4️⃣ *الفعاليات والمؤتمرات القادمة* (Upcoming Events)\n"
            . "5️⃣ *النماذج الإلكترونية وطلب المطبوعات* (Forms & Literature)\n"
            . "6️⃣ *وسائل التواصل الاجتماعي والموقع* (Social Media & Website)\n"
            . "7️⃣ *الاشتراك في قراءة فقط لليوم يومياً* (Daily JFT Broadcast)\n"
            . "0️⃣ *التحدث مع متطوع من خط المساعدة* (Talk to Volunteer)\n\n"
            . "🌐 موقعنا الرسمي: https://naegypt.org";
    }

    /**
     * Handle Just For Today reading request.
     */
    protected function handleJftReading(WhatsAppConversation $conversation): void
    {
        try {
            $reading = $this->jftService->getReading();
            $title = !empty($reading['title']) ? $reading['title'] : 'فقط لليوم';
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
                $cleanContent = "يمكنك متابعة قراءة فقط لليوم كاملة عبر موقعنا الرسمي.";
            }

            // Truncate cleanly for WhatsApp if very long
            if (mb_strlen($cleanContent) > 1500) {
                $cleanContent = mb_substr($cleanContent, 0, 1500) . "...\n\n(لقراءة النص كاملاً يرجى زيارة موقعنا)";
            }

            $message = "📖 *فقط لليوم - {$pageDate}*\n\n"
                . "📌 *{$title}*\n\n";

            if ($quote) {
                $cleanQuote = strip_tags($quote);
                $message .= "💬 _{$cleanQuote}_\n\n";
            }

            $message .= "{$cleanContent}\n\n";

            if ($thought) {
                $cleanThought = strip_tags($thought);
                $message .= "✨ *تذكرة اليوم:* {$cleanThought}\n\n";
            }

            $message .= "🔗 للقراءة كاملة على الموقع: https://naegypt.org/jft";

            $this->replyAndLog($conversation, $message, 'jft');
        } catch (\Throwable $e) {
            Log::error('[WhatsAppBot] Error fetching JFT: ' . $e->getMessage());
            $this->replyAndLog(
                $conversation,
                "📖 يمكنك متابعة قراءة فقط لليوم عبر موقعنا الرسمي:\nhttps://naegypt.org/jft",
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
            . "الرجاء اختيار رقم المحافظة أو النوع:\n"
            . "1️⃣ القاهرة (Cairo)\n"
            . "2️⃣ الجيزة (Giza)\n"
            . "3️⃣ الإسكندرية (Alexandria)\n"
            . "4️⃣ اجتماعات أونلاين عبر الإنترنت (Online Meetings)\n"
            . "5️⃣ باقي المحافظات (Other Governorates)\n"
            . "0️⃣ رجوع للقائمة الرئيسية\n\n"
            . "🔗 دليل الاجتماعات الكامل على الموقع: https://naegypt.org/meetings";

        $this->replyAndLog($conversation, $message, 'meetings');
    }

    /**
     * Process city choice and return today's meetings.
     */
    protected function handleMeetingCitySelection(WhatsAppConversation $conversation, string $input): void
    {
        if ($input === '0' || $input === 'رجوع') {
            $conversation->update(['current_step' => null]);
            $this->replyAndLog($conversation, $this->getMainMenuText(), 'menu');
            return;
        }

        $todayName = Carbon::now('Africa/Cairo')->format('l'); // Monday, Tuesday...
        $day = Day::where('name', $todayName)->first();
        $dayId = $day ? $day->id : null;

        $targetCityName = null;
        $isOnline = false;

        if ($input === '1' || str_contains($input, 'قاهرة') || str_contains($input, 'cairo')) {
            $targetCityName = 'القاهرة';
        } elseif ($input === '2' || str_contains($input, 'جيزة') || str_contains($input, 'giza')) {
            $targetCityName = 'الجيزة';
        } elseif ($input === '3' || str_contains($input, 'إسكندرية') || str_contains($input, 'اسكندرية') || str_contains($input, 'alex')) {
            $targetCityName = 'الإسكندرية';
        } elseif ($input === '4' || str_contains($input, 'اونلاين') || str_contains($input, 'أونلاين') || str_contains($input, 'online')) {
            $isOnline = true;
        }

        $conversation->update(['current_step' => null]);

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
        }

        $meetings = $query->take(8)->get();

        if ($meetings->isEmpty()) {
            $cityNameLabel = $targetCityName ?: 'المحافظة المختارة';
            $this->replyAndLog(
                $conversation,
                "لم يتم العثور على اجتماعات مسجلة اليوم في {$cityNameLabel}.\n\nيمكنك البحث في دليل الاجتماعات المحدث بالكامل عبر الرابط:\nhttps://naegypt.org/meetings",
                'meetings'
            );
            return;
        }

        $cityNameLabel = $targetCityName ?: 'الاجتماعات اليومية';
        $message = "📍 *اجتماعات اليوم في {$cityNameLabel}*\n\n";

        foreach ($meetings as $idx => $m) {
            $num = $idx + 1;
            $groupName = $m->group ? ($m->group->ar_name ?: $m->group->en_name) : 'اجتماع';
            $neighborhood = $m->group && $m->group->neighborhood ? $m->group->neighborhood->ar_name : '';
            $time = $m->start_time ? substr($m->start_time, 0, 5) : '';
            $address = $m->group ? ($m->group->ar_address ?: $m->group->en_address ?: $m->group->location) : '';

            $message .= "{$num}️⃣ *{$groupName}* ({$neighborhood})\n";
            $message .= "⏰ الميعاد: {$time}\n";
            if ($address) {
                $message .= "🏢 العنوان: {$address}\n";
            }
            if ($m->group && $m->group->location && (str_starts_with($m->group->location, 'http://') || str_starts_with($m->group->location, 'https://'))) {
                $message .= "🗺️ الموقع: {$m->group->location}\n";
            }
            $message .= "───────────────\n";
        }

        $message .= "\n🔗 لمشاهدة جميع الاجتماعات والخريطة التفاعلية:\nhttps://naegypt.org/meetings";

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
        $message = "📞 *أرقام خط المساعدة لزمالة المدمنين المجهولين في مصر*\n"
            . "سرية تامة - متاح للمساعدة والرد على استفساراتكم:\n\n"
            . "👨 *خط مساعدة الرجال (Men Helpline):*\n"
            . "📱 +201006979198\n"
            . "📱 +201060933888\n\n"
            . "👩 *خط مساعدة السيدات (Women Helpline):*\n"
            . "📱 +201503884411\n\n"
            . "💬 يمكنك أيضاً طلب التحدث مع أحد المتطوعين عبر واتساب الآن بإرسال كلمة: *متطوع*\n\n"
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
                "📅 لا توجد فعاليات قادمة مسجلة حالياً.\nيمكنك متابعة جدول الفعاليات والمؤتمرات عبر الموقع:\nhttps://naegypt.org/events",
                'events'
            );
            return;
        }

        $message = "🎉 *الفعاليات والمؤتمرات القادمة لزمالة NA مصر:*\n\n";
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

        $message .= "\n🔗 تفاصيل الفعاليات كاملة: https://naegypt.org/events";
        $this->replyAndLog($conversation, $message, 'events');
    }

    /**
     * Send links to public forms and literature requests.
     */
    protected function handleFormsInfo(WhatsAppConversation $conversation): void
    {
        $message = "📋 *النماذج الإلكترونية وطلب المطبوعات:*\n\n"
            . "1️⃣ *طلب التعاون مع الزمالة (للمؤسسات والجهات الطبية والإعلامية):*\n"
            . "🔗 https://naegypt.org/cooperation\n\n"
            . "2️⃣ *استمارة طلب الأدبيات والمطبوعات للمجموعات واللجان:*\n"
            . "🔗 https://naegypt.org/literature-requests/create\n\n"
            . "3️⃣ *قراءة وتحميل الأدبيات المعتمدة مجاناً:*\n"
            . "🔗 https://naegypt.org/literature\n\n"
            . "4️⃣ *استمارة اتصل بنا العامة:*\n"
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
     * Send response via WhatsAppClient and log outbound message in database.
     */
    protected function replyAndLog(WhatsAppConversation $conversation, string $text, string $category): void
    {
        $response = $this->client->sendTextMessage($conversation->jid, $text);

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
