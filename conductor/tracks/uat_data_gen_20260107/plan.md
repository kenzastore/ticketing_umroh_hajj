# Track Plan: Generate Indonesian Dummy Data for UAT

## Tasks

### Phase 1: Preparation & Utilities [checkpoint: 32fd41e]
- [x] **Task 1: Identify and Load Data Constants**
    -   **Action:** Define arrays for Indonesian names, agents, corporates, airports (SUB, CGK, JED, MED), and carriers (Garuda, Saudia, Lion).
- [x] **Task 2: Create Seeding Script Skeleton**
    -   **File:** `database/seed_uat_data.php` (New)
    -   **Action:** Set up the basic PHP script with necessary includes (`db_connect.php` and models).
- [x] **Task 3: Conductor - User Manual Verification 'Phase 1: Preparation & Utilities' (Protocol in workflow.md)** 32fd41e

### Phase 2: Data Generation Logic
- [ ] **Task 4: Implement Master Data Seeding**
    -   **Action:** Generate 10 Agent/Corporate records using `Agent::create` and `Corporate::create`.
- [ ] **Task 5: Implement Booking Request Seeding**
    -   **Action:** Generate 30 records with varying pax and segments.
- [ ] **Task 6: Implement Movement Seeding**
    -   **Action:** Generate 40 records, including the specific H-3 and Past Due scenarios.
- [ ] **Task 7: Implement Invoice & Payment Seeding**
    -   **Action:** Generate 20 records linked to movements with varying payment statuses.
- [ ] **Task 8: Conductor - User Manual Verification 'Phase 2: Data Generation Logic' (Protocol in workflow.md)**

### Phase 3: Verification
- [ ] **Task 9: End-to-End Verification**
    -   **Action:** Execute the script and verify the data distribution and dashboard visibility.
- [ ] **Task 10: Conductor - User Manual Verification 'Phase 3: Verification' (Protocol in workflow.md)**
