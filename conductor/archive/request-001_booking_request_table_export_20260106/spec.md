# Track Spec: REQUEST-001 Booking Request Table & Export

## Overview
This track updates the Booking Request list view to match the layout defined in `booking_request.pdf`. It includes displaying multi-leg flight details inline, adding export capabilities, and providing a UI for email actions.

## Functional Requirements

### 1. Table Layout
- **Reference:** `booking_request.pdf`
- **Columns:**
    -   NO (Row Number or Request No)
    -   CORPORATE NAME
    -   Agent Name
    -   Skyagent ID
    -   **FLIGHT LEG 1** (Date, Flight No, Sector)
    -   **FLIGHT LEG 2** (Date, Flight No, Sector)
    -   **FLIGHT LEG 3** (Date, Flight No, Sector)
    -   **FLIGHT LEG 4** (Date, Flight No, Sector)
    -   Group Size
    -   TCP
    -   DURATION
    -   ADD 1
    -   TTL DAYS

### 2. Export Functionality
- **Format:** HTML-based Excel download (`.xls`) to support coloring and merging if possible, or standard CSV.
- **Content:** Exact replica of the table view.
- **Trigger:** Button "Export to Excel" above the table.

### 3. Email UI
- **Action:** A button or link "Email" for each row (or a bulk action).
- **Scope:** This track only implements the UI/Trigger. The actual email sending logic (SMTP integration) is separate but the hook must be present.

## Technical Requirements
- **Model Update:** `BookingRequest::readAll()` needs to fetch leg data efficiently. A `JOIN` or eager loading strategy is required to get up to 4 legs per request in a single result set or efficient loop.
- **Styling:** Use Bootstrap tables.
- **Leg Display:** 
    -   Each leg column group (Date/Flight/Sector) should use a distinct sub-header color as per the PDF (Yellow background in header).
    -   Handle variable numbers of legs (1 to 4). Empty legs should display as empty cells.

## Acceptance Criteria
- [ ] Table columns match the PDF reference order and content.
- [ ] Flight legs (1-4) are correctly populated from the database.
- [ ] "Export to Excel" downloads a file containing the table data.
- [ ] UI is responsive enough to scroll horizontally for wide tables.
