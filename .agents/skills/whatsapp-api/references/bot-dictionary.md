# WhatsApp Bot Keyword Dictionary & State Machine Reference

This reference documents the conversational triggers, state transitions, and keyword dictionaries handled by `App\Services\WhatsApp\WhatsAppBotService`.

---

## 1. Conversational Lifecycle & State Machine

```
User Message Arrives
        │
        ▼
Is Live Agent Active? ─── YES ──► Is message "انهاء" or "end"?
        │                                │
        NO                               ├─ YES ──► Resume Bot & send Main Menu
        │                                └─ NO  ──► Forward to Volunteer Inbox (Bot silent)
        ▼
Evaluate User Input:
  ├─ "0", "متطوع", "volunteer" ───────► Enable Live Agent (30 min timeout) & Alert Admins
  ├─ "1", "jft", "فقط لليوم" ──────────► Send Today's Just For Today reading
  ├─ "2", "اجتماعات", "meetings" ──────► Enter Step 1: Prompt for City (القاهرة, الجيزة, ...)
  ├─ If current_step == 'awaiting_city' ► Query meetings in city & reset step
  ├─ "3", "خط المساعدة", "helpline" ───► Send Regional Hotline Numbers
  ├─ "4", "فعاليات", "events" ─────────► Fetch Upcoming Events from CalendarEvent
  ├─ "5", "نماذج", "forms" ────────────► Send Public Form Links (Cooperation, Literature)
  ├─ "6", "سوشيال", "social" ──────────► Send Official Website & Social Links
  ├─ "7", "اشتراك", "subscribe" ───────► Add/Activate Subscriber for Daily 07:00 JFT
  ├─ "الغاء الاشتراك", "unsubscribe" ──► Deactivate Subscriber
  └─ Default (Unrecognized) ───────────► Send Greeting & Main Menu Options
```

---

## 2. Keyword & Trigger Dictionary

| Keyword Triggers | Action / Handler | Response Summary |
|---|---|---|
| `1`, `jft`, `قراءة`, `فقط لليوم`, `اليوم` | `handleJft()` | Delivers date, title, quote, body, and daily affirmation from `JftService`. |
| `2`, `اجتماعات`, `اجتماع`, `مواعيد`, `meetings` | `handleMeetingsPrompt()` | Prompts user to select city: `1: القاهرة`, `2: الجيزة`, `3: الإسكندرية`, `4: أونلاين`. Sets conversation step to `awaiting_meeting_city`. |
| City selection (`awaiting_meeting_city`) | `handleMeetingsByCity()` | Returns meeting names, days, times, and maps/zoom links for chosen city. |
| `3`, `خط المساعدة`, `مساعدة`, `ارقام`, `helpline` | `handleHelpline()` | Lists Cairo, Alexandria, Upper Egypt hotlines and operating hours. |
| `4`, `فعاليات`, `مؤتمرات`, `events` | `handleEvents()` | Returns next 3 upcoming fellowship conventions/events. |
| `5`, `نماذج`, `طلبات`, `forms` | `handleForms()` | Links to public literature order, PI cooperation, and contact request forms. |
| `6`, `سوشيال`, `موقع`, `روابط`, `social` | `handleSocial()` | Direct URLs for NA Egypt official website, Facebook, YouTube, and Instagram. |
| `7`, `اشتراك`, `subscribe` | `handleSubscribe()` | Subscribes phone to `whatsapp_subscribers` for automated daily morning JFT. |
| `الغاء الاشتراك`, `الغاء`, `unsubscribe`, `stop` | `handleUnsubscribe()` | Deactivates subscription (`status = inactive`). |
| `0`, `متطوع`, `تحدث مع متطوع`, `agent`, `human` | `handleVolunteerRequest()` | Activates `is_live_agent_mode = true` for 30 minutes; alerts volunteers in inbox. |
| `انهاء`, `إنهاء`, `خروج`, `end`, `quit` | `handleEndLiveAgent()` | Resumes automated bot immediately. |

---

## 3. Live Agent Handover Mechanics

1. **Activation:**
   - **Triggered by user:** Typing `0` or `متطوع`.
   - **Triggered by volunteer:** When an admin sends a reply from `/whatsapp/inbox`, `WhatsAppConversation::enableLiveAgent()` is called automatically.
2. **Timeout:**
   - Defaults to `30` minutes (configured via `WHATSAPP_LIVE_AGENT_TIMEOUT`).
   - If the volunteer does not send another message within 30 minutes and the user messages again, the bot automatically wakes back up.
3. **Suppression:**
   - While `isLiveAgentActive()` is `true`, `ProcessIncomingWhatsAppMessage` stores the incoming text in `whatsapp_messages` but suppresses `WhatsAppBotService::process()`.
