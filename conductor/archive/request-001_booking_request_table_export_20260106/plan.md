# Track Plan: REQUEST-001 Booking Request Table & Export

## Tasks

### Phase 1: Data Access & Logic
- [x] **Task 1: Enhance BookingRequest::readAll**
    -   **File:** `app/models/BookingRequest.php`
    -   **Action:** Update the `readAll` method (or create a new `readAllWithLegs`) to fetch associated flight legs.
    -   **Strategy:** Perform a `LEFT JOIN` on `booking_request_legs` and aggregate the results, or fetch all legs in a second query and map them in PHP to avoid row duplication issues with LIMIT/OFFSET in the future.
    -   **Test:** Create a test case asserting that legs are correctly attached to the request objects.

### Phase 2: UI Implementation
- [x] **Task 2: Refactor Table View**
    -   **File:** `public/admin/booking_requests.php`
    -   **Action:** Update the HTML table structure to match `booking_request.pdf`.
    -   **Details:** Add columns for Legs 1-4, TCP, Duration, Add1, TTL. Implement horizontal scrolling container (`.table-responsive`).

- [x] **Task 3: Implement Excel Export**
    -   **File:** `public/admin/export_requests.php` (New file)
    -   **Action:** Create a script that generates an Excel-compatible file (headers `Content-Type: application/vnd.ms-excel`) with the same table layout.

### Phase 3: Integration
- [x] **Task 4: Add Email UI Action**
    -   **File:** `public/admin/booking_requests.php`
    -   **Action:** Add an "Email" button/icon to the Actions column (or a separate column). Link it to `mailto:` or a placeholder script `email_request.php`.

- [x] **Task 5: Final Verification**
    -   **Action:** Verify the table against the PDF reference visually and check the export file content.
