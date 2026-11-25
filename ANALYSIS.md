1. Client & Legacy System Problems
The client is a gym owner offering group classes (Yoga, CrossFit, Spinning, Boxing) through weekly subscription packages (Basic: 3 bookings/week, Premium: 6 bookings/week). 
The old system failed because booking rules were only checked on the website frontend and completely ignored in the admin dashboard.
________________________________________________________________________________________________________________________________________________
            Legacy System Problem                            |                            Solution in the New Syste                            |
_____________________________________________________________|_________________________________________________________________________________|
Overbooking: 23 members booked a Boxing class with a         | Run all bookings inside a Database Transaction with lockForUpdate() on the class|
15-person limit.	                                         | row and check active bookings inside a single service class.                    |
_____________________________________________________________|_________________________________________________________________________________|
Unauthorized Edits: A trainer edited attendance for another  | Scope Filament queries using getEloquentQuery() so trainers only see their own  |
trainer's class.                                             | classes, and enforce Laravel Policies on all actions.                           |
_____________________________________________________________|_________________________________________________________________________________|
Expired Members Booking: A member whose subscription expired | Enforce an active subscription check (starts_at <= today <= ends_at) inside the | 2 months ago kept booking classes.                           | central BookingService.                                                         |
_____________________________________________________________|_________________________________________________________________________________|
Dashboard Bypassing Rules: Admin bookings skipped validation | Put all business rules in one central BookingService used by both the           |
rules because logic lived in website controllers.	         | member website (Blade) and the admin panel (Filament).                          |
_____________________________________________________________|_________________________________________________________________________________|
==================================================================================================================================================
2. Roles & Permissions Matrix
__________________________________________________________________________________________________________________________________________________
        Action           |  Admin/Staff           |             Trainer                           |  Member                                      |
_________________________|________________________|_______________________________________________|______________________________________________|
Access Filament Dashboard|	    Allowed	          | Allowed with condition (sees only own classes)|	Forbidden (403)                              |
_________________________|________________________|_______________________________________________|______________________________________________|
Manage Class Types, 	 | Allowed (Full CRUD)	  |              Forbidden	                      |  Forbidden (view only)                       |
Packages & Classes       |                        |                                               |                                              |
_________________________|________________________|_______________________________________________|______________________________________________|
Manage Trainers & Members|	Allowed (Full CRUD)   |	              Forbidden                       |	Forbidden (can only register own account)    |
_________________________|________________________|_______________________________________________|______________________________________________|
Manage Subscriptions     | 	Allowed (create/	  | 	         Forbidden                        |Forbidden (view own subscription only)        |
                         |renew after gym payment)|                                               |                                              |
_________________________|________________________|_______________________________________________|______________________________________________|
Book / Join Waitlist	 | Allowed with condition |	           Forbidden                          | Allowed with condition (not banned +         |
                         | (must pass all member  |                                               | active subscription + weekly quota left +    |
                         |  booking rules)        | 	                                          | no duplicate/time conflict + future class)   |
_________________________|________________________|_______________________________________________|______________________________________________|
Cancel a Booking	     | Allowed with condition |              Forbidden                        |	Allowed with condition (own booking + at     |
                         |(must be at least 3hours|                                               | least 3 hours before class start)            |
                         | before class start)	  |                                               |                                              |
_________________________|________________________|_______________________________________________|______________________________________________|
Record Attendance        | Allowed with condition | Allowed with condition (own classes only +    | Forbidden                                    |
 / No-Show	             |  (after class ends)	  | after class end time)	                      |                                              |
_________________________|________________________|_______________________________________________|______________________________________________|
Lift Member Suspension	 | Allowed                |             	Forbidden	                  | Forbidden                                    |
_________________________|________________________|_______________________________________________|______________________________________________|
==================================================================================================================================================
3. Entities, Relationships & ERDEntities & Columnsusers:
* id (PK), name, email (Unique), phone, age, gender, password, role (admin, trainer, member), suspended_until (nullable datetime), remember_token, timestamps.
* class_types: id (PK), name, description (nullable), timestamps.
class_type_trainer (Pivot): id (PK), trainer_id (FK $\rightarrow$ users.id), class_type_id (FK $\rightarrow$ class_types.id).
* packages: id (PK), name, weekly_limit (int: 3 or 6), duration_days (int), timestamps.
* subscriptions: id (PK), user_id (FK $\rightarrow$ users.id), package_id (FK $\rightarrow$ packages.id), starts_at (date), ends_at (date), timestamps.
* gym_classes: id (PK), class_type_id (FK $\rightarrow$ class_types.id), trainer_id (FK $\rightarrow$ users.id), starts_at (datetime), ends_at (datetime), capacity (int), timestamps.
* bookings: id (PK), user_id (FK $\rightarrow$ users.id), gym_class_id (FK $\rightarrow$ gym_classes.id), status (confirmed, waitlisted, cancelled, attended, no_show), cancelled_at (nullable datetime), promoted_at (nullable datetime), timestamps.
__________________________________________________________________________________________________________________________________________________
Relationship Justificationsusers:
(Trainers) $\leftrightarrow$ class_types (Many-to-Many): One trainer can teach multiple class types, and one class type can have multiple trainers, requiring the class_type_trainer pivot table.
users (Members) $\rightarrow$ subscriptions (One-to-Many) & packages $\rightarrow$ subscriptions (One-to-Many): A member can have multiple subscriptions over time, and each subscription belongs to one package.
Why bookings is a full Entity (not just a simple Pivot Table): A booking has its own lifecycle and tracks extra data beyond two foreign keys—specifically status, cancelled_at, promoted_at (to notify the user when promoted from the waitlist), and created_at (to keep waitlist queue order).

