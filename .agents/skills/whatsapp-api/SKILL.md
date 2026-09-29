---
name: whatsapp-api
description: Comprehensive reference, integration guide, and operational manual for the NA-Egypt WhatsApp automation microservice (aldinokemal/go-whatsapp-web-multidevice v3.4.0), Laravel WhatsApp services, webhook routing, bot state machine, live-agent takeover, anti-ban broadcasts, and Docker operations.
---

# WhatsApp Automation API & Microservice Integration Skill

This skill provides an end-to-end guide and technical reference for working with the NA-Egypt WhatsApp Automation architecture. It covers both the upstream Go WhatsApp multi-device REST microservice (`aldinokemal/go-whatsapp-web-multidevice`) and the downstream Laravel backend integration layer (`app/Services/WhatsApp/`, background jobs, webhook receivers, bot state machine, live-agent handover, and admin reporting).

---

## 1. System Architecture Overview

```
                                      +------------------------------------+
                                      |          WhatsApp Servers          |
                                      +-----------------+------------------+
                                                        |
                                           WebSocket / Multi-Device
                                                        |
                                                        v
+-------------------------------------------------------------------------------------------------------+
| Docker Host (127.0.0.1)                                                                               |
|                                                                                                       |
|  [naegypt_whatsapp Container] (aldinokemal/go-whatsapp-web-multidevice:latest v3.4.0)                |
|  - Host Port: 127.0.0.1:3000                                                                          |
|  - Volume: ./storage/app/whatsapp-data:/app/storages                                                  |
|  - Auth: Basic Auth (naegypt:WHATSAPP_API_KEY)                                                        |
|  - Active Device: default                                                                             |
+---------------------------------------------------+---------------------------------------------------+
                               | Outbound REST APIs | Inbound Webhook Event Callback
                               | (X-Device-Id: ...) | (POST /api/v1/whatsapp/webhook)
                               v                    v
+-------------------------------------------------------------------------------------------------------+
| NA-Egypt Laravel Backend (app/Services/WhatsApp/)                                                     |
|                                                                                                       |
|  - WhatsAppClient: HTTP wrapper with basic auth, device_id header, auto-provisioning & dev guardrails |
|  - WhatsAppWebhookController: Validates secret & immediately queues payload in < 50ms                 |
|  - ProcessIncomingWhatsAppMessage (Queue Job): Unpacks payload, logs message, invokes bot/agent       |
|  - WhatsAppBotService: Multi-step interactive menu, keyword routing (JFT, meetings, helpline, etc.)   |
|  - WhatsAppReportService: Aggregates KPIs, volume trends, response times & CSV streaming               |
|  - SendWhatsAppBroadcast (Queue Job): Dispatches JFT with 2-5s randomized anti-ban jitter delays      |
|  - BroadcastDailyJftCommand: Console command scheduled daily at 07:00 Cairo time                      |
+-------------------------------------------------------------------------------------------------------+
```

---

## 2. Microservice REST API (v3.4.0)

The microservice runs inside Docker on `http://127.0.0.1:3000`.

### Authentication & Required Headers
- **Basic Auth:** `Authorization: Basic <base64("naegypt:" . $apiKey)>`
- **Device Header:** `X-Device-Id: default` (or query param `?device_id=default`)
- **Content-Type:** `application/json`
- **Accept:** `application/json`

### Key Endpoints

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/devices` | List registered devices |
| `POST` | `/devices` | Create a new device slot `{"device_id":"default"}` |
| `DELETE` | `/devices/{id}` | Delete a device slot and clear its session |
| `GET` | `/devices/{id}/status` | Check if device is connected and logged in |
| `GET` | `/devices/{id}/login` | Generate fresh 30-second pairing QR code |
| `POST` | `/devices/{id}/login/code` | Request 8-character pairing code by phone `?phone=2010...` |
| `POST` | `/devices/{id}/reconnect` | Trigger session reconnect |
| `POST` | `/devices/{id}/logout` | Disconnect / log out WhatsApp web session |
| `GET` | `/devices/{id}/webhook` | View active device webhook config |
| `PATCH` | `/devices/{id}/webhook` | Update device webhook URL `{"webhook_url":"..."}` |
| `POST` | `/send/message` | Send text message `{"phone":"2010...","message":"..."}` |
| `POST` | `/send/image` | Send image with caption `{"phone":"...","image":"...","caption":"..."}` |

> For a full list of all available microservice endpoints (including files, documents, locations, passkeys, and contact queries), consult [microservice-api.md](file:///var/www/new/.agents/skills/whatsapp-api/references/microservice-api.md).

---

## 3. Laravel Service Layer

### `App\Services\WhatsApp\WhatsAppClient`
Injectable client managing microservice communication.

```php
use App\Services\WhatsApp\WhatsAppClient;

$client = app(WhatsAppClient::class);

// 1. Check health & connection
$status = $client->getDeviceStatus();
// Returns: ['success' => true, 'connected' => true/false, 'data' => [...]]

// 2. Fetch login QR code (returns base64 data URI data:image/png;base64,... ready for <img>)
$qr = $client->getLoginQrCode();
// Returns: ['success' => true, 'qr_image' => 'data:image/...', 'qr_duration' => 30]

