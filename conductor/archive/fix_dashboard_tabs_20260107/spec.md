# Track Spec: Fix Dashboard Tab Interactivity

## Overview
This track addresses a bug on the Admin Dashboard where the "Urgent Deadlines" widget tabs (DP1, DP2, FP) are unclickable. Users report that the cursor does not change to a pointer when hovering over these tabs, suggesting they are being obscured by another layout element.

## Functional Requirements
- **Tab Interactivity:** All tabs (Ticketing, DP1, DP2, FP) in the Urgent Deadlines widget must be clickable and switch to their respective content.
- **Visual Feedback:** Hovering over any interactive tab must display the standard `pointer` cursor.
- **Content Accuracy:** Ensure each tab displays the correct filtered data once the switching is fixed.

## Technical Requirements
- **HTML/CSS Investigation:** Identify any elements (like overlapping rows or absolute positioned divs) that are covering the tab navigation area.
- **Bootstrap 5 Compliance:** Verify the `nav-tabs` and `tab-content` structure follows the standard Bootstrap 5.3 pattern.
- **Model Integrity:** Ensure `Movement::getDeadlinesByCategory` is returning valid data for all tabs.

## Acceptance Criteria
- [ ] Tabs switch correctly on click.
- [ ] Hover states (cursor: pointer) are restored for all tab headers.
- [ ] No regression in other dashboard functionalities (Menu Grid, etc.).
