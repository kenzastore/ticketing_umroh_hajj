# Specification: Web-Based Admin Manual Viewer

## 1. Overview
This feature implements a web-based interface for viewing the system's documentation (located in `docs/manual/`). The viewer will dynamically render Markdown files into HTML, providing a seamless reading experience for Administrator users directly within the application.

## 2. Functional Requirements
### 2.1 Admin-Only Access
- The viewer must be protected by Role-Based Access Control (RBAC).
- Only users with the `admin` role can access the manual pages.
- Access check must be performed on every request to `public/shared/manual.php`.

### 2.2 Dynamic Markdown Rendering
- Use a PHP Markdown library (e.g., `erusev/parsedown`) to convert `.md` files from `docs/manual/` to HTML on-the-fly.
- The viewer should load `docs/manual/index.md` by default.

### 2.3 Navigation & Table of Contents
- A Table of Contents (ToC) sidebar must be displayed on all manual pages.
- The ToC should be dynamically generated based on the files available in `docs/manual/` or by parsing the main `index.md`.
- Integration: A "User Manual" link will be added to the "Masters" dropdown in the global header for Admin users.

### 2.4 Internal Link Rewriting
- Implement logic to automatically rewrite internal Markdown links (e.g., `[Stage 1](./stage1_demand_intake.md)`) into web-friendly URLs (e.g., `manual.php?page=stage1_demand_intake`).

### 2.5 UI/UX Design
- The layout should be consistent with the existing Admin dashboard using Bootstrap 5.
- Main area for content, sidebar for the Table of Contents.

## 3. Technical Constraints
- **Location:** The viewer script will be located at `public/shared/manual.php`.
- **Backend:** Native PHP with server-side rendering.
- **Dependency:** Must include a Markdown parser library via Composer (or a single-file version if vendor autoloading is restricted).

## 4. Acceptance Criteria
- [ ] Only Admin users can access the manual via the "Masters" dropdown.
- [ ] Markdown files from `docs/manual/` are correctly rendered as HTML.
- [ ] The Table of Contents allows switching between different manual stages.
- [ ] Links between manual pages work correctly within the web interface.
- [ ] The UI follows the project's design guidelines and is responsive.

## 5. Out of Scope
- Editing the manual files through the web interface.
- Search functionality within the manual content (Phase 1).
- Exporting to PDF from the web view.