[View Entity Relationship Diagram (ERD)](/erd.png)
==================================================================================================================================================
4. Unbreakable Business Rules
  # |             Rule	                    |         When Checked	            |           What the User Sees if Broken                         |
|___|_______________________________________|___________________________________|________________________________________________________________|
|   | No Active Suspension: suspended_until | On booking, joining waitlist,     |  Error message showing that the account is suspended           |
| 1 |    must be null or in the past.	    | or waitlist promotion.	        |  and the exact enddate.                                        |
|___|_______________________________________|___________________________________|________________________________________________________________|
|   | Active Subscription Required: Member  | On booking or joining waitlist.   | Error message: "You do not have an active subscription. Please |
| 2 | must have a subscription covering     |                                   | contact the front desk."                                       |
|   | today (starts_at <= today <= ends_at).|                                   |                                                                |
|___|_______________________________________|___________________________________|________________________________________________________________|
|   | Weekly Booking Quota: Active bookings | On booking or automatic waitlist  | Error message: "You have reached your weekly booking limit"    |
| 3 | (confirmed / attended / no_show) in   | promotion.                        |                                                                |
|   | the current week must be less than    |                                   |                                                                |
|   | package.weekly_limit .                |                                   |                                                                |
|___|_______________________________________|___________________________________|________________________________________________________________|
|   | No Duplicate Class Booking: A member  | On booking or joining waitlist.   | Error message: "You are already booked or on the waitlist for  |
| 4 | cannot have an active (confirmed or   |                                   | this class."                                                   |
|   | waitlisted) booking for the same      |                                   |                                                                |
|   | class twice.                          |                                   |                                                                |
|___|_______________________________________|___________________________________|________________________________________________________________|
|   | No Overlapping Times: A member cannot |  On booking or automatic waitlist | Error message: "You already have another class booked during   |
| 5 |  hold two confirmed bookings whose    | promotion.                        | this time."                                                    |
|   |  time ranges overlap.                 |                                   |                                                                |
|___|_______________________________________|___________________________________|________________________________________________________________|
|   | Future Classes Only: 	                | On booking or joining waitlist.	| Book button is hidden/disabled in UI, and server rejects with  |
| 6 | gym_class.starts_at must be greater   |                                   | "This class has already started or ended."                     |
|   | than now().                           |                                   |                                                                |
|___|_______________________________________|___________________________________|________________________________________________________________|
|   | Class Capacity Check: Confirmed   	|On direct booking.                 | Direct booking is blocked; user is offered the "Join Waitlist" |
| 7 | bookings must be less than            |                                   | option instead.                                                |
|   | gym_class.capacity.                   |                                   |                                                                |
|___|_______________________________________|___________________________________|________________________________________________________________|
|   | 3-Hour Cancellation Rule: 	        | On cancelling a booking.	        | Cancel button is hidden, and server rejects with "Cancellations|
| 8 | Cancellation is allowed only if       |                                   | are only allowed at least 3 hours before class starts."        |
|   | now() <= starts_at - 3 hours.         |                                   |                                                                |
|___|_______________________________________|___________________________________|________________________________________________________________|
|   | Auto-Ban on 3 No-Shows: If a member   | Immediately when trainer/admin    | Member sees a suspension banner on their dashboard with the end|
| 9 | gets 3 no_show statuses within 30 days| marks no_show.                    | date and cannot book new classes.                              |
|   | ,set suspended_until = now() + 7 days.|                                   |                                                                |
|___|_______________________________________|___________________________________|________________________________________________________________|

==================================================================================================================================================
5. Booking States & Transitions:

* confirmed: Initial state when booking a class with free spots, or when promoted from the waitlist.

* waitlisted: Initial state when joining a full class queue (ordered by created_at ascending).

* cancelled: Transition from confirmed or waitlisted, triggered by the Member or Admin at least 3 hours before starts_at. (If confirmed is cancelled, the first eligible waitlisted member is promoted to confirmed).

* attended: Transition from confirmed, triggered by the Trainer (for their own class) or Admin after ends_at.

* no_show: Transition from confirmed, triggered by the Trainer (for their own class) or Admin after ends_at (triggers the 3-strikes suspension check).
==================================================================================================================================================
6. Questions for the Client (with Assumptions)

* How is a "week" calculated for the 3 or 6 booking limit? Is it a fixed calendar week (e.g., Saturday to Friday) or a rolling 7-day window from the subscription start date?
`Assumption`: Used a fixed calendar week (startOfWeek() to endOfWeek()) so quotas reset predictably for all members.
__________________________________________________________________________________________________________________________________________________
* Does joining the waitlist count toward the weekly booking limit immediately?
`Assumption`: No. Waitlisted spots do not consume weekly quota until promoted to confirmed. If the first person in line has used up their weekly limit or is suspended when a spot opens, the system skips them and promotes the next eligible member.
__________________________________________________________________________________________________________________________________________________
* If a member cancels a confirmed booking on time (more than 3 hours before), do they get that weekly booking credit back?
`Assumption`: Yes, cancelled bookings do not count toward the weekly limit.
__________________________________________________________________________________________________________________________________________________
* After a 7-day suspension ends, do the previous 3 no-shows reset, or will 1 more no-show trigger another ban?
`Assumption`: Only no_show records created after the end of the last suspension (within the last 30 days) are counted so a member is not punished twice for the same absences.
__________________________________________________________________________________________________________________________________________________
* What happens to a member's existing future bookings when they get suspended?
`Assumption`: Existing confirmed future bookings remain valid; the suspension only blocks creating new bookings or joining waitlists for 7 days.
==================================================================================================================================================
