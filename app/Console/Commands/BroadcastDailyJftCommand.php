<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppBroadcast;
use App\Services\JftService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BroadcastDailyJftCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'whatsapp:broadcast-jft';

    /**
     * The console command description.
     */
    protected $description = 'Broadcast today\'s Just For Today (فقط لليوم) reading to active WhatsApp subscribers';

    /**
     * Execute the console command.
     */
    public function handle(JftService $jftService): int
    {
        $this->info('Fetching today\'s Just For Today reading...');

        $reading = $jftService->getReading();
        $title = $reading['title'] ?? 'فقط لليوم';
        $pageDate = $reading['page_date'] ?? Carbon::now('Africa/Cairo')->translatedFormat('j F');
        $thought = $reading['thought'] ?? ($reading['thought_for_the_day'] ?? '');
        $quote = $reading['quote'] ?? '';
        $content = $reading['content'] ?? '';

        if (is_array($content)) {
            $content = implode("\n\n", $content);
        }

        $cleanContent = strip_tags(str_replace(['<br>', '<br/>', '<p>'], ["\n", "\n", "\n\n"], (string) $content));
        $cleanContent = trim(preg_replace("/\n{3,}/", "\n\n", $cleanContent));

        if (mb_strlen($cleanContent) > 1400) {
            $cleanContent = mb_substr($cleanContent, 0, 1400) . "...\n\n(لقراءة الموضوع كاملاً يرجى زيارة موقعنا)";
        }

        $message = "🌅 *رسالة فقط لليوم - {$pageDate}*\n"
            . "زمالة المدمنين المجهولين - مصر 🇪🇬\n\n"
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

        $message .= "🔗 للقراءة كاملة عبر الموقع: https://naegypt.org/jft\n"
            . "🕊️ لإلغاء الاشتراك أرسل: *الغاء الاشتراك*";

        $broadcastTitle = "Just For Today - {$pageDate}";

        $this->info("Dispatching broadcast job: {$broadcastTitle}");
        SendWhatsAppBroadcast::dispatch('jft', $broadcastTitle, $message);

        $this->info('Broadcast job dispatched successfully.');
        return Command::SUCCESS;
    }
}
