# Go WhatsApp Web Multi-Device Microservice API Reference (v3.4.0)

This reference documents the low-level HTTP REST API provided by [`aldinokemal/go-whatsapp-web-multidevice`](https://github.com/aldinokemal/go-whatsapp-web-multidevice) (container `naegypt_whatsapp` running on `127.0.0.1:3000`).

---

## 1. Authentication & Protocol

- **Base URL:** `http://127.0.0.1:3000`
- **Authentication:** HTTP Basic Authentication
  - Username: `naegypt`
  - Password: `${WHATSAPP_API_KEY}` (default: `secret_whatsapp_token_change_me`)
- **Device Routing Header:**
  - `X-Device-Id: default` (or query param `?device_id=default`)
  - All device-specific requests require this header.

---

## 2. Device Management & Pairing

### `GET /devices`
List all devices currently registered in the database.
```bash
curl -s -u naegypt:secret_whatsapp_token_change_me http://127.0.0.1:3000/devices
```
**Response (200 OK):**
```json
{
  "code": "SUCCESS",
  "message": "List devices",
  "results": [
    {
      "id": "default",
      "display_name": "",
      "jid": "2010xxxxxxxx@s.whatsapp.net",
      "state": "connected",
      "created_at": "2026-09-29T00:50:23Z"
    }
  ]
}
```

### `POST /devices`
Add a new device identifier to the registry.
```bash
curl -s -X POST -u naegypt:secret_whatsapp_token_change_me \
  -H "Content-Type: application/json" \
  -d '{"device_id":"default"}' \
  http://127.0.0.1:3000/devices
```

### `GET /devices/{id}/status`
Check device connection and login state.
```bash
curl -s -u naegypt:secret_whatsapp_token_change_me http://127.0.0.1:3000/devices/default/status
```
**Response (200 OK):**
```json
{
  "code": "SUCCESS",
  "message": "Device status",
  "results": {
    "device_id": "default",
    "is_connected": true,
    "is_logged_in": true
  }
}
```

### `GET /devices/{id}/login`
Generate a pairing QR code. The QR expires after 30 seconds.
```bash
curl -s -u naegypt:secret_whatsapp_token_change_me http://127.0.0.1:3000/devices/default/login
```
**Response (200 OK):**
```json
{
  "code": "SUCCESS",
  "message": "Login success",
  "results": {
    "device_id": "default",
    "qr_duration": 30,
    "qr_link": "http://127.0.0.1:3000/statics/qrcode/scan-qr-9146847b.png"
  }
}
```

### `POST /devices/{id}/login/code`
Pair using an 8-character pairing code sent to a phone number.
```bash
curl -s -X POST -u naegypt:secret_whatsapp_token_change_me \
  "http://127.0.0.1:3000/devices/default/login/code?phone=201012345678"
```

### `POST /devices/{id}/reconnect`
Reconnect existing WhatsApp session if disconnected.
```bash
curl -s -X POST -u naegypt:secret_whatsapp_token_change_me http://127.0.0.1:3000/devices/default/reconnect
```

### `POST /devices/{id}/logout`
Log out and invalidate current WhatsApp session.
```bash
curl -s -X POST -u naegypt:secret_whatsapp_token_change_me http://127.0.0.1:3000/devices/default/logout
```

---

## 3. Webhook Registration

### `GET /devices/{id}/webhook`
Get current webhook settings for the device.
```bash
curl -s -u naegypt:secret_whatsapp_token_change_me http://127.0.0.1:3000/devices/default/webhook
```

### `PATCH /devices/{id}/webhook`
Set or update the destination webhook URL.
```bash
curl -s -X PATCH -u naegypt:secret_whatsapp_token_change_me \
  -H "Content-Type: application/json" \
  -d '{"webhook_url":"https://naegypt.org/api/v1/whatsapp/webhook"}' \
  http://127.0.0.1:3000/devices/default/webhook
```

---

## 4. Sending Messages

### `POST /send/message`
Send a simple text message.
```bash
curl -s -X POST -u naegypt:secret_whatsapp_token_change_me \
  -H "X-Device-Id: default" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "201012345678",
    "message": "Welcome to Narcotics Anonymous Egypt."
  }' \
  http://127.0.0.1:3000/send/message
```

### `POST /send/image`
Send an image with optional caption.
```bash
curl -s -X POST -u naegypt:secret_whatsapp_token_change_me \
  -H "X-Device-Id: default" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "201012345678",
    "image": "https://naegypt.org/images/logo.png",
    "caption": "زمالة المدمنين المجهولين بمصر"
  }' \
  http://127.0.0.1:3000/send/image
```
