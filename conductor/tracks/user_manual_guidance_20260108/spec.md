# Specification: Comprehensive User Manual for End-to-End Workflow

## 1. Overview
This track focuses on generating a definitive User Manual for the Ticketing Umroh & Haji system. The manual will guide users through the entire business workflow, from the initial booking request to final financial reconciliation, tailored to the specific roles of Admin, Ticketing, Finance, and Monitors.

## 2. Functional Requirements
### 2.1 Workflow-Based Structure
The manual must be organized chronologically according to the business process stages:
- **Stage 1: Demand Intake (Booking Request):** Instructions for creating and managing incoming requests.
- **Stage 2: Operational Execution (Movement & Monitoring):** Guidelines for converting requests to active movements, tracking flights, and using the FullView monitoring.
- **Stage 3: Financial Settlement (Invoicing & Payments):** Detailed steps for generating external invoices, recording payments, and processing internal Payment Advice (airline transfers).
- **Stage 4: Management & Audit:** Instructions for master data maintenance, role management, and reviewing audit logs.

### 2.2 Role-Specific Guidance
Include clear indicators or sections for:
- **Ticketing/Operational Staff:** Workflow focused on Requests and Movements.
- **Finance Officer:** Workflow focused on Invoices, Receipts, and Payment Advice.
- **Administrator:** System configuration and oversight.
- **Monitor/Viewer:** Dashboard navigation and KPI interpretation.

### 2.3 Integrated Documentation
- **Format:** Markdown files integrated into the repository under a structured directory (e.g., `docs/manual/`).
- **Visual Aids:** Use text-based representations of UI elements (buttons, menus) and placeholders for screenshots where necessary.

## 3. Non-Functional Requirements
- **Language:** Professional but accessible language (Indonesian or English as per project preference, default to matching existing code comments/docs).
- **Maintainability:** Structured in a way that developers can easily update it as features evolve.

## 4. Acceptance Criteria
- [ ] A complete user manual in Markdown format exists in the repository.
- [ ] All 4 stages of the business workflow are thoroughly documented.
- [ ] Role-specific instructions are clearly identifiable.
- [ ] Cross-references between menus (e.g., Request -> Movement -> Invoice) are accurately described.

## 5. Out of Scope
- Interactive video tutorials.
- Context-sensitive "Help" tooltips within the application UI (this is a standalone manual).
