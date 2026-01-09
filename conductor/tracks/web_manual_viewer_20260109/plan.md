# Plan: Web-Based Admin Manual Viewer

## Phase 1: Setup & Dependencies
- [x] Task: Install `erusev/parsedown` using Composer. (Manually installed to includes/Parsedown.php due to composer restrictions)
- [x] Task: Create the basic file structure for `public/shared/manual.php`.
- [~] Task: Conductor - User Manual Verification 'Setup & Dependencies' (Protocol in workflow.md)

## Phase 2: Core Rendering & Link Logic
- [x] Task: Write unit tests for the Markdown loading and rendering logic.
- [x] Task: Implement file loading and Markdown-to-HTML rendering in `manual.php`.
- [x] Task: Write tests for the automatic link rewriting functionality.
- [x] Task: Implement regex-based link rewriting to convert `.md` links to `manual.php?page=...` parameters.
- [~] Task: Conductor - User Manual Verification 'Core Rendering & Link Logic' (Protocol in workflow.md)

## Phase 3: UI & Navigation
- [ ] Task: Write tests for the dynamic Table of Contents (ToC) generation.
- [ ] Task: Implement the ToC sidebar by parsing the directory or `index.md`.
- [ ] Task: Integrate the viewer into the Admin layout using Bootstrap 5 and `public/shared/header.php`.
- [ ] Task: Conductor - User Manual Verification 'UI & Navigation' (Protocol in workflow.md)

## Phase 4: Integration & Security
- [ ] Task: Add the "User Manual" link to the "Masters" dropdown in `public/shared/header.php`.
- [ ] Task: Implement and verify `check_auth('admin')` protection on the manual viewer page.
- [ ] Task: Write an integration test to ensure non-admin users are redirected or denied access.
- [ ] Task: Conductor - User Manual Verification 'Integration & Security' (Protocol in workflow.md)

## Phase 5: Final Validation
- [ ] Task: Perform a full end-to-end manual walk-through of the web manual.
- [ ] Task: Verify that all internal links between stages (1-4) function correctly.
- [ ] Task: Conductor - User Manual Verification 'Final Validation' (Protocol in workflow.md)
