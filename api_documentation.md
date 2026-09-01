# NA-Egypt API Documentation (v1)

This document provides the complete, authoritative API reference for all backend endpoints available under `/api/v1/` on the NA-Egypt platform.

---

## 1. Overview & Base URL

- **Base URL:** `/api/v1` (e.g. `https://naegypt.org/api/v1` or `https://egyptna.org/api/v1`)
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Authentication:** Bearer token via Laravel Sanctum (`Authorization: Bearer <token>`)
- **API Versioning:** All endpoints are versioned under the `/api/v1/` prefix.
- **Response Format:** Standard REST JSON envelopes (`{ "data": ... }` for resources/collections).
- **HTTP Status Codes:**
  - `200 OK`: Successful retrieval / update
  - `201 Created`: Resource successfully created
  - `204 No Content`: Resource successfully deleted
  - `400 Bad Request`: Malformed payload or missing required OAuth attributes
  - `401 Unauthorized`: Missing or invalid authentication token
  - `403 Forbidden`: Insufficient permissions or role restrictions
  - `404 Not Found`: Requested resource not found
  - `422 Unprocessable Content`: Validation failure with field errors

---

## 2. Authentication Endpoints

### 2.1 Azure AD / Microsoft OAuth Direct Token Exchange
Exchange an Azure AD OAuth access token acquired via Microsoft Identity SDK (PKCE flow) for a backend Laravel Sanctum personal access token.

- **Endpoint:** `POST /api/v1/auth/azure/login` (Alias: `POST /api/v1/login/azure`)
- **Access:** Public
- **Headers:** `Content-Type: application/json`, `Accept: application/json`
- **Request Body:**
  ```json
  {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6..."
  }
  ```
- **Response (200 OK):**
  ```json
  {
    "user": {
      "id": 1,
      "name": "Ahmed Ali",
      "email": "ahmed.ali@naegypt.org",
      "service_body_id": 2,
      "created_at": "2026-01-01T00:00:00.000000Z",
      "updated_at": "2026-01-01T00:00:00.000000Z"
    },
    "token": "1|AbCdEf1234567890..."
  }
  ```

---

### 2.2 Current Authenticated User Profile
Retrieve the authenticated user's profile information, assigned service body, and permissions.

- **Endpoint:** `GET /api/v1/user`
- **Access:** Protected (`auth:sanctum`)
- **Headers:** `Authorization: Bearer <token>`
- **Response (200 OK):**
  ```json
  {
    "id": 1,
    "name": "Ahmed Ali",
    "email": "ahmed.ali@naegypt.org",
    "service_body_id": 2,
    "created_at": "2026-01-01T00:00:00.000000Z",
    "updated_at": "2026-01-01T00:00:00.000000Z"
  }
  ```

---

### 2.3 In-App WebBrowser Microsoft SSO Flow (Mobile Deep-Link)
For mobile applications (React Native / Expo), Microsoft authentication can be initiated via an in-app browser session:
- **Redirect URL:** `https://egyptna.org/login/microsoft?mobile=1`
- **Deep-link Callback:** `naegypt://auth-callback?token=<SANCTUM_TOKEN>`

---

## 3. Frontpage & Composite Content Endpoints (Public)

### 3.1 Frontpage Composite Data (`GET /api/v1/home` or `/api/v1/frontpage`)
Returns all aggregated data required for rendering the homepage in a single round-trip (platform statistics, daily spiritual reading, helpline contact cards, official social links, and upcoming featured calendar events). Cached for 30 minutes.

- **Endpoint:** `GET /api/v1/home` (Alias: `GET /api/v1/frontpage`)
- **Access:** Public
- **Query Parameters:**
  - `date` *(optional, string `YYYY-MM-DD`)*: Retrieve reading and event context for a specific date (defaults to today in Egypt timezone `Africa/Cairo`).
