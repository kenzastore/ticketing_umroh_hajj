# Track Plan: App Refactoring (Worksheet Sync)

This plan details the migration of the PHP code to support the new schema.

## Phase 1: Model Migration
1.  **Refactor `Agent.php`**: Align with new `agents` table columns.
2.  **Create `Corporate.php`**: Basic CRUD for corporates.
3.  **Create `BookingRequest.php`**: 
    - Handle `booking_requests` and `booking_request_legs`.
    - Implement multi-leg flight storage.
4.  **Create `Movement.php`**:
    - Handle `movements` and `flight_legs`.
    - Support worksheet-specific fields (DP1/2 Status, FP Status).
5.  **Refactor `Invoice.php`**:
    - Update to use the header/lines table split.
6.  **Cleanup**: Delete or archive old `Booking.php` and `Request.php` (if they existed).

## Phase 2: Controller & UI Updates
1.  **Admin Masters**:
    - Update Agents management.
    - Create Corporates management.
2.  **Request Flow**:
    - Update `new_request.php` to handle the new fields (Corporate, SkyAgent).
    - Update process script to save to new tables.
3.  **Movement Dashboard**:
    - Refactor `movement_fullview.php` to match the worksheet's data structure.

## Steps
- [x] Implement `Corporate.php` and its master UI. a1a4951
- [x] Refactor `Agent.php` and its master UI. a1a4951
- [x] Implement `BookingRequest.php` and update the request creation flow. a1a4951
- [x] Implement `Movement.php` and update the movement dashboard. a1a4951
- [x] Refactor Invoice.php and the finance dashboard. a1a4951
