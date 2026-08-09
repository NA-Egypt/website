# Project AGENTS Guidelines

## API & Architecture Rules
1. **API Versioning:** All REST API endpoints must be versioned under `/api/v1/` prefix in `routes/api.php`.
2. **Mass-Assignment Security:** Do not pass `$request->all()` directly to Eloquent models. Always perform explicit validation using `$request->validate([...])` or custom FormRequest classes.
3. **HTTP Status Code Consistency:**
   - Resource creation (`store()`) returns `201 Created`.
   - Read/update operations return `200 OK`.
   - Resource deletions return `204 No Content`.
   - Validation failures return `422 Unprocessable Content`.
4. **Pagination & Query Optimization:** Avoid returning unpaginated `Model::all()` collections for large datasets. Always eager-load relationships (`with(...)`) to prevent N+1 performance bottlenecks.
5. **Resource Naming:** Use plural nouns for REST endpoints (e.g. `/api/v1/contact-requests`, `/api/v1/calendar-events`).
