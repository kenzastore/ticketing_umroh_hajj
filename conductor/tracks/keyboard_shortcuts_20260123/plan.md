# Implementation Plan: Keyboard Shortcut System

## Phase 1: Foundation & Global Infrastructure
- [x] Task: Create `public/assets/js/shortcut-system.js` with basic initialization logic. 47f8630
- [x] Task: Implement the Centralized Shortcut Registry (JS configuration object). 1320799
- [x] Task: Create a basic global listener for keydown events to detect registered shortcuts. 56aa117
- [x] Task: Implement the persistent visual cue (floating icon) in a shared layout file (e.g., `public/shared/header.php` or a dedicated footer component). 85fff30
- [ ] Task: Conductor - User Manual Verification 'Phase 1: Foundation & Global Infrastructure' (Protocol in workflow.md)

## Phase 2: Overlay UI & Dynamic Content
- [ ] Task: Create the HTML/CSS for the Modern Minimalist Overlay.
- [ ] Task: Implement the `Ctrl`/`Alt` press-and-hold logic (>500ms) to trigger the overlay.
- [ ] Task: Implement the dynamic rendering of shortcuts within the overlay based on the current `window.location`.
- [ ] Task: Add visual key icons to the overlay (Bootstrap/FontAwesome based).
- [ ] Task: Conductor - User Manual Verification 'Phase 2: Overlay UI & Dynamic Content' (Protocol in workflow.md)

## Phase 3: Core Functional Shortcuts
- [ ] Task: Implement **Navigation** shortcuts (e.g., `Alt+D` for Dashboard, `Alt+B` for Booking).
- [ ] Task: Implement **Form Action** shortcuts (e.g., `Ctrl+S` for Save/Submit - ensuring it prevents default if necessary).
- [ ] Task: Implement **Data Operation** shortcuts (e.g., `Alt+E` for Export, `Alt+F` for Search focus).
- [ ] Task: Add logic to ignore shortcuts when focus is in `input` or `textarea` elements.
- [ ] Task: Conductor - User Manual Verification 'Phase 3: Core Functional Shortcuts' (Protocol in workflow.md)

## Phase 4: Refinement & Mobile Consideration
- [ ] Task: Conduct cross-browser testing to ensure no conflicts with standard browser shortcuts.
- [ ] Task: Implement ARIA labels and screen reader support for the overlay and floating icon.
- [ ] Task: Optimize the listener for performance (e.g., debouncing or efficient lookup).
- [ ] Task: Conductor - User Manual Verification 'Phase 4: Refinement & Mobile Consideration' (Protocol in workflow.md)
