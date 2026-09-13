# NA-Egypt API Documentation (v1)

This document provides the complete, authoritative REST API reference for all backend endpoints available under `/api/v1/` on the NA-Egypt platform (`naegypt.org` / `egyptna.org`).

---

## 1. Overview & Architecture Conventions

- **Base URL:** `https://naegypt.org/api/v1` (or local `/api/v1`)
- **Default Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json` (or `multipart/form-data` for file upload endpoints)
- **Authentication:** Bearer token via Laravel Sanctum (`Authorization: Bearer <token>`)
- **API Versioning:** All endpoints are strictly versioned under the `/api/v1/` prefix in `routes/api.php`.
- **Response Format:** Standard REST JSON envelopes (`{ "data": ... }` for resources/collections).
- **HTTP Status Codes:**
  - `200 OK`: Successful retrieval (`index()`, `show()`) or update (`update()`).
  - `201 Created`: Resource successfully created across all `store()` endpoints and public form submissions.
  - `204 No Content`: Resource successfully deleted (`destroy()`) or token revoked (`logout()`).
  - `400 Bad Request`: Malformed payload or missing required OAuth attributes.
  - `401 Unauthorized`: Missing or invalid Bearer authentication token.
  - `403 Forbidden`: Insufficient permissions or role restrictions.
  - `404 Not Found`: Requested resource not found.
  - `422 Unprocessable Content`: Validation failure with field errors dictionary.

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

### 2.3 Token Revocation / Logout
Revokes the caller's active personal access token, destroying the current session.

- **Endpoint:** `POST /api/v1/auth/logout` (Alias: `POST /api/v1/logout`)
- **Access:** Protected (`auth:sanctum`)
- **Headers:** `Authorization: Bearer <token>`
- **Response (204 No Content)**

---

### 2.4 In-App WebBrowser Microsoft SSO Flow (Mobile Deep-Link)
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
        "date": "2026-08-17",
        "page_date": "17 أغسطس",
        "title": "الامتنان اليومي",
        "quote": "أنا ممتن جداً للتعافي وللفرصة لأعيش يوماً آخر نظيفاً.",
        "quote_source": "النص الأساسي - ص. 42",
        "content": [
          "الامتنان ليس مجرد شعور، بل هو تصرف نمارسه يومياً...",
          "عندما نركز على ما نملكه بدلاً مما ينقصنا، نكتشف وفرة الحياة."
        ],
        "thought_for_the_day": "لليوم فقط: سأحافظ على صلتي الواعية بقوتي العظمى وسأكون ممتناً لما لدي.",
        "content_html": "<p>الامتنان ليس مجرد شعور...</p>"
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
          "region": "Alexandria Area",
          "region_ar": "منطقة الإسكندرية",
          "phones": ["+201503884411"],
          "whatsapp": "https://wa.me/201503884411",
          "hours": "12 PM - 10 PM",
          "hours_ar": "١٢ م - ١٠ م"
        },
        {
          "region": "Upper Egypt Area",
          "region_ar": "منطقة مصر العليا",
          "phones": ["+201018596644"],
          "whatsapp": null,
          "hours": "2 PM - 11 PM",
          "hours_ar": "٢ م - ١١ م"
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
          "title": "مؤتمر التعافي السنوي ٢٠٢٦",
          "start": "2026-09-01T10:00:00.000000Z",
          "end": "2026-09-01T18:00:00.000000Z",
          "description": "مؤتمر سنوي عام لجميع الأعضاء...",
          "color": "#00698f",
          "organizer": "لجنة المؤتمرات",
          "location": "القاهرة - مدينة نصر",
          "is_featured": true
        }
      ]
    }
  }
  ```

---

### 3.2 Just For Today Daily Reading (`GET /api/v1/jft`)
Retrieves structured and HTML representation of the Arabic Just For Today daily reading for any day of the year. Cached for 24 hours per file.

- **Endpoint:** `GET /api/v1/jft`
- **Access:** Public
- **Query Parameters:**
  - `date` *(optional, string `YYYY-MM-DD`)*: Defaults to current day in Cairo time.
- **Response (200 OK):**
  ```json
  {
    "data": {
      "date": "2026-08-17",
      "page_date": "17 أغسطس",
      "title": "الامتنان اليومي",
      "quote": "أنا ممتن جداً للتعافي...",
      "quote_source": "النص الأساسي - ص. 42",
      "content": [
        "الفقرة الأولى...",
        "الفقرة الثانية..."
      ],
      "thought_for_the_day": "لليوم فقط: سأحافظ على صلتي الواعية...",
      "content_html": "<p>...</p>"
    }
  }
  ```