- **Response (200 OK):**
  ```json
  {
    "data": {
      "stats": {
        "weekly_meetings": 142,
        "total_meetings": 160,
        "in_person_groups": 45,
        "online_groups": 12,
        "groups": 45,
        "total_groups": 57,
        "governorates": 14,
        "cities": 14,
        "upcoming_events": 3
      },
      "jft": {
        "date": "2026-09-01",
        "page_date": "1 سبتمبر",
        "title": "الامتنان اليومي",
        "quote": "أنا ممتن جداً لتَوَصُلي للإيمان.",
        "quote_source": "منشور رقم 21 - المنعزل",
        "content": [
          "إن الإيمان بقوة عظمى يمكن أن يُحدِث كل الفرق عندما تسوء الأمور!...",
          "فالخطوات الاثنتا عشرة تقودنا بروية نحو صحوة روحانية..."
        ],
        "thought_for_the_day": "لليوم فقط: سأحافظ على صلتي الواعية بقوتي العظمى.",
        "content_html": "<p>...</p>"
      },
      "helplines": [
        {
          "region": "Egypt Region (General)",
          "region_ar": "إقليم مصر (عام)",
          "phones": ["+201006979198", "+201060933888"],
          "whatsapp": "https://wa.me/201060933888",
          "hours": "10 AM - 12 Midnight",
          "hours_ar": "١٠ ص - ١٢ منتصف الليل"
        },
        {
          "region": "Alexandria",
          "region_ar": "الإسكندرية",
          "phones": ["+201503884411"],
          "whatsapp": "https://wa.me/201503884411",
          "hours": "12 PM - 10 PM",
          "hours_ar": "١٢ م - ١٠ م"
        }
      ],
      "social_links": {
        "facebook": "https://www.facebook.com/OfficialNAEgyPage",
        "instagram": "https://www.instagram.com/narcoticsanonymousegy",
        "tiktok": "https://www.tiktok.com/@narcoticsanonymousegypt",
        "whatsapp": "https://wa.me/201060933888",
        "email": "pr@naegypt.org"
      },
      "upcoming_events": [
        {
          "id": 1,
          "title": "Regional Committee Meeting",
          "start": "2026-09-05T10:00:00.000000Z",
          "end": "2026-09-05T14:00:00.000000Z",
          "description": "Monthly RSC Meeting",
          "color": "#00698f",
          "organizer": "Egypt RSC",
          "location": "Community Hall, Cairo",
          "recurrence": ["monthly", "1st"],
          "formatted_recurrence": "First Saturday",
          "is_featured": true,
          "created_at": "2026-08-20T00:00:00.000000Z",
          "updated_at": "2026-08-20T00:00:00.000000Z"
        }
      ]
    }
  }
  ```

---

### 3.2 Just For Today Daily Reading (`GET /api/v1/jft`)
Retrieves structured daily reading from the "Just For Today" NA literature archive.

- **Endpoint:** `GET /api/v1/jft`
- **Access:** Public
- **Query Parameters:**
  - `date` *(optional, string `YYYY-MM-DD`)*: Date of the reading (defaults to current date in Cairo timezone).
- **Response (200 OK):**
  ```json
  {
    "data": {
      "date": "2026-09-01",
      "page_date": "1 سبتمبر",
      "title": "الامتنان",
      "quote": "أنا ممتن جداً لتَوَصُلي للإيمان.",
      "quote_source": "منشور رقم 21 - المنعزل",
      "content": [
        "إن الإيمان بقوة عظمى يمكن أن يُحدِث كل الفرق عندما تسوء الأمور!...",
        "فالخطوات الاثنتا عشرة تقودنا بروية نحو صحوة روحانية..."
      ],
      "thought_for_the_day": "لليوم فقط: أنا ممتن لعلاقتي مع القوة العظمى التي تعتني بي.",
      "content_html": "<p>...</p>"
    }
  }
  ```

---

### 3.3 Platform Statistics (`GET /api/v1/stats`)
Retrieves public aggregated metrics and counters.

- **Endpoint:** `GET /api/v1/stats`
- **Access:** Public
- **Response (200 OK):**
  ```json
  {
    "data": {
      "weekly_meetings": 142,
      "total_meetings": 160,
      "in_person_groups": 45,
      "online_groups": 12,
      "groups": 45,
      "total_groups": 57,
      "governorates": 14,
      "cities": 14,
      "upcoming_events": 3
    }
  }
  ```

---

## 4. Public Directory & Meeting Resources (Read Public, Write Protected)

All resources in this section support public read operations (`GET`), while write operations (`POST`, `PUT`, `PATCH`, `DELETE`) require `auth:sanctum` authentication.

---

### 4.1 Meetings Directory (`/api/v1/meetings`)

Provides meeting search and schedule listings with enriched geographical metadata, formatted timing, and prioritized location/meeting URL resolution.

#### Prioritized URL Resolution Logic
Both `location_url` and `meeting_url` fields in `MeetingResource` are resolved following a prioritized fallback hierarchy:
1. `$meeting->location_url`
2. `$meeting->meeting_url`
3. `$group->location`
4. `$directOnlineGroup->location`
5. `$directOnlineGroup->meeting_url`
6. `$directOnlineGroup->zoom_url`
7. `$directOnlineGroup->url`
8. `$directOnlineGroup->link`

