# System Design & Architecture — Gym Class Booking System

## 1. Discoveries During Implementation (Beyond Initial Analysis)

While translating the initial requirements into working code, several edge cases and concurrency challenges emerged that required architectural adjustments:

1. **Concurrent Race Conditions on Capacity & Waitlist Promotion:**
   - *Discovery:* Checking `confirmedCount < capacity` before inserting a booking is vulnerable to race conditions when two members book the final seat simultaneously, or when two cancellations happen at the exact same moment.
   - *Resolution:* Wrapped `book()`, `cancel()`, and `markAttendance()` inside `DB::transaction()` using pessimistic row-level locking (`lockForUpdate()`) on both the `GymClass` and `Booking` rows.

2. **Re-validating Eligibility During FIFO Waitlist Promotion:**
   - *Discovery:* A member who joined a waitlist might become ineligible by the time a confirmed spot opens up (for example, they booked other classes in the interim and exhausted their weekly package quota, booked an overlapping class, or received a suspension).
   - *Resolution:* `WaitlistService::promoteNextEligible()` iterates through waitlisted bookings in chronological order (`created_at ASC`) and runs `BookingValidator::validateEligibility()` on each candidate, automatically skipping ineligible members until a valid candidate is promoted.

3. **Preventing Double-Counting No-Shows After a Previous Suspension:**
   - *Discovery:* Counting no-shows over a static 30-day lookback window would immediately re-ban a member once their 7-day suspension expired, because the original 3 no-shows would still fall within the last 30 days.
   - *Resolution:* In `SuspensionService::evaluateAndApplyBan()`, the evaluation window starts at `max(now()->subDays(30), $member->suspended_until)` so that only new no-shows recorded after a previous suspension ended count toward a future ban.

4. **Trainer Double-Booking & Past Scheduling in the Admin Panel:**
   - *Discovery:* Preventing time conflicts for members alone was insufficient; administrators also needed safeguards against assigning the same trainer to two overlapping classes (even across different class types) or scheduling classes in the past.
   - *Resolution:* Added custom closure validation rules and 24-hour formatting (`Y-m-d H:i`) inside `GymClassResource` to enforce trainer availability and restrict `starts_at` to future timestamps (`after:now`) on creation while still allowing edits to existing records.

5. **Application Timezone Alignment:**
   - *Discovery:* Time-sensitive rules—such as the 3-hour cancellation cutoff, class start/end checks, and weekly quota boundaries—depend on local gym time rather than default UTC.
   - *Resolution:* Explicitly configured `'timezone' => 'Asia/Damascus'` in `config/app.php`.

---

## 2. Key Design Decisions

### Decision 1: Decomposed Domain Service Layer Bound via Contracts
- **What was chosen:** Extracted all core business workflows into focused, single-responsibility service classes under `app/Services/` (`BookingService`, `BookingValidator`, `WaitlistService`, `SuspensionService`) bound to interfaces (`BookingServiceInterface`, `AttendanceServiceInterface`) in `AppServiceProvider`.
- **Why:** Keeping business logic out of HTTP controllers, Filament resources, and Eloquent models prevents code duplication across user interfaces, adheres to SOLID principles (Single Responsibility and Dependency Inversion), and allows `WaitlistService` to reuse `BookingValidator` directly without duplicating eligibility checks.

### Decision 2: Dual-Interface Architecture with Enum-Based RBAC & Query Scoping
- **What was chosen:** Used a PHP Backed Enum (`App\Enums\UserRole`: `Admin`, `Trainer`, `Member`) enforced via custom route middleware (`EnsureUserHasRole`) to separate a dedicated Blade member portal (`/dashboard`, `/my-bookings`) from the Filament admin/trainer panel (`/admin`), combined with Eloquent query scoping (`getEloquentQuery()`) inside Filament resources.
- **Why:** Members and gym staff have fundamentally different workflows. Separating their interfaces keeps the member booking experience fast and focused, while scoping Filament queries ensures trainers can only view their own assigned classes and record attendance for their own students without administrative privileges.

---

## 3. Placement of Booking Rules & Rationale

### Where the Rules Live
All booking and enforcement rules are centralized in **`app/Services/`**:
- **`BookingValidator.php`:** Enforces member role verification, active suspension checks, active subscription validation, class start-time checks, weekly package quota limits, duplicate booking prevention, and overlapping schedule detection.
- **`BookingService.php`:** Orchestrates atomic booking creation, enforces the 3-hour cancellation window (`Booking::canBeCancelled()`), triggers waitlist promotion, and validates post-class attendance recording.
- **`WaitlistService.php`:** Handles FIFO waitlist promotion when a confirmed spot opens.
- **`SuspensionService.php`:** Evaluates the 3-no-show rule within a 30-day rolling window and applies a 7-day booking suspension.

### Why This Location Was Chosen
1. **Single Source of Truth:** Every entry point (member web controllers, automated waitlist promotion, or future API endpoints) passes through the exact same validation and transactional pipeline.
2. **Transactional Integrity:** Placing validation alongside `DB::transaction()` and `lockForUpdate()` inside `BookingService` guarantees that checks and writes happen atomically—something Form Requests or frontend guards cannot guarantee under concurrent requests.
3. **Composable Reuse:** Having `BookingValidator` as an injectable dependency allows both `BookingService::book()` and `WaitlistService::promoteNextEligible()` to enforce identical eligibility rules without duplication.