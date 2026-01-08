# Track Plan: NOTIF-001 Reminder System (H-3) & Notification Hub

## Tasks

### Phase 1: Background Service (Cron)
- [x] **Task 1: Update Reminder Service Logic**
    -   **File:** `cron/reminder_service.php`
    -   **Action:** Refactor to check all H-3 dates (Ticketing, DP1, DP2, FP).
    -   **Action:** Implement unread-check to avoid duplicates.

- [x] **Task 2: Model Enhancements**
    -   **File:** `app/models/Notification.php`
    -   **Action:** Add `existsUnread($entityType, $entityId, $messagePrefix)` method.
    -   **Action:** Add `markAllAsRead()` method.

### Phase 2: User Interface
- [x] **Task 3: Implement Notification Hub**
    -   **File:** `public/admin/notifications.php`
    -   **Action:** Create the UI to list notifications with filtering and actions.

- [x] **Task 4: Add Action Handlers**
    -   **File:** `public/admin/notification_process.php` (New)
    -   **Action:** Handle marking individual or all notifications as read.

### Phase 3: Final Integration
- [x] **Task 5: Header Linkage**
    -   **File:** `public/shared/header.php`
    -   **Action:** Ensure the bell icon links to the Hub.

- [x] **Task 6: Final Verification**
    -   **Action:** Seed items due in 3 days, run cron, and verify visibility in Hub.
