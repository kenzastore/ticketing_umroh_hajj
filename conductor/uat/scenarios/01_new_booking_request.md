# UAT Scenario: New Booking Request

## Scenario ID: UAT-REQ-01
**Module:** Request Management
**Role:** Operational Staff / Ticketing
**Priority:** High

## Description
Validate the process of creating a new booking request, including multi-segment flight details, agent selection, and form validation.

## Prerequisites
- User is logged in as Operational Staff or Admin.
- Master data for Agents and Corporates exists in the system.

## Test Steps
| Step | Action | Expected Outcome |
|------|--------|------------------|
| 1    | Navigate to "New Request" page. | New Request form is displayed. |
| 2    | Submit the form without filling any fields. | Validation errors are displayed for mandatory fields (Corporate, Agent, Pax, segments). |
| 3    | Select a Corporate and Agent from the dropdown. | Selection is successful. |
| 4    | Enter Group Size (Pax) and Duration. | Values are accepted. |
| 5    | Fill in 2 segments of flight details (Date, Flight No, Sector). | Flight details are entered correctly. |
| 6    | Click "Save Request". | System saves the request and redirects to Request List or Detail page. |
| 7    | Verify the new request appears in the Request List with "Pending" status. | Request is visible with correct data. |

## Expected Result
A new booking request is successfully created with multi-segment flight data and is visible in the list.

## Notes
- Ensure multi-segment logic allows at least 4 segments as per worksheet.
- TTL (Time To Live) should be calculated or entered as per business rules.
