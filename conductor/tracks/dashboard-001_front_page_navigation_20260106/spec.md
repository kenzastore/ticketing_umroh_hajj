# Track Spec: DASHBOARD-001 Front Page & Navigation

## Overview
This track implements the main dashboard interface for the Ticketing Umroh & Haji application, aligning it with the `FRONT_PAGE_DASHBOARD.pdf` reference. The goal is to provide a central navigation hub that mimics the structure of the original "FRONT PAGE" worksheet, offering quick access to core modules and a "Time Limit" summary.

## Functional Requirements

### 1. Dashboard Layout
- **Visual Structure:** A modern grid-based layout using Bootstrap 5 cards.
- **Card Design:** 
    - **Compact Layout:** Use small-sized buttons for primary actions instead of large clickable areas.
    - **Visual Hierarchy:** Distinct icons for each card, positioned prominently.
    - **Descriptions:** Concise 1-2 sentence descriptions for each functionality.
    - **Interactivity:** Consistent styling with subtle hover effects and uniform dimensions.
- **Reference:** `conductor/reference/FRONT_PAGE_DASHBOARD.pdf`.
- **Menu Items (6 Cards):**
    1.  **Booking Request:** Manage incoming demands and passenger details.
    2.  **Movement:** Track active group movements and flight segments.
    3.  **Payment Report:** View and manage Sales vs. Cost financial reports.
    4.  **Invoice:** Generate and track proforma invoices for agents.
    5.  **Rangkuman:** High-level summary and management dashboard.
    6.  **Payment Advise:** Process and track internal airline payments.
- **Accessibility:** Ensure ARIA labels, semantic HTML, keyboard navigability, and high contrast ratios.

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
