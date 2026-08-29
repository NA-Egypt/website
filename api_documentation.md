# API Documentation

This document provides complete API reference documentation for the backend system endpoints available under `/api/v1/`.

---

## 1. Overview & Base URL

- **Base URL:** `/api/v1`
- **Authentication:** Bearer token via Laravel Sanctum (`Authorization: Bearer <token>`)
- **API Versioning:** All endpoints are versioned under `/api/v1/`
- **Response Formats:** Standard REST JSON responses (`{ "data": ... }`)
- **HTTP Status Codes:**
  - `200 OK`: Successful retrieval / update
  - `201 Created`: Resource successfully created
  - `204 No Content`: Resource successfully deleted
  - `401 Unauthorized`: Missing or invalid authentication token
  - `403 Forbidden`: Insufficient permissions
  - `404 Not Found`: Resource not found
  - `422 Unprocessable Content`: Validation errors

---

## 2. Authentication Endpoints

### 2.1 Azure AD / OAuth Login
Authenticate using Azure AD tokens to obtain a Sanctum Bearer Token.

- **Endpoint:** `POST /api/v1/auth/azure/login` (Alias: `POST /api/v1/login/azure`)
- **Access:** Public
- **Headers:** `Content-Type: application/json`
- **Request Body:**
  ```json
  {
    "access_token": "string (required)"
  }
  ```
- **Response (200 OK):**
  ```json
  {
    "token": "string",
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com"
    }
  }
  ```

---

### 2.2 Current Authenticated User Profile
Retrieve the authenticated user's profile information.

- **Endpoint:** `GET /api/v1/user`
- **Access:** Protected (`auth:sanctum`)
- **Headers:** `Authorization: Bearer <token>`
- **Response (200 OK):**
  ```json
  {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com",
    "roles": [...],
    "permissions": [...]
  }
  ```

---

## 3. Frontpage & Content Endpoints (Public)

### 3.1 Frontpage Composite Data (`GET /api/v1/home` or `/api/v1/frontpage`)
Returns all aggregated data required for rendering the homepage in a single round-trip (statistics, daily spiritual reading, helpline contact cards, official social links, and upcoming featured calendar events). Cached for high performance.

