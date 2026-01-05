# Track Plan: INV-001 Invoice Contract Compliance

## Phase 1: Contract Analysis and Verification Strategy
- [~] Task: Thoroughly review `conductor/contracts/invoice_template_contract.md` to understand all field requirements, layout specifications, and data formats.
- [~] Task: Analyze current invoice generation code (`app/templates/invoice_template.php`, relevant PHP logic in `public/finance/` and models) to identify how it maps to the contract.
- [~] Task: Define specific, granular verification steps for each section (Header, Title, Main Table, etc.) based on the `spec.md` and `invoice_template_contract.md`. (User chose high-level checks).
- [~] Task: Plan the execution strategy for these verification steps (manual checks, automated scripts if feasible).

## Phase 2: Invoice Generation Compliance Audit
- [x] Task: Perform verification for **Visual Layout & Content** against contract specifications for all sections (Header, Title, Main Table, Summary, Flight Info, Fare Breakdown, Totals, Remarks, Time Limit, Bank Details, Footer).
- [x] Task: Perform verification for **Data Retrieval** to ensure all required fields are fetched and handled correctly.
- [x] Task: Perform verification for **Field Mapping** to ensure exact correspondence between contract fields and data source.
- [x] Task: Perform verification for **Export Functionality** (PDF generation) to ensure accurate rendering.
- [x] Task: Document all identified deviations from the `invoice_template_contract.md`.

## Phase 3: Remediation and Refinement
- [~] Task: Update invoice generation code (template, data retrieval, PDF export) to address any identified deviations. (Remediation steps proposed)
- [~] Task: Re-verify corrected sections against the contract. (Conceptually pending application of proposed changes)

## Phase 4: Documentation and Workflow Integration
- [x] Task: Update `product.md` with findings or changes related to invoice compliance.
- [x] Task: Update `workflow.md` to include a "Contract Compliance" gate in the relevant workflow.
- [!] Task: Add references to the `invoice_template_contract.md` in `GEMINI.md` and `AGENTS.md`. (Skipped: Files not found)
- [!] Task: Record any decisions made during this process in `DECISIONS.md`. (Skipped: File not found)