---

### 3.3 Platform Statistics (`GET /api/v1/stats`)
Live statistical counter cached for 1 hour.

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

## 4. Public Directory & Hybrid Resources (Read Public, Write Protected)

*All `GET` listing and detail endpoints in this section are publicly readable. Mutations (`POST`, `PUT`, `PATCH`, `DELETE`) require `Authorization: Bearer <token>`.*

---

### 4.1 Meetings Directory (`/api/v1/meetings`)

#### Prioritized URL Resolution Hierarchy
`location_url` and `meeting_url` fields in `MeetingResource` resolve via prioritized fallback:
1. `$meeting->location_url`
2. `$meeting->meeting_url`
3. `$group->location`
4. `$directOnlineGroup->location`
5. `$directOnlineGroup->meeting_url`
6. `$directOnlineGroup->zoom_url`
7. `$directOnlineGroup->url`
8. `$directOnlineGroup->link`

#### `GET /api/v1/meetings`
Query parameters:
- `day`: Arabic or English day name (`Friday`, `الجمعة`, or `all`).
- `city`: City name (`ar_name` or `en_name`).
- `neighborhood`: Neighborhood name.
- `serviceBody`: Area name.
- `group`: Group name.
- `type`: Meeting format / type code.
- `search`: Keyword matching group name or address.
- `virtualOnly`: `1` or `true` to filter virtual/online meetings.
- `englishOnly`: `1` or `true` to filter English-language meetings.

#### `GET /api/v1/meetings/{id}`
Returns a single meeting object with loaded relations (`group`, `day`, `topics`, `options`).

#### `POST /api/v1/meetings` *(Auth Required)*
- **Response (201 Created):** Single `MeetingResource` object.

#### `PUT /api/v1/meetings/{id}` *(Auth Required)*
- **Response (200 OK):** Updated `MeetingResource` object.

#### `DELETE /api/v1/meetings/{id}` *(Auth Required)*
- **Response (204 No Content)**

---

### 4.2 Groups Directory (`/api/v1/groups`)

- `GET /api/v1/groups`: Paginated directory of in-person fellowship groups (`per_page` query parameter supported).
- `GET /api/v1/groups/{id}`: Single group details with loaded relationships.
- `POST /api/v1/groups`: Create new group (Returns `201 Created`).
- `PUT /api/v1/groups/{id}`: Update group attributes (Returns `200 OK`).
- `DELETE /api/v1/groups/{id}`: Delete group record (Returns `204 No Content`).

---

### 4.3 Direct Online Groups (`/api/v1/direct-online-groups`)

Manages virtual fellowship recovery groups holding meetings on Zoom, Google Meet, MS Teams, etc.

#### `GET /api/v1/direct-online-groups`
- **Access:** Public
- **Query Params:** `per_page` (integer, default 15, max 100), `page` (integer)
- **Response (200 OK):** Paginated `DirectOnlineGroupResource` collection with relations (`user`, `meetings`).

#### `GET /api/v1/direct-online-groups/{id}`
- **Access:** Public
- **Response (200 OK):** Single `DirectOnlineGroupResource`.

#### `POST /api/v1/direct-online-groups` *(Auth Required)*
- **Body Schema:**
  ```json
  {
    "ar_name": "مجموعة الأمل أونلاين",
    "en_name": "Hope Online Group",
    "ar_gsr_name": "أحمد",
    "en_gsr_name": "Ahmed",
    "phone": "+201011122233",
    "location": "https://zoom.us/j/123456789"
  }
  ```
- **Response (201 Created)**

#### `PUT /api/v1/direct-online-groups/{id}` *(Auth Required)*
- **Response (200 OK)**

#### `DELETE /api/v1/direct-online-groups/{id}` *(Auth Required)*
- **Response (204 No Content)**

---

### 4.4 Calendar Events (`/api/v1/calendar-events`)

- `GET /api/v1/calendar-events`: Public events listing. Supports `start` and `end` ISO-8601 query parameters with automatic recurrence expansion.
- `GET /api/v1/calendar-events/{id}`: Single calendar event.
- `POST /api/v1/calendar-events`: Create event (Returns `201 Created`).
- `PUT /api/v1/calendar-events/{id}`: Update event (Returns `200 OK`).
- `DELETE /api/v1/calendar-events/{id}`: Delete event (Returns `204 No Content`).

---

### 4.5 Announcements & Events (`/api/v1/events`)

- `GET /api/v1/events`: List service body announcements and events.
- `GET /api/v1/events/{id}`: Single event details.
- `POST /api/v1/events`: Create event (Returns `201 Created`).
- `PUT /api/v1/events/{id}`: Update event (Returns `200 OK`).
- `DELETE /api/v1/events/{id}`: Delete event (Returns `204 No Content`).

