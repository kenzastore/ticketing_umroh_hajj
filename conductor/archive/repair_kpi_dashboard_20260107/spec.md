# Track Spec: Repair and Enhance Dashboard KPI Cards

## Overview
This track focuses on repairing and expanding the "Time Limit" KPI card on the admin dashboard (`public/admin/dashboard.php`). It transforms the single-purpose "Ticketing Deadline" widget into a multi-functional "Urgent Deadlines" hub that tracks Ticketing, DP1, DP2, and Full Payment deadlines using a tabbed interface.

## Functional Requirements

### 1. Data Retrieval Enhancements
- **Multi-Category Tracking:** Update the backend logic to fetch four types of urgent deadlines:
    - **Ticketing:** `ticketing_deadline` (where `ticketing_done = 0`).
    - **DP1:** `deposit1_airlines_date` or `deposit1_eemw_date` (where `dp1_status != 'PAID'`).
    - **DP2:** `deposit2_airlines_date` or `deposit2_eemw_date` (where `dp2_status != 'PAID'`).
    - **FP:** `fullpay_airlines_date` or `fullpay_eemw_date` (where `fp_status != 'PAID'`).
- **Inclusion of Past Due:** Modify queries to include items where the deadline has already passed but the status remains incomplete.
- **Sorting:** Prioritize past-due items at the top of each list.

### 2. Dashboard UI Updates
- **Tabbed Interface:** Replace the static list with Bootstrap 5 Navigation Tabs.
- **Visual Highlighting:** 
    - Use a high-contrast style (e.g., text-danger or bold background) for items that are past due.
    - Maintain the "remind 3 days before" logic for upcoming items.
- **Interactivity:** Wrap list items in anchor tags linking to `edit_movement.php?id=[ID]`.
- **Empty States:** Display "No urgent deadlines" for each tab if no items match the criteria.

## Technical Requirements
- **Model:** Update `app/models/Movement.php` to provide comprehensive deadline data.
- **View:** Refactor `public/admin/dashboard.php` to implement the tabbed UI using Bootstrap 5 components.

## Acceptance Criteria
- [ ] The dashboard widget displays four distinct tabs for different deadline types.
- [ ] Each tab correctly lists items due within the next 3 days.
- [ ] Items with passed deadlines (past due) are listed at the top and visually highlighted.
- [ ] Clicking any item in the list redirects the user to the correct movement's edit page.
- [ ] The widget correctly displays an empty state message when no deadlines are found for a category.
