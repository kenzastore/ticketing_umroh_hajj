# Track Plan: Reminder System Implementation

This plan outlines the implementation of the cron-based reminder system.

## Phase 1: Database & Model
1. **Notifications Table:** Add `notifications` table to `schema.sql`.
   - `id`, `entity_type`, `entity_id`, `message`, `status` (UNREAD/READ), `created_at`.
2. **Notification Model:** Create `app/models/Notification.php`.

## Phase 2: Cron Logic
1. **Reminder Script:** Create `cron/reminder_service.php`.
   - Query `movements` for `ticketing_deadline`.
   - Query `movements` for deposit airline dates.
   - Insert records into `notifications`.

## Phase 3: UI Integration
1. **Header Component:** Update `public/shared/header.php` to show unread notification count.
2. **Alerts Page:** Create `public/admin/notifications.php` to list alerts.

## Steps
- [x] Add `notifications` table to schema and database.
- [x] Create `Notification.php` model.
- [x] Implement `cron/reminder_service.php`.
- [x] Add notification badge to UI header.
- [x] Create notifications list page.