---

### 4.6 Group Agendas (`/api/v1/agendas`)

- `GET /api/v1/agendas`: List group agenda submissions.
- `GET /api/v1/agendas/{id}`: Single agenda report.
- `POST /api/v1/agendas`: Submit group agenda report (Returns `201 Created`).
- `PUT /api/v1/agendas/{id}`: Update agenda (Returns `200 OK`).
- `DELETE /api/v1/agendas/{id}`: Delete agenda (Returns `204 No Content`).

---

### 4.7 Service Committee Workgroups (`/api/v1/workgroups`)

Sub-entities belonging to parent Service Committees (`parent_id != null`). Scoped strictly to parent committee officers, RSC Committee 83, and Super Admins.

#### `GET /api/v1/workgroups`
- **Access:** Public
- **Query Params:** `parent_id`, `status` (`active`/`inactive`), `per_page`
- **Response (200 OK):** Paginated `WorkgroupResource` collection with loaded `parent`, `user`, and `meetings`.

#### `POST /api/v1/workgroups` *(Auth Required)*
- **Body Schema:**
  ```json
  {
    "parent_id": 1,
    "workgroup_type": "standing",
    "status": "active",
    "ar_name": "مجموعة عمل السوشيال ميديا",
    "en_name": "Social Media Workgroup",
    "chairman_name": "محمد",
    "chairman_phone": "+201009988776"
  }
  ```
- **Response (201 Created)**

#### `PUT /api/v1/workgroups/{id}` *(Auth Required)*
- **Response (200 OK)**

#### `DELETE /api/v1/workgroups/{id}` *(Auth Required)*
- **Response (204 No Content)**

---

### 4.8 Geographic & Meeting Taxonomy Lookups

| Resource Endpoint | GET (Public) | POST/PUT/DELETE (Auth) | Description & Key Attributes |
| :--- | :--- | :--- | :--- |
| `/api/v1/cities` | List / Detail | Full CRUD (`201 Created`, `204 No Content`) | Cities directory (`ar_name`, `en_name`, `latitude`, `longitude`) |
| `/api/v1/neighborhoods` | List / Detail | Full CRUD (`201 Created`, `204 No Content`) | Neighborhoods (`ar_name`, `en_name`, `city_id`, `latitude`, `longitude`) |
| `/api/v1/days` | List / Detail | Full CRUD (`201 Created`, `204 No Content`) | Week days lookup (`ar_name`, `en_name`) |
| `/api/v1/topics` | List / Detail | Full CRUD (`201 Created`, `204 No Content`) | Meeting topics (`ar_name`, `en_name`, `description`) |
| `/api/v1/options` | List / Detail | Full CRUD (`201 Created`, `204 No Content`) | Meeting options (`ar_name`, `en_name`) |
| `/api/v1/sc-meetings` | List / Detail | Full CRUD (`201 Created`, `204 No Content`) | Service Committee schedule meetings (`service_committee_id`, `week_number`, `day_id`, `time`, `notes`) |
| `/api/v1/service-bodies` | List / Detail | Full CRUD (`201 Created`, `204 No Content`) | Areas / Service bodies (`ar_name`, `en_name`, `day_id`, `start_time`, `end_time`, `location`) |
| `/api/v1/service-committees` | List / Detail | Full CRUD (`201 Created`, `204 No Content`) | Subcommittees (`ar_name`, `en_name`, `ar_address`, `en_address`, `location`) |

---

## 5. Protected Resources (Full Authentication Required)

*All requests to the following endpoints require `Authorization: Bearer <sanctum_token>`.*

---

### 5.1 Service Body Agendas (`/api/v1/service-body-agendas`)
- `GET /api/v1/service-body-agendas`: List available agendas with permission rules applied.
- `POST /api/v1/service-body-agendas`: Create monthly agenda (Returns `201 Created`).
- `GET /api/v1/service-body-agendas/{id}`: Single agenda with questions & answers.
- `PUT /api/v1/service-body-agendas/{id}`: Update agenda data.
- `DELETE /api/v1/service-body-agendas/{id}`: Delete agenda record (`204 No Content`).

---

### 5.2 IT & Data Change Requests (`/api/v1/change-requests`)

Allows trusted servants and members to submit change requests for meetings, groups, and committee information with file attachments.

#### `GET /api/v1/change-requests`
- **Access:** Authenticated. Regular users view their own requests; super admins view all requests across the fellowship.
- **Query Params:** `status` (`pending`, `in_progress`, `completed`, `rejected`), `per_page`
- **Response (200 OK):** Paginated `ChangeRequestResource` list.

