# Track Spec: DASHBOARD-001 Front Page & Navigation

## Overview
This track implements the main dashboard interface for the Ticketing Umroh & Haji application, aligning it with the `FRONT_PAGE_DASHBOARD.pdf` reference. The goal is to provide a central navigation hub that mimics the structure of the original "FRONT PAGE" worksheet, offering quick access to core modules and a "Time Limit" summary.

## Functional Requirements

### 1. Dashboard Layout
- **Visual Structure:** A clean, card-based or list-based layout (depending on screen size) representing the main modules.
- **Reference:** `conductor/reference/FRONT_PAGE_DASHBOARD.pdf`.
- **Menu Items:**
    1.  **Booking Request** (Links to Request Management)
    2.  **Movement** (Links to Movement Monitoring)
    3.  **Payment Report** (Links to Payment Reporting)
    4.  **Invoice** (Links to Invoice Generation)
    5.  **Rangkuman** (Links to Summary/Admin Dashboard)
    6.  **Payment Advise** (Links to Payment Advise module)
    7.  **Time Limit** (Dashboard widget/section)

### 2. Time Limit Widget
- **Function:** Display upcoming deadlines.
- **Rule:** "Remind 3 days before".
- **Initial Implementation:** A display section showing items with approaching deadlines (dummy data or basic query if models allow).
- **Scope:** Frontend display of the alert logic.

### 3. Responsive Navigation
- **Mobile:** Hamburger menu or accessible list view.
- **Desktop:** Sidebar or top navigation bar + main dashboard grid.

## Technical Requirements
- **File:** `public/admin/dashboard.php` (Update existing).
- **Styling:** Bootstrap 5 (as per Tech Stack).
- **Assets:** Use relevant icons (e.g., FontAwesome or Bootstrap Icons) for each menu item to enhance usability.

## Acceptance Criteria
- [ ] The dashboard displays all 7 specified sections clearly.
- [ ] Navigation links correctly point to their respective module URLs (even if some are placeholders).
- [ ] "Time Limit" section is visible and styled to highlight urgent items.
- [ ] Layout is responsive (usable on mobile and desktop).
