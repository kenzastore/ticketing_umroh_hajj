# Specification: Enhanced Login Page & Workflow Overview

## 1. Overview
This feature focuses on redesigning the `login.php` page to provide a more professional first impression and immediate clarity on the system's purpose. The new design will feature a split-screen layout that combines a functional login form with a visual business workflow overview and role-based demo credentials.

## 2. Functional Requirements
### 2.1 Split-Screen Layout
- **Left Side (Information Panel):** A visual overview of the business workflow stages (Demand Intake, Operational Execution, Financial Settlement, and Management & Audit).
- **Right Side (Access Panel):** A clean, centered login card with username and password fields.

### 2.2 Workflow Visualization
- Use icons and brief labels to represent the 4 core stages of the Ticketing Umroh & Haji process.
- Use arrows to indicate the flow of data between stages.
- Each stage should mention the responsible role (e.g., "Operational Execution handled by Ticketing Staff").

### 2.3 Demo Credentials Table
- A clear table or list within the Information Panel displaying usernames for each role:
    - **Admin:** `admin_demo`
    - **Operational:** `op_demo`
    - **Finance:** `finance_demo`
    - **Monitor:** `monitor_demo`
- Password `password123` displayed in clear text for all demo roles.

### 2.4 Responsiveness
- The split-screen must be fully responsive.
- On mobile devices, the Information Panel should stack below the Access Panel or be accessible via a toggle.

## 3. UI/UX Design
- **Branding:** Consistent with `product-guidelines.md` (Professional, Trustworthy, and Approachable).
- **Framework:** Utilize Bootstrap 5 for layout and styling.
- **Visual Feedback:** Clear error messages for invalid credentials.

## 4. Acceptance Criteria
- [ ] `login.php` uses a split-screen layout.
- [ ] Workflow stages are visually represented with icons and flow indicators.
- [ ] Demo credentials for all 4 roles are clearly visible.
- [ ] The login form remains fully functional and connects to the existing authentication logic.
- [ ] The page is responsive and looks good on mobile and desktop.

## 5. Out of Scope
- Implementing registration or password recovery flows.
- Real-time workflow status updates on the login page.