#### `POST /api/v1/change-requests`
- **Content-Type:** `multipart/form-data`
- **Form Fields:**
  - `request_type`: `required|string|in:meetings_groups,committee_info,general,other`
  - `subject`: `required|string|max:255`
  - `description`: `required|string`
  - `attachment`: `nullable|file|mimes:pdf,png,jpg,jpeg,docx,xlsx|max:5120` (up to 5MB)
- **Response (201 Created):** Single `ChangeRequestResource`.

#### `GET /api/v1/change-requests/{id}`
- **Response (200 OK)**

#### `PATCH /api/v1/change-requests/{id}/status` *(Super Admin Only)*
- **Body Schema:**
  ```json
  {
    "status": "in_progress"
  }
  ```
- **Response (200 OK)**

#### `DELETE /api/v1/change-requests/{id}`
Deletes request and any associated file attachment. Allowed for owner (if `pending`) or super admin.
- **Response (204 No Content)**

---

### 5.3 Custom Forms & Public Submissions (`/api/v1/forms`)

#### Public Form Endpoints (Unauthenticated):
- `GET /api/v1/forms/public/{slug}`: Retrieves public form definition and active fields. Increments form views counter. Returns `404` if unpublished/archived.
- `POST /api/v1/forms/public/{slug}/submit`: Submits response data for an active form.
  - **Body Schema:** Pass field values using `field_{id}`:
    ```json
    {
      "field_1": "أحمد علي",
      "field_2": "ahmed@example.com",
      "field_3": "yes"
    }
    ```
  - **Response (201 Created):**
    ```json
    {
      "data": {
        "id": 10,
        "custom_form_id": 2,
        "user_id": null,
        "responses": {
          "1": "أحمد علي",
          "2": "ahmed@example.com"
        },
        "created_at": "2026-09-13T03:00:00.000000Z"
      }
    }
    ```

#### Form Management Endpoints (`auth:sanctum`):
- `GET /api/v1/forms`: List user's forms (or all forms if super admin).
- `POST /api/v1/forms`: Create custom form with embedded fields (Returns `201 Created`).
- `GET /api/v1/forms/{id}`: Single form details with fields and submission count.
- `PUT /api/v1/forms/{id}`: Update form properties.
- `DELETE /api/v1/forms/{id}`: Delete form and cascaded fields/submissions (`204 No Content`).
- `GET /api/v1/forms/{id}/submissions`: Paginated list of submissions for the specified form.

---

### 5.4 Committee Reports (`/api/v1/committee-reports`)
- `GET /api/v1/committee-reports`: List sub-committee periodic reports.
- `POST /api/v1/committee-reports`: Upload/publish sub-committee report (Returns `201 Created`).
- `GET /api/v1/committee-reports/{id}`: Retrieve report details.
- `PUT /api/v1/committee-reports/{id}`: Update report.
- `DELETE /api/v1/committee-reports/{id}`: Delete report (`204 No Content`).

---

### 5.5 Contact Requests (`/api/v1/contact-requests` & `/api/v1/contact-us`)
- `GET /api/v1/contact-requests` / `GET /api/v1/contact-us`: Retrieve member/public contact submissions.
- `POST /api/v1/contact-us`: Create contact request (Returns `201 Created`).
- `GET /api/v1/contact-us/{id}`
- `PUT /api/v1/contact-us/{id}`
- `DELETE /api/v1/contact-us/{id}` (`204 No Content`)

---

### 5.6 Newsletter Members (`/api/v1/newsletter-members`)
- `GET /api/v1/newsletter-members`: Newsletter recipient directory.
- `POST /api/v1/newsletter-members`: Add subscriber (Returns `201 Created`).
- `DELETE /api/v1/newsletter-members/{id}`: Unsubscribe/remove member (`204 No Content`).

---

### 5.7 Financial & Audit Transactions (`/api/v1/transactions`)
- `GET /api/v1/transactions`: Audit ledger recording system and model actions.
- `POST /api/v1/transactions`: Record audit transaction (Returns `201 Created`).
- `GET /api/v1/transactions/{id}`
- `PUT /api/v1/transactions/{id}`
- `DELETE /api/v1/transactions/{id}` (`204 No Content`)

---

### 5.8 User, Role & Permission Management
- `GET /api/v1/users`: List users (Super admin / RSC).
- `POST /api/v1/users`: Create user account (Returns `201 Created`).
- `GET /api/v1/users/{id}`: User profile.
- `PUT /api/v1/users/{id}`: Update user role / service body assignment.
- `DELETE /api/v1/users/{id}`: Deactivate/delete user account (`204 No Content`).
- `GET /api/v1/roles`: System roles (`super admin`, `rsc`, `Committees`, `Workgroups`, etc.).
- `GET /api/v1/permissions`: Role permissions matrix.

