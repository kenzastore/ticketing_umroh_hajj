# Track Plan: DASHBOARD-001 Front Page & Navigation

## Tasks

### Phase 1: Setup & Assets
- [x] **Task 1: Setup Dashboard Structure**
    -   **File:** `public/admin/dashboard.php`
    -   **Action:** Refactor the existing dashboard to use a grid layout for the new menu items.
    -   **Tech:** HTML, Bootstrap 5.

- [x] **Task 2: Implement Navigation Menu**
    -   **File:** `public/shared/header.php` (and sidebar if applicable)
    -   **Action:** Update global navigation to match the "FRONT PAGE" items.

### Phase 2: Dashboard Components
- [x] **Task 3: Create Menu Cards**
    -   **File:** `public/admin/dashboard.php`
    -   **Action:** Implement clickable cards for "Booking Request", "Movement", "Payment Report", "Invoice", "Rangkuman", "Payment Advise".
    -   **Details:** Add icons and brief descriptions for each.

- [x] **Task 4: Implement Time Limit Widget**
    -   **File:** `public/admin/dashboard.php`
    -   **Action:** Create a "Time Limit" section/card.
    -   **Logic:** Add a placeholder or basic query to show items due in <= 3 days (mock data if backend logic isn't fully ready, or connect to existing models if possible).

### Phase 3: Review & Polish
- [x] **Task 5: Responsive Verification**
    -   **Action:** Verify layout on mobile vs. desktop views.
    -   **Adjustment:** Ensure touch targets are large enough on mobile.

- [x] **Task 6: Final Integration Check**
    -   **Action:** Ensure all links work and the page loads without errors.
