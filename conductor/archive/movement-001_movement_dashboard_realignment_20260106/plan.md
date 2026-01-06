# Track Plan: MOVEMENT-001 Movement Dashboard Realignment

## Tasks

### Phase 1: Database & Model
- [x] **Task 1: Update Database Schema**
    -   **Action:** Create a migration script (or run SQL) to add `category` ENUM('UMRAH', 'HAJJI') DEFAULT 'UMRAH' to `movements` table.
    -   **Action:** Verify/Add missing columns (e.g., `pattern_code`, `first_deposit_airlines_date`, etc.) if not fully covered by `schema.sql`.

- [x] **Task 2: Update Movement Model**
    -   **File:** `app/models/Movement.php`
    -   **Action:** Update `create` / `update` methods to handle the new columns.

### Phase 2: API Updates
- [x] **Task 3: Enhance API Response**
    -   **File:** `public/api/movement.php`
    -   **Action:** Update query to fetch new columns.
    -   **Action:** Structure response to potentially return `{'umrah': [...], 'hajji': [...]}` or let frontend filter.

### Phase 3: Frontend Implementation
- [x] **Task 4: Update Dashboard UI**
    -   **File:** `public/admin/movement_fullview.php`
    -   **Action:** Implement the "Double Table" layout (Umrah Top, Hajji Bottom).
    -   **Action:** Rebuild the table headers to match the PDF columns precisely.
    -   **Action:** Implement the row rendering logic with color coding.

### Phase 4: Verification
- [x] **Task 5: Verify Data Flow**
    -   **Action:** Insert dummy data for both Umrah and Hajji.
    -   **Action:** Verify they appear in correct sections with correct column data.
