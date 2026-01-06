# Track Spec: Reminder System Implementation

## Overview
Develop a notification system that monitors critical time limits (Ticketing Deadlines, Payment Due Dates) and alerts the relevant staff. This system will be driven by a background cron job.

## Objectives
1. **Define Notification Schema:** Ensure we have a place to store alerts (e.g., `notifications` table).
2. **Develop Reminder Logic:** Scan `movements` and `invoices` for upcoming deadlines.
3. **Cron Script:** Create a standalone PHP script (`cron/reminder_checker.php`) to be executed periodically.
4. **Dashboard View:** Add a notification area or "Alerts" section to the Admin/Finance dashboards.

## Logic Rules (Worksheet Based)
- **Manifest/Ticketing:** Alert 3 days before `ticketing_deadline`.
- **Payment DP1/DP2:** Alert 2 days before airline deposit dates.
- **Full Payment:** Alert if `ticketing_done` is 1 but `fp_status` is not 'PAID'.

## Acceptance Criteria
- [ ] Cron script correctly identifies expiring deadlines.
- [ ] Notifications are stored in the database.
- [ ] Staff can see active alerts on the dashboard.