#### `GET /api/v1/meetings`
- **Access:** Public
- **Query Filter Parameters:**
  - `day`: Filter by day name in Arabic (e.g. `الجمعة`) or English (e.g. `Friday`), or `all`.
  - `city`: Filter by city name (`ar_name` or `en_name`).
  - `neighborhood`: Filter by neighborhood name (`ar_name` or `en_name`).
  - `serviceBody`: Filter by service body / ASC name (`ar_name` or `en_name`).
  - `group`: Filter by group name (`ar_name` or `en_name`).
  - `type`: Filter by format/type code (e.g. `open`, `closed`).
  - `search`: Keyword string matching group name or address.
  - `virtualOnly`: `1` or `true` to filter online/virtual meetings.
  - `englishOnly`: `1` or `true` to filter meetings conducted in English.
- **Response (200 OK):**
  ```json
  {
    "data": [
      {
        "id": 10,
        "day_id": 1,
        "group_id": 3,
        "direct_online_group_id": null,
        "type": "open",
        "lang": "arabic",
        "status": "available",
        "start_time": "19:30:00",
        "end_time": "21:00:00",
        "formatted_start_time": "07:30 PM",
        "formatted_end_time": "09:00 PM",
        "duration": 90,
        "notes": "Wheelchair accessible",
        "recurrence": ["weekly"],

        "group_name_ar": "مجموعة الأمل",
        "group_name_en": "Hope Group",
        "group_type": "in_person",
        "address_ar": "وسط البلد، القاهرة",
        "address_en": "Downtown, Cairo",
        "location_url": "https://maps.google.com/?q=30.0444,31.2357",
        "meeting_url": "https://maps.google.com/?q=30.0444,31.2357",

        "neighborhood_id": 1,
        "neighborhood_name_ar": "وسط البلد",
        "neighborhood_name_en": "Downtown",
        "city_id": 1,
        "city_name_ar": "القاهرة",
        "city_name_en": "Cairo",

        "day": {
          "id": 1,
          "ar_name": "السبت",
          "en_name": "Saturday"
        },
        "topics": [
          { "id": 1, "ar_name": "خطوة أولى", "en_name": "Step 1" }
        ],
        "options": [
          { "id": 2, "ar_name": "مفتوح", "en_name": "Open" }
        ]
      }
    ]
  }
  ```

#### `GET /api/v1/meetings/{id}`
- **Access:** Public
- **Response (200 OK):** Single `MeetingResource` object.

#### `POST /api/v1/meetings` *(Auth Required)*
- **Body Schema:**
  ```json
  {
    "group_id": 3,
    "day_id": 1,
    "start_time": "19:30:00",
    "end_time": "21:00:00",
    "type": "open",
    "lang": "arabic",
    "status": "available",
    "notes": "Wheelchair accessible",
    "topics": [1, 2],
    "options": [2]
  }
  ```
- **Response (201 Created):** Single `MeetingResource` object.

#### `PUT /api/v1/meetings/{id}` *(Auth Required)*
- **Body Schema:** Partial update of any meeting attribute (`sometimes|required`).
- **Response (200 OK):** Updated `MeetingResource` object.

#### `DELETE /api/v1/meetings/{id}` *(Auth Required)*
- **Response (204 No Content)**

---

### 4.2 Groups Directory (`/api/v1/groups`)

#### `GET /api/v1/groups`
- **Access:** Public
- **Query Parameters:** `page` (integer), `per_page` (integer, default 15, max 100)
- **Response (200 OK):** Paginated list of groups.

#### `GET /api/v1/groups/{id}`
- **Access:** Public
- **Response (200 OK):** Details of the group with loaded relationships (`serviceBody`, `neighborhood`, `user`).

#### `POST /api/v1/groups` *(Auth Required)*
- **Body Schema:**
  ```json
  {
    "ar_name": "مجموعة الأمل",
    "en_name": "Hope Group",
    "ar_gsr_name": "أحمد",
    "en_gsr_name": "Ahmed",
    "phone": "+201000000000",
    "location": "https://maps.google.com/?q=30.0444,31.2357",
    "ar_address": "وسط البلد، القاهرة",
    "en_address": "Downtown, Cairo",
    "group_type": "in_person",
    "service_body_id": 1,
    "neighborhood_id": 2,
    "capacity": 50
  }
  ```
