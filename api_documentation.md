# API Documentation

This document provides complete API reference documentation for the backend system endpoints available under `/api/v1/`.

---

## 1. Overview & Base URL

- **Base URL:** `/api/v1`
- **Authentication:** Bearer token via Laravel Sanctum (`Authorization: Bearer <token>`)
- **API Versioning:** All endpoints are versioned under `/api/v1/`
- **Response Formats:** Standard REST JSON responses
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

## 3. Standard Resource Endpoints Matrix

All resource endpoints adhere to Laravel's standard `apiResource` conventions:

| Operation | Method | URI Path | Description |
|---|---|---|---|
| **List** | `GET` | `/api/v1/{resource}` | Retrieve paginated list of items |
| **Create** | `POST` | `/api/v1/{resource}` | Create a new resource item |
| **Show** | `GET` | `/api/v1/{resource}/{id}` | Retrieve specific item details |
| **Update** | `PUT`/`PATCH` | `/api/v1/{resource}/{id}` | Update existing resource item |
| **Delete** | `DELETE` | `/api/v1/{resource}/{id}` | Remove resource item |

---

## 4. Protected Resources (Full Auth Required)

These resources require `auth:sanctum` authentication for **all** actions (`index`, `show`, `store`, `update`, `destroy`).

1. **Committee Reports:** `/api/v1/committee-reports`
2. **Contact Requests:** `/api/v1/contact-requests`
3. **Contact Us Submissions:** `/api/v1/contact-us`
4. **Newsletter Members:** `/api/v1/newsletter-members`
5. **Permissions Management:** `/api/v1/permissions`
6. **Roles Management:** `/api/v1/roles`
7. **Service Body Agendas:** `/api/v1/service-body-agendas`
8. **Financial Transactions:** `/api/v1/transactions`
9. **User Management:** `/api/v1/users`

---

## 5. Publicly Accessible Resources (Read Public, Write Auth Required)

These resources allow unauthenticated access for read operations (`index`, `show`), but require `auth:sanctum` authentication for write operations (`store`, `update`, `destroy`).

1. **Agendas:** `/api/v1/agendas`
2. **Calendar Events:** `/api/v1/calendar-events`
3. **Cities:** `/api/v1/cities`
4. **Days:** `/api/v1/days`
5. **Events:** `/api/v1/events`
6. **Groups:** `/api/v1/groups`
7. **Meetings:** `/api/v1/meetings`
8. **Neighborhoods:** `/api/v1/neighborhoods`
9. **Options:** `/api/v1/options`
10. **Service Committee Meetings:** `/api/v1/sc-meetings`
11. **Service Bodies:** `/api/v1/service-bodies`
12. **Service Committees:** `/api/v1/service-committees`
13. **Topics:** `/api/v1/topics`

---

## 6. Query Parameters & Conventions

- **Pagination:** `?page=1&per_page=15`
- **Filtering & Search:** `?search=query` or field-specific filters where supported
- **Sorting:** `?sort_by=created_at&sort_order=desc`
- **Relations Loading:** `?with=relation1,relation2`
