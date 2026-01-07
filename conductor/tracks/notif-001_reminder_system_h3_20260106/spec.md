# Track Spec: NOTIF-001 Reminder System (H-3) & Notification Hub

## Overview
This track enhances the background notification system to proactively alert staff about upcoming deadlines. It focuses on H-3 (3 days before) reminders for all critical ticketing and financial events defined in the worksheet.

## Functional Requirements

### 1. Automated Reminders (Cron)
- **H-3 Ticketing:** Alert when `ticketing_deadline` is in 3 days and `ticketing_done` is 0.
- **H-3 Deposit 1:** Alert when `deposit1_airlines_date` or `deposit1_eemw_date` is in 3 days and status is not PAID.
- **H-3 Deposit 2:** Alert when `deposit2_airlines_date` or `deposit2_eemw_date` is in 3 days and status is not PAID.
- **H-3 Full Payment:** Alert when `fullpay_airlines_date` or `fullpay_eemw_date` is in 3 days and status is not PAID.
- **De-duplication:** Ensure only one unread notification exists per entity/event type to prevent flooding.

### 2. Notification Hub
- **List View:** Display all notifications in a central feed (`public/admin/notifications.php`).
- **Grouping:** Group by `alert_type` (DEADLINE, PAYMENT, SYSTEM).
- **Actions:**
    - Mark as Read (individually or "Mark All").
    - Link to relevant Movement/Request detail.
- **Visuals:** High-contrast icons for urgent deadlines.

### 3. Navigation
- Update the notification bell in the header to link to the Hub and show the unread count accurately.

## Technical Requirements
- **Service:** Update `cron/reminder_service.php`.
- **Model:** Enhance `app/models/Notification.php` with `markAllAsRead` and existence checks.
- **UI:** PHP with Bootstrap 5.

## Acceptance Criteria
- [ ] Running the reminder service generates notifications for items due in exactly 3 days.
- [ ] No duplicate unread notifications are created for the same event.
- [ ] The Notification Hub lists all generated alerts correctly.
- [ ] "Mark as Read" updates the database and the header badge count.
- [ ] Clicking a notification redirects to the correct detail page.
