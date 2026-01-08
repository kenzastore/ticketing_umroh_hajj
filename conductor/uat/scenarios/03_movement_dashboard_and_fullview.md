# UAT Scenario: Movement Dashboard & FullView

## Scenario ID: UAT-MOV-02
**Module:** Movement Monitoring
**Role:** All Roles (Admin, Finance, Ops, Monitor)
**Priority:** High

## Description
Validate the Movement Dashboard functionality, including data accuracy, filtering, and the special "FullView" display mode used for monitoring.

## Prerequisites
- User is logged in.
- Multiple movements exist in the system across different dates, agents, and carriers.

## Test Steps
| Step | Action | Expected Outcome |
|------|--------|------------------|
| 1    | Navigate to "Movement Dashboard". | Dashboard is displayed with a list of active movements. |
| 2    | Apply a filter by Carrier (e.g., "Saudi Arabian"). | List updates to show only movements for the selected carrier. |
| 3    | Apply a filter by Date Range or Agent. | List updates correctly. |
| 4    | Clear filters. | List returns to showing all active movements. |
| 5    | Click on a "FullView" or "Monitor View" link. | A clean, high-visibility dashboard is displayed (suitable for TV display). |
| 6    | Verify that the FullView display automatically refreshes or shows live data. | Data is accurate and matches the main dashboard. |
| 7    | Verify that status indicators (e.g., Ticketing Done, FP status) are clearly visible. | Indicators are intuitive and correct. |

## Expected Result
The dashboard provides accurate, filterable data and a clear "FullView" display mode that meets the operational needs of monitoring flight movements.

## Notes
- "FullView" is a specific requirement from the proposal for TV-mode monitoring.
- Ensure the mobile responsiveness of the main dashboard is also briefly checked during this scenario.
