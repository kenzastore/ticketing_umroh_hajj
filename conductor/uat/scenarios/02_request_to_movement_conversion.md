# UAT Scenario: Request to Movement Conversion

## Scenario ID: UAT-MOV-01
**Module:** Movement Monitoring
**Role:** Admin / Operational Staff
**Priority:** High

## Description
Validate the process of converting a pending booking request into an active movement by assigning a PNR and Tour Code.

## Prerequisites
- User is logged in as Admin or Operational Staff.
- At least one "Pending" booking request exists in the system.

## Test Steps
| Step | Action | Expected Outcome |
|------|--------|------------------|
| 1    | Navigate to "Booking Requests" list. | List of requests is displayed. |
| 2    | Find a "Pending" request and click "Convert to Movement" (or "Process"). | Conversion form/page is displayed with pre-filled request data. |
| 3    | Enter a PNR and Tour Code. | Values are accepted. |
| 4    | Confirm flight details and dates (adjust if necessary). | Data is verified. |
| 5    | Click "Save Movement". | System converts the request and redirects to Movement Dashboard or Detail page. |
| 6    | Verify the request status changed to "Processed" or "Active". | Status is updated correctly. |
| 7    | Verify the new entry exists in the Movement Dashboard with correct PNR and Tour Code. | Entry is visible in Movement. |

## Expected Result
The booking request is successfully converted into a movement, and all details (PNR, Tour Code, flights) are correctly reflected in the Movement module.

## Notes
- This is a critical step that links the "Demand" (Request) to "Execution" (Movement).
- Ensure the original request is no longer editable once converted (or reflects its processed state).