- **Response (201 Created):** Group object.

#### `PUT /api/v1/groups/{id}` *(Auth Required)*
- **Response (200 OK):** Updated Group object.

#### `DELETE /api/v1/groups/{id}` *(Auth Required)*
- **Response (204 No Content)**

---

### 4.3 Calendar Events (`/api/v1/calendar-events`)

Rich calendar events supporting date-range recurrence expansion, color coding, and featured flags.

#### `GET /api/v1/calendar-events`
- **Access:** Public
- **Query Parameters:**
  - `start` *(optional, ISO-8601 string `YYYY-MM-DD`)*: Window start date.
  - `end` *(optional, ISO-8601 string `YYYY-MM-DD`)*: Window end date.
  *(When both `start` and `end` are provided, recurring events are automatically expanded into discrete instances).*
- **Response (200 OK):**
  ```json
  {
    "data": [
      {
        "id": 1,
        "title": "Regional Committee Meeting",
        "start": "2026-09-01T10:00:00.000000Z",
        "end": "2026-09-01T14:00:00.000000Z",
        "description": "Monthly regional meeting",
        "user_id": 1,
        "color": "#00698f",
        "organizer": "Cairo Area",
        "location": "Community Hall, Cairo",
        "recurrence": ["monthly", "1st"],
        "formatted_recurrence": "First Tuesday",
        "is_featured": true,
        "created_at": "2026-08-20T00:00:00.000000Z",
        "updated_at": "2026-08-20T00:00:00.000000Z"
      }
    ]
  }
  ```

#### `GET /api/v1/calendar-events/{id}`
- **Response (200 OK):** Single `CalendarEventResource`.

#### `POST /api/v1/calendar-events` *(Auth Required)*
- **Body Schema:**
  ```json
  {
    "title": "Regional Committee Meeting",
    "start": "2026-09-01 10:00:00",
    "end": "2026-09-01 14:00:00",
    "description": "Monthly meeting notes",
    "color": "#00698f",
    "organizer": "Cairo Area",
    "location": "Community Hall, Cairo",
    "recurrence": ["monthly", "1st"],
    "is_featured": true
  }
  ```
- **Response (201 Created):** Single `CalendarEventResource`.

#### `PUT /api/v1/calendar-events/{id}` *(Auth Required)*
- **Response (200 OK):** Updated `CalendarEventResource`.

#### `DELETE /api/v1/calendar-events/{id}` *(Auth Required)*
- **Response (200 OK):** `{"message": "Deleted successfully"}`

---

### 4.4 Announcements & Events (`/api/v1/events`)

Announcements linked to specific service bodies and days of the week.

- `GET /api/v1/events` *(Public)* -> Paginated or complete list of announcements.
- `GET /api/v1/events/{id}` *(Public)* -> Single announcement with relations.
- `POST /api/v1/events` *(Auth Required)* -> Returns `201 Created`.
  - **Body Schema:** `name` (required|string), `description` (required|string), `date` (required|date), `service_body_id` (required|exists:service_bodies,id), `day_id` (required|exists:days,id)
- `PUT /api/v1/events/{id}` *(Auth Required)* -> Returns `200 OK`.
- `DELETE /api/v1/events/{id}` *(Auth Required)* -> Returns `204 No Content`.

---

### 4.5 Group Agendas (`/api/v1/agendas`)

Group monthly business meeting and service agenda submissions.

- `GET /api/v1/agendas` *(Public read)*
- `GET /api/v1/agendas/{id}` *(Public read)*
- `POST /api/v1/agendas` *(Auth Required)* -> Returns `201 Created`
  - **Body Schema:** `group_id` (required|exists:groups,id), `agenda_date` (required|date), `service_position`, `submitter_name`, `meetings_per_week`, `new_comers`, `open_positions`, `next_business_meeting`, `recovery_atmosphere`, `trusted_servants`, `financial_issues`, `other_topics` (array).
- `PUT /api/v1/agendas/{id}` *(Auth Required)* -> Returns `200 OK`.
- `DELETE /api/v1/agendas/{id}` *(Auth Required)* -> Returns `204 No Content`.

---

### 4.6 Service Body Agendas (`/api/v1/service-body-agendas`)

Area and regional monthly agenda reports with voting topics.
- **Visibility Rules:**
  - `super admin` and `rsc` roles: Can view all agendas (including drafts and exceptional agendas).
  - `ServiceBody` role: Can view agendas belonging to their assigned service body.
  - Public / Unauthenticated users: Can view only `approved` agendas whose monthly release date has arrived (10th of the month).