- **Endpoint:** `GET /api/v1/home` (Alias: `GET /api/v1/frontpage`)
- **Access:** Public
- **Query Parameters:**
  - `date` *(optional, string `YYYY-MM-DD`)*: Retrieve reading and event context for a specific date.
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
        "quote": "...",
        "quote_source": "النص الأساسي - ص.42",
        "content": [
          "الفقرة الأولى من التأمل...",
          "الفقرة الثانية من التأمل..."
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
      "upcoming_events": []
    }
  }
  ```

---

### 3.2 Just For Today Daily Reading (`GET /api/v1/jft`)
Retrieves the structured daily reading from the "Just For Today" NA literature archive.

- **Endpoint:** `GET /api/v1/jft`
- **Access:** Public
- **Query Parameters:**
  - `date` *(optional, string `YYYY-MM-DD`)*: Date of the reading. Defaults to current date in Egypt (`Africa/Cairo`).
- **Response (200 OK):**
  ```json
  {
    "data": {
      "date": "2026-01-10",
      "page_date": "10 يناير",
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
      "upcoming_events": 2
    }
  }
  ```

---

## 4. Standard Resource Endpoints Matrix

All resource endpoints adhere to Laravel's standard `apiResource` conventions:

| Operation | Method | URI Path | Description |
|---|---|---|---|
| **List** | `GET` | `/api/v1/{resource}` | Retrieve paginated list of items |
| **Create** | `POST` | `/api/v1/{resource}` | Create a new resource item |
| **Show** | `GET` | `/api/v1/{resource}/{id}` | Retrieve specific item details |
| **Update** | `PUT`/`PATCH` | `/api/v1/{resource}/{id}` | Update existing resource item |
| **Delete** | `DELETE` | `/api/v1/{resource}/{id}` | Remove resource item |

---

## 5. Protected Resources (Full Auth Required)

These resources require `auth:sanctum` authentication for **all** actions (`index`, `show`, `store`, `update`, `destroy`):

1. **Committee Reports:** `/api/v1/committee-reports`
2. **Contact Requests:** `/api/v1/contact-requests`
3. **Contact Us Submissions:** `/api/v1/contact-us`
4. **Newsletter Members:** `/api/v1/newsletter-members`
5. **Permissions Management:** `/api/v1/permissions`
6. **Roles Management:** `/api/v1/roles`
7. **Financial Transactions:** `/api/v1/transactions`
8. **User Management:** `/api/v1/users`

---

## 6. Publicly Accessible Resources (Read Public, Write Auth Required)

These resources allow unauthenticated access for read operations (`index`, `show`), but require `auth:sanctum` authentication for write operations (`store`, `update`, `destroy`):

### 6.1 Calendar Events (`/api/v1/calendar-events`)

Rich calendar events supporting date-range recurrence expansion, color coding, and featured flags.

- `GET /api/v1/calendar-events`
  - **Query Params:** `start` (ISO-8601 date string), `end` (ISO-8601 date string)
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
- `GET /api/v1/calendar-events/{id}`
- `POST /api/v1/calendar-events` *(Auth)* -> Returns `201 Created`
  - **Body Schema:** `title` (required|string), `start` (required|date), `end` (required|date|after_or_equal:start), `description` (nullable|string), `color` (nullable|string), `organizer` (nullable|string), `location` (nullable|string), `recurrence` (nullable|array), `is_featured` (nullable|boolean)
- `PUT /api/v1/calendar-events/{id}` *(Auth)* -> Returns `200 OK` (supports partial updates)
- `DELETE /api/v1/calendar-events/{id}` *(Auth)* -> Returns `200 OK` (`{"message": "Deleted successfully"}`)

---

### 6.2 Announcements & Events (`/api/v1/events`)

Announcements linked to specific service bodies and days of the week.

- `GET /api/v1/events`
  - **Response (200 OK):**
    ```json
    {
      "data": [
        {
          "id": 1,
          "name": "Unity Convention",
          "description": "Annual convention gathering",
          "date": "2026-10-15",
          "service_body_id": 1,
          "service_body": {
            "id": 1,
            "ar_name": "لجنة مصر",
            "en_name": "Egypt RSC"
          },
          "day_id": 1,
          "day": {
            "id": 1,
            "ar_name": "السبت",
            "en_name": "Saturday"
          },
          "created_at": "2026-08-20T00:00:00.000000Z",
          "updated_at": "2026-08-20T00:00:00.000000Z"
        }
      ]
    }
    ```
- `GET /api/v1/events/{id}`
- `POST /api/v1/events` *(Auth)* -> Returns `201 Created`
  - **Body Schema:** `name` (required|string), `description` (required|string), `date` (required|date), `service_body_id` (required|exists:service_bodies,id), `day_id` (required|exists:days,id)
- `PUT /api/v1/events/{id}` *(Auth)* -> Returns `200 OK` (supports partial updates)
- `DELETE /api/v1/events/{id}` *(Auth)* -> Returns `204 No Content`

---

### 6.3 Other Public Directory & Data Resources

1. **Agendas:** `/api/v1/agendas`
2. **Cities:** `/api/v1/cities`
3. **Days:** `/api/v1/days`
4. **Groups:** `/api/v1/groups`
5. **Meetings:** `/api/v1/meetings`
6. **Neighborhoods:** `/api/v1/neighborhoods`
7. **Options:** `/api/v1/options`
8. **Service Committee Meetings:** `/api/v1/sc-meetings`
9. **Service Bodies:** `/api/v1/service-bodies`
10. **Service Body Agendas:** `/api/v1/service-body-agendas` *(Public sees released agendas; authenticated members see scoped drafts)*
11. **Service Committees:** `/api/v1/service-committees`
12. **Topics:** `/api/v1/topics`

---

## 7. Query Parameters & Conventions

- **Pagination:** `?page=1&per_page=15`
- **Filtering & Search:** `?search=query` or field-specific filters where supported
- **Sorting:** `?sort_by=created_at&sort_order=desc`
- **Relations Loading:** `?with=relation1,relation2`

