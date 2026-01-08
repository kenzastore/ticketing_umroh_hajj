# UAT Scenario: Time Limit Reminders

## Scenario ID: UAT-NOT-01
**Module:** Notification System / Time Limit
**Role:** All Roles (Admin, Finance, Ops)
**Priority:** High

## Description
Validate the automated reminder system for flight time limits (Manifest & Ticketing), specifically the H-3 reminder logic.

## Prerequisites
- User is logged in.
- System has movements with Time Limit dates set to exactly 3 days from today.
- Notification service (cron) has been executed or triggered.

## Test Steps
| Step | Action | Expected Outcome |
|------|--------|------------------|
| 1    | Navigate to "Notifications" or "Dashboard Reminders". | List of active notifications is displayed. |
| 2    | Verify that movements with a Time Limit in 3 days are listed in the "Time Limit" section. | Correct movements are identified. |
| 3    | Verify that a "Reminder" notification was generated for these movements. | Notification exists in the system. |
| 4    | (If applicable) Check if an email or system alert was triggered for the H-3 deadline. | Alert is confirmed. |
| 5    | Change a Movement's Time Limit to be 5 days away. | Movement should NO LONGER trigger the H-3 reminder (unless it's a general reminder). |
| 6    | Mark a Time Limit task as "Done" in the Movement dashboard. | Reminder for that specific movement should disappear or be marked as read. |

## Expected Result
The system correctly identifies and notifies users of movements approaching their time limit (3 days before), helping to prevent missed deadlines.

## Notes
- H-3 is the specific business requirement from the worksheet.
- Ensure the "Time Limit" section on the front page/dashboard reflects these urgent items.
