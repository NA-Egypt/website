<?php

namespace Tests\Feature;

use App\Services\WhatsApp\WhatsAppClient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class WhatsAppDevModeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dev_mode_blocks_outbound_to_non_whitelisted_numbers(): void
    {
        Config::set('whatsapp.dev_mode', true);
        Config::set('whatsapp.dev_whitelist', ['201006979198', '201060933888']);

        $client = new WhatsAppClient();

        // Whitelisted numbers should be allowed
        $this->assertTrue($client->isRecipientAllowed('201006979198'));
        $this->assertTrue($client->isRecipientAllowed('201060933888@s.whatsapp.net'));

        // Non-whitelisted number should be blocked
        $this->assertFalse($client->isRecipientAllowed('201199999999'));
        $this->assertFalse($client->isRecipientAllowed('+201200000000@s.whatsapp.net'));

        // sendTextMessage skips non-whitelisted safely
        $result = $client->sendTextMessage('201199999999', 'Should not send');
        $this->assertTrue($result['success']);
        $this->assertTrue($result['dev_skipped']);
    }

    public function test_production_mode_allows_all_recipients(): void
    {
        Config::set('whatsapp.dev_mode', false);
        $client = new WhatsAppClient();

        $this->assertTrue($client->isRecipientAllowed('201199999999'));
    }
}