// 3. Send text message (with dev whitelist guardrails)
$res = $client->sendTextMessage('201001234567', 'Hello from NA Egypt');
// Returns: ['success' => true, 'data' => [...]]

// 4. Send image message
$res = $client->sendImageMessage('201001234567', 'https://naegypt.org/logo.png', 'NA Egypt Logo');

// 5. Session control
$client->reconnectDevice();
$client->logoutDevice();
```

---

## 4. Development & Staging Sandbox Guardrails (`egyptna.org`)

To prevent accidental broadcasts or testing messages to real fellowship members during staging or development:

1. **Detection:**
   - Active when `config('whatsapp.dev_mode') === true`, OR
   - When the host request matches `egyptna.org` or `*.egyptna.org`.
2. **Whitelist Filter:**
   - Evaluated in `WhatsAppClient::isRecipientAllowed($phoneOrJid)`.
   - Outbound messages to numbers not listed in `WHATSAPP_DEV_WHITELIST` (`config('whatsapp.dev_whitelist')`) are blocked and logged.
   - Returns `['success' => true, 'dev_skipped' => true, 'message' => 'Skipped in dev mode...']`.

---

## 5. Webhook Specification & Bot State Machine

### Webhook Endpoint: `POST /api/v1/whatsapp/webhook`
- Public endpoint (bypasses CSRF).
- Optional secret verification via header `X-Webhook-Secret` or payload key `secret` matching `config('whatsapp.webhook_secret')`.
- Responds immediately with HTTP `200` (`{"status":"queued"}`) and dispatches `ProcessIncomingWhatsAppMessage` job to the async queue.

### Bot Logic (`App\Services\WhatsApp\WhatsAppBotService`)
- **Live Agent Mode:** If a volunteer is chatting, or if the user typed `0` / `متطوع`, the automated bot pauses for 30 minutes (`WHATSAPP_LIVE_AGENT_TIMEOUT`).
- **Resuming Bot:** The user or volunteer can type `انهاء` or click "Resume Bot" in the admin inbox.
- **Interactive Keywords:**
  - `1`, `jft`, `فقط لليوم`: Daily Just For Today reading from `JftService`.
  - `2`, `اجتماعات`, `meetings`: Two-step city lookup (Cairo, Giza, Alexandria, Online).
  - `3`, `خط المساعدة`, `helpline`: Official helpline hotlines.
  - `4`, `فعاليات`, `events`: Upcoming events from `CalendarEvent`.
  - `5`, `نماذج`, `forms`: Links to Cooperation, Literature, and Contact forms.
  - `6`, `سوشيال`, `social`: Official social media and website links.
  - `7`, `اشتراك`, `subscribe`: Opt-in to daily morning JFT broadcast.
  - `الغاء الاشتراك`, `unsubscribe`: Opt-out from broadcast.
  - `0`, `متطوع`, `agent`: Live agent handover.

> For complete message templates, responses, and translations, consult [bot-dictionary.md](file:///var/www/new/.agents/skills/whatsapp-api/references/bot-dictionary.md).

---

## 6. Daily JFT Broadcast Scheduling & Anti-Ban Throttling

- **Artisan Command:** `php artisan whatsapp:broadcast-jft`
- **Scheduler:** Daily at 07:00 Cairo time (`Africa/Cairo`) in `routes/console.php`.
- **Anti-Ban Throttling (`SendWhatsAppBroadcast`):**
  - Iterates through active `WhatsAppSubscriber` records.
  - Applies a randomized jitter delay between 2 and 5 seconds (`config('whatsapp.broadcast_delay_min_seconds')` to `max`) between each message.
  - Logs transmission metrics to `whatsapp_broadcast_logs`.

---

## 7. Docker & Operational Management

All commands run from `/var/www/new`:

```bash
# 1. Check container status
docker ps -f name=naegypt_whatsapp

# 2. View live logs
docker logs -f naegypt_whatsapp

# 3. Restart container
docker restart naegypt_whatsapp

# 4. Start / Stop via Docker Compose
docker compose -f docker-compose.whatsapp.yml up -d
docker compose -f docker-compose.whatsapp.yml down

# 5. Fix permissions on host storage volume if needed
sudo chmod -R 777 storage/app/whatsapp-data
```

---

## 8. Companion Helper Scripts

This skill includes ready-to-run helper scripts under `scripts/`:

1. **`scripts/check-health.sh`**:
   Comprehensive health audit verifying Docker container, port 3000 binding, device registry, connection state, and webhook setup.
   ```bash
   .agents/skills/whatsapp-api/scripts/check-health.sh
   ```

2. **`scripts/simulate-webhook.sh`**:
   Dispatches simulated incoming WhatsApp messages to test bot replies, live agent transitions, or subscription events against local, staging, or production endpoints.
   ```bash
   # Simulate sending "1" (JFT) to local Laravel
   .agents/skills/whatsapp-api/scripts/simulate-webhook.sh --message "1" --phone "201012345678"

   # Simulate targeting staging or production
   .agents/skills/whatsapp-api/scripts/simulate-webhook.sh --target production --message "helpline"
   ```

---

## 9. Testing & QA

Run the automated test suite covering Admin controllers, Webhooks, Bot keyword handlers, Broadcast queues, and Dev Mode whitelist filters:

```bash
php artisan test --filter=WhatsApp
```
