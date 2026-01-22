# Plan: Web-Based Admin Manual Viewer

## Phase 1: Setup & Dependencies [checkpoint: 42015f1]
- [x] Task: Install `erusev/parsedown` using Composer. (Manually installed to includes/Parsedown.php due to composer restrictions) [42015f1]
- [x] Task: Create the basic file structure for `public/shared/manual.php`. [42015f1]
- [x] Task: Conductor - User Manual Verification 'Setup & Dependencies' (Protocol in workflow.md) [42015f1]

## Phase 2: Core Rendering & Link Logic [checkpoint: 42015f1]
- [x] Task: Write unit tests for the Markdown loading and rendering logic. [42015f1]
- [x] Task: Implement file loading and Markdown-to-HTML rendering in `manual.php`. [42015f1]
- [x] Task: Write tests for the automatic link rewriting functionality. [42015f1]
- [x] Task: Implement regex-based link rewriting to convert `.md` links to `manual.php?page=...` parameters. [42015f1]
- [x] Task: Conductor - User Manual Verification 'Core Rendering & Link Logic' (Protocol in workflow.md) [42015f1]

## Phase 3: UI & Navigation [checkpoint: 42015f1]
- [x] Task: Write tests for the dynamic Table of Contents (ToC) generation.
- [x] Task: Implement the ToC sidebar by parsing the directory or `index.md`.
- [x] Task: Integrate the viewer into the Admin layout using Bootstrap 5 and `public/shared/header.php`.
- [~] Task: Conductor - User Manual Verification 'UI & Navigation' (Protocol in workflow.md)

## Phase 4: Integration & Security [checkpoint: 42015f1]
- [x] Task: Add the "User Manual" link to the "Masters" dropdown in `public/shared/header.php`.
- [x] Task: Implement and verify `check_auth('admin')` protection on the manual viewer page.
- [x] Task: Write an integration test to ensure non-admin users are redirected or denied access.
- [~] Task: Conductor - User Manual Verification 'Integration & Security' (Protocol in workflow.md)

## Phase 5: Final Validation [checkpoint: 42015f1]
- [x] Task: Perform a full end-to-end manual walk-through of the web manual. [42015f1]
- [x] Task: Verify that all internal links between stages (1-4) function correctly. [42015f1]
- [x] Task: Conductor - User Manual Verification 'Final Validation' (Protocol in workflow.md) [42015f1]