- `GET /api/v1/service-body-agendas` *(Public sees released agendas; Auth sees authorized scopes)*
- `GET /api/v1/service-body-agendas/{id}`
- `POST /api/v1/service-body-agendas` *(Auth Required)*
- `PUT /api/v1/service-body-agendas/{id}` *(Auth Required)*
- `DELETE /api/v1/service-body-agendas/{id}` *(Auth Required)*

---

### 4.7 Geographic & Meeting Taxonomy Lookups

All lookup endpoints support public `GET` and require `auth:sanctum` for `POST`, `PUT`, `DELETE`:

| Resource Endpoint | GET (Public) | POST/PUT/DELETE (Auth) | Description |
| :--- | :--- | :--- | :--- |
| `/api/v1/cities` | List / Detail | Full CRUD | Cities / Governorates (`ar_name`, `en_name`) |
| `/api/v1/neighborhoods` | List / Detail | Full CRUD | Neighborhoods linked to cities |
| `/api/v1/days` | List / Detail | Full CRUD | Weekdays lookup (Saturday through Friday) |
| `/api/v1/topics` | List / Detail | Full CRUD | Meeting topics (Step, Tradition, Concept, etc.) |
| `/api/v1/options` | List / Detail | Full CRUD | Meeting options/formats (Open, Closed, Men, Women) |
| `/api/v1/sc-meetings` | List / Detail | Full CRUD | Service Committee schedule meetings |
| `/api/v1/service-bodies` | List / Detail | Full CRUD | Areas / Service bodies (e.g. Cairo ASC, Alexandria ASC) |
| `/api/v1/service-committees` | List / Detail | Full CRUD | Subcommittees (PI, H&I, Literature, Web) |

---

## 5. Protected Resources (Full Authentication Required)

These resources require `auth:sanctum` authentication for **all** HTTP methods (`index`, `show`, `store`, `update`, `destroy`):

### 5.1 Committee Reports (`/api/v1/committee-reports`)
Periodic PDF / text reports uploaded by subcommittees.
- `GET /api/v1/committee-reports`
- `POST /api/v1/committee-reports`
- `GET /api/v1/committee-reports/{id}`
- `PUT /api/v1/committee-reports/{id}`
- `DELETE /api/v1/committee-reports/{id}`

---

### 5.2 Contact Requests (`/api/v1/contact-requests` and `/api/v1/contact-us`)
Submissions from contact forms and member support inquiries.
- `GET /api/v1/contact-requests`
- `POST /api/v1/contact-requests`
- `GET /api/v1/contact-requests/{id}`
- `PUT /api/v1/contact-requests/{id}`
- `DELETE /api/v1/contact-requests/{id}`

---

### 5.3 Newsletter Members (`/api/v1/newsletter-members`)
Newsletter subscriber management.
- `GET /api/v1/newsletter-members`
- `POST /api/v1/newsletter-members`
- `GET /api/v1/newsletter-members/{id}`
- `PUT /api/v1/newsletter-members/{id}`
- `DELETE /api/v1/newsletter-members/{id}`

---

### 5.4 Financial Transactions (`/api/v1/transactions`)
7th tradition contributions, area funds, and expense ledger entries.
- `GET /api/v1/transactions`
- `POST /api/v1/transactions`
- `GET /api/v1/transactions/{id}`
- `PUT /api/v1/transactions/{id}`
- `DELETE /api/v1/transactions/{id}`

---

### 5.5 User, Role & Permission Management
Administrative user management and Spatie RBAC integration.
- `GET /api/v1/users`
- `POST /api/v1/users`
- `GET /api/v1/users/{id}`
- `PUT /api/v1/users/{id}`
- `DELETE /api/v1/users/{id}`
- `GET /api/v1/roles`
- `GET /api/v1/permissions`

---

## 6. Query Parameters & Global Conventions

- **Pagination:** Standard Laravel pagination parameters `?page=1&per_page=15` (max `per_page=100`).
- **Filtering:** Model query scopes and parameters as described per endpoint.
- **Sorting:** `?sort_by=created_at&sort_order=desc`.
- **Eager Loading:** All listing endpoints automatically eager-load required relations to prevent N+1 queries.
- **Sanctum Authentication Header:**
  ```http
  Authorization: Bearer <your_sanctum_personal_access_token>
  ```
