# Specification: Minimalist & User-Friendly Login Page

## 1. Overview
This feature focuses on redesigning the `login.php` page to be as simple, clean, and user-friendly as possible while still providing necessary context for the system's business process and demo access. The new design will use a centered minimalist card layout with an integrated workflow overview.

## 2. Functional Requirements
### 2.1 Minimalist Login Card
- **Layout:** A clean, centered card on a neutral background.
- **Form Fields:** Username and Password fields with clear labels and internal icons (e.g., `fas fa-user`, `fas fa-lock`).
- **User-Friendly Features:**
    - Helpful placeholders (e.g., "admin_demo").
    - A password visibility toggle (eye icon).
    - Clear Bootstrap alerts for validation errors (empty fields, invalid credentials).

### 2.2 Integrated Workflow Accordion
- **Placement:** Within the login card, below the login button.
- **Structure:** A Bootstrap accordion labeled "How the system works".
- **Content:** A 4-stage process flow:
    1. **Demand Intake:** Input requests (Role: Operational).
    2. **Operational Execution:** Manage PNR/Movement (Role: Operational/Admin).
    3. **Financial Settlement:** Invoices & Payments (Role: Finance).
    4. **Management & Audit:** Deadlines & Logs (Role: Admin).
- **Visuals:** Simple icons and short, one-sentence summaries for each stage.

### 2.3 Distinct Credentials Footer
- **Placement:** A separate, visually distinct area at the bottom of the page.
- **Content:** A clear table listing demo accounts:
    - Admin: `admin_demo`
    - Operational: `op_demo`
    - Finance: `finance_demo`
    - Monitor: `monitor_demo`
    - All passwords: `password123`.

## 3. UI/UX Design
- **Branding:** Consistent with `product-guidelines.md` (Professional, Trustworthy, and Approachable).
- **Framework:** Bootstrap 5.
- **Responsiveness:** Ensure the centered card and footer adapt elegantly to mobile and tablet screens.

## 4. Acceptance Criteria
- [ ] `login.php` displays a centered minimalist card.
- [ ] The login form is fully functional with error alerts and icons.
- [ ] The "How the system works" accordion is present and informative.
- [ ] Demo credentials are listed clearly at the bottom of the page.
- [ ] The password toggle works as expected.
- [ ] The layout is responsive across devices.

## 5. Out of Scope
- Password recovery or registration flows.
- Multi-language support for the login page.