---

## 6. Universal Client Integration Snippets

### Example cURL Request with Auth:
```bash
curl -X GET "https://naegypt.org/api/v1/meetings?city=Cairo&day=Friday" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|your_sanctum_token_here"
```

### TypeScript / JavaScript Request Helper Example:
```typescript
interface ApiResponse<T> {
  data: T;
}

export class NaEgyptApiClient {
  private baseUrl: string;
  private token: string | null = null;

  constructor(baseUrl: string = "https://naegypt.org/api/v1") {
    this.baseUrl = baseUrl;
  }

  public setToken(token: string | null) {
    this.token = token;
  }

  private async request<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
    const headers: Record<string, string> = {
      "Accept": "application/json",
      ...(options.body instanceof FormData ? {} : { "Content-Type": "application/json" }),
      ...(this.token ? { "Authorization": `Bearer ${this.token}` } : {}),
      ...(options.headers as Record<string, string> || {}),
    };

    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      ...options,
      headers,
    });

    if (response.status === 204) {
      return null as unknown as T;
    }

    const data = await response.json();
    if (!response.ok) {
      throw new Error(data.message || `HTTP Error ${response.status}`);
    }

    return data;
  }

  // Azure Token Exchange
  public async loginWithAzure(azureAccessToken: string) {
    const res = await this.request<{ user: any; token: string }>("/auth/azure/login", {
      method: "POST",
      body: JSON.stringify({ access_token: azureAccessToken }),
    });
    this.setToken(res.token);
    return res;
  }

  // Logout (Token Revocation)
  public async logout() {
    await this.request<void>("/auth/logout", { method: "POST" });
    this.setToken(null);
  }

  // Fetch Frontpage Data
  public async getHomeData(date?: string) {
    const query = date ? `?date=${encodeURIComponent(date)}` : "";
    return this.request<ApiResponse<any>>(`/home${query}`);
  }

  // Fetch Just For Today Daily Reading
  public async getJft(date?: string) {
    const query = date ? `?date=${encodeURIComponent(date)}` : "";
    return this.request<ApiResponse<any>>(`/jft${query}`);
  }

  // Fetch Meetings
  public async getMeetings(params?: Record<string, string>) {
    const queryString = params ? "?" + new URLSearchParams(params).toString() : "";
    return this.request<ApiResponse<any[]>>(`/meetings${queryString}`);
  }

  // Submit Public Form
  public async submitPublicForm(slug: string, payload: Record<string, any>) {
    return this.request<ApiResponse<any>>(`/forms/public/${slug}/submit`, {
      method: "POST",
      body: JSON.stringify(payload),
    });
  }
}
```

---

## 7. Automated QA & Testing Suite

The complete REST API is verified by an automated PHPUnit Feature test suite located in `tests/Feature/Api/`:
```bash
php vendor/bin/phpunit tests/Feature/Api/
```

### Coverage by Feature Suite:
1. `AuthApiTest`: Azure OAuth token exchange, user profile, Sanctum logout & token revocation (`POST /auth/logout`).
2. `CalendarEventApiTest`: Public listings, date range expansions, create, update, 204 delete.
3. `ChangeRequestApiTest`: Authenticated submissions with attachments, user isolation, admin status updates, 204 delete.
4. `CustomFormApiTest`: Public slug display, public submissions with data validation, admin CRUD & submissions listing.
5. `DirectOnlineGroupApiTest`: Public listing/detail, authenticated create (201), update (200), delete (204).
6. `WorkgroupApiTest`: Public listing/detail, parent committee role scoping, create (201), update (200), delete (204).
7. `CompositeApiTest`: Consolidated `/home`, `/frontpage`, `/jft`, `/stats`.
8. `MeetingApiTest`: Prioritized URL hierarchy, multi-criteria filtering, full meeting CRUD.
9. `AgendaApiTest`: Group agenda reporting lifecycle and PDF serialization.
10. `DirectoryApiTest`: Geographic directories (`cities`, `neighborhoods`, `days`, `topics`, `options`, `sc-meetings`, `service-bodies`, `service-committees`).
11. `EventApiTest`: Events and announcements lifecycle.
12. `ProtectedManagementApiTest`: `committee-reports`, `contact-us`, `newsletter-members`, `permissions`, `roles`, `transactions`, `users`.

**Verification Status:** 77 tests, 420 assertions, 100% passing.
