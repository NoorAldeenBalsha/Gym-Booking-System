<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\ClassType;
use App\Models\GymClass;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $password = Hash::make('password');

            // =========================================================================
            // 1. (Class Types)
            // =========================================================================
            $classTypes = [
                'yoga' => ClassType::create([
                    'name' => 'Yoga & Mobility',
                    'description' => 'Guided Hatha and Vinyasa flows focused on flexibility, posture alignment, and breath control.',
                ]),
                'crossfit' => ClassType::create([
                    'name' => 'CrossFit Conditioning',
                    'description' => 'High-intensity functional movements combining Olympic lifting, bodyweight gymnastics, and metabolic conditioning.',
                ]),
                'hiit' => ClassType::create([
                    'name' => 'HIIT Burn',
                    'description' => '45-minute interval training alternating maximum-effort bursts with short active recovery periods.',
                ]),
                'boxing' => ClassType::create([
                    'name' => 'Boxing & Bag Work',
                    'description' => 'Technical striking combinations, footwork drills, and heavy bag rounds for endurance and coordination.',
                ]),
                'spinning' => ClassType::create([
                    'name' => 'Indoor Spinning',
                    'description' => 'Rhythm-driven stationary cycling climbs and sprints designed to build cardiovascular stamina.',
                ]),
                'pilates' => ClassType::create([
                    'name' => 'Core Pilates',
                    'description' => 'Low-impact mat exercises targeting deep core stability, spinal alignment, and muscular endurance.',
                ]),
                'strength' => ClassType::create([
                    'name' => 'Strength & Hypertrophy',
                    'description' => 'Structured resistance training focusing on compound barbell and dumbbell lifts with progressive overload.',
                ]),
            ];

            // =========================================================================
            // 2.  الاراك (Subscription Packages)
            // =========================================================================
            $packages = [
                'basic' => Package::create([
                    'name' => 'Basic Starter',
                    'weekly_limit' => 2,
                    'duration_days' => 30,
                ]),
                'standard' => Package::create([
                    'name' => 'Standard Active',
                    'weekly_limit' => 3,
                    'duration_days' => 30,
                ]),
                'pro' => Package::create([
                    'name' => 'Pro Unlimited+',
                    'weekly_limit' => 5,
                    'duration_days' => 30,
                ]),
                'elite' => Package::create([
                    'name' => 'Elite Quarterly',
                    'weekly_limit' => 7,
                    'duration_days' => 90,
                ]),
            ];

            // =========================================================================
            // 3. (Admins & Trainers)
            // =========================================================================
            User::create([
                'name' => 'System Admin',
                'email' => 'admin@gym.com',
                'phone' => '+963933100001',
                'age' => 32,
                'gender' => 'male',
                'password' => $password,
                'role' => UserRole::Admin,
            ]);

            $trainerSara = User::create([
                'name' => 'Coach Sara Mansour',
                'email' => 'sara.trainer@gym.com',
                'phone' => '+963933200001',
                'age' => 28,
                'gender' => 'female',
                'password' => $password,
                'role' => UserRole::Trainer,
            ]);
            $trainerSara->classTypes()->sync([
                $classTypes['yoga']->id,
                $classTypes['pilates']->id,
            ]);

            $trainerTariq = User::create([
                'name' => 'Coach Tariq Haddad',
                'email' => 'tariq.trainer@gym.com',
                'phone' => '+963933200002',
                'age' => 34,
                'gender' => 'male',
                'password' => $password,
                'role' => UserRole::Trainer,
            ]);
            $trainerTariq->classTypes()->sync([
                $classTypes['crossfit']->id,
                $classTypes['strength']->id,
            ]);

            $trainerKareem = User::create([
                'name' => 'Coach Kareem Nassar',
                'email' => 'kareem.trainer@gym.com',
                'phone' => '+963933200003',
                'age' => 30,
                'gender' => 'male',
                'password' => $password,
                'role' => UserRole::Trainer,
            ]);
            $trainerKareem->classTypes()->sync([
                $classTypes['boxing']->id,
                $classTypes['hiit']->id,
            ]);

            $trainerLina = User::create([
                'name' => 'Coach Lina Darwish',
                'email' => 'lina.trainer@gym.com',
                'phone' => '+963933200004',
                'age' => 27,
                'gender' => 'female',
                'password' => $password,
                'role' => UserRole::Trainer,
            ]);
            $trainerLina->classTypes()->sync([
                $classTypes['spinning']->id,
                $classTypes['hiit']->id,
                $classTypes['yoga']->id,
            ]);

            // =========================================================================
            // 4.  (Members) 
            // =========================================================================

            $mainMember = User::create([
                'name' => 'Noor Aldeen',
                'email' => 'member@gym.com',
                'phone' => '+963933300001',
                'age' => 24,
                'gender' => 'male',
                'password' => $password,
                'role' => UserRole::Member,
            ]);

            $quotaFullMember = User::create([
                'name' => 'Rami Khoury',
                'email' => 'quota.full@gym.com',
                'phone' => '+963933300002',
                'age' => 26,
                'gender' => 'male',
                'password' => $password,
                'role' => UserRole::Member,
            ]);

            $waitlistMember = User::create([
                'name' => 'Maya Suleiman',
                'email' => 'waitlist@gym.com',
                'phone' => '+963933300003',
                'age' => 23,
                'gender' => 'female',
                'password' => $password,
                'role' => UserRole::Member,
            ]);

            $twoStrikesMember = User::create([
                'name' => 'Ziad Barakat',
                'email' => 'strikes@gym.com',
                'phone' => '+963933300004',
                'age' => 29,
                'gender' => 'male',
                'password' => $password,
                'role' => UserRole::Member,
            ]);

            $suspendedMember = User::create([
                'name' => 'Samer Al-Ali',
                'email' => 'suspended@gym.com',
                'phone' => '+963933300005',
                'age' => 31,
                'gender' => 'male',
                'password' => $password,
                'role' => UserRole::Member,
                'suspended_until' => now()->addDays(5),
            ]);

            $expiredMember = User::create([
                'name' => 'Hala Youssef',
                'email' => 'expired@gym.com',
                'phone' => '+963933300006',
                'age' => 25,
                'gender' => 'female',
                'password' => $password,
                'role' => UserRole::Member,
            ]);

            $noSubMember = User::create([
                'name' => 'Omar Sharaf',
                'email' => 'nosub@gym.com',
                'phone' => '+963933300007',
                'age' => 22,
                'gender' => 'male',
                'password' => $password,
                'role' => UserRole::Member,
            ]);

            $extraMembers = [];
            $extraSpecs = [
                ['Dalia Hamwi', 'dalia@gym.com', '+963933300008', 27, 'female'],
                ['Fadi Issa', 'fadi@gym.com', '+963933300009', 30, 'male'],
                ['Reem Sabbagh', 'reem@gym.com', '+963933300010', 24, 'female'],
                ['Bilal Osman', 'bilal@gym.com', '+963933300011', 28, 'male'],
                ['Nadia Murad', 'nadia@gym.com', '+963933300012', 26, 'female'],
            ];

            foreach ($extraSpecs as [$name, $email, $phone, $age, $gender]) {
                $extraMembers[] = User::create([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'age' => $age,
                    'gender' => $gender,
                    'password' => $password,
                    'role' => UserRole::Member,
                ]);
            }

            // =========================================================================
            // 5.  (Subscriptions)
            // =========================================================================
            Subscription::create([
                'user_id' => $mainMember->id,
                'package_id' => $packages['pro']->id, 
                'starts_at' => now()->subDays(5)->toDateString(),
                'ends_at' => now()->addDays(25)->toDateString(),
            ]);

            Subscription::create([
                'user_id' => $quotaFullMember->id,
                'package_id' => $packages['basic']->id, 
                'starts_at' => now()->subDays(10)->toDateString(),
                'ends_at' => now()->addDays(20)->toDateString(),
            ]);

            Subscription::create([
                'user_id' => $waitlistMember->id,
                'package_id' => $packages['pro']->id,
                'starts_at' => now()->subDays(3)->toDateString(),
                'ends_at' => now()->addDays(27)->toDateString(),
            ]);

            Subscription::create([
                'user_id' => $twoStrikesMember->id,
                'package_id' => $packages['standard']->id,
                'starts_at' => now()->subDays(15)->toDateString(),
                'ends_at' => now()->addDays(15)->toDateString(),
            ]);

            Subscription::create([
                'user_id' => $suspendedMember->id,
                'package_id' => $packages['standard']->id,
                'starts_at' => now()->subDays(20)->toDateString(),
                'ends_at' => now()->addDays(10)->toDateString(),
            ]);

            Subscription::create([
                'user_id' => $expiredMember->id,
                'package_id' => $packages['basic']->id,
                'starts_at' => now()->subDays(40)->toDateString(),
                'ends_at' => now()->subDays(10)->toDateString(),
            ]);

            foreach ($extraMembers as $index => $extraMember) {
                Subscription::create([
                    'user_id' => $extraMember->id,
                    'package_id' => $index % 2 === 0 ? $packages['standard']->id : $packages['elite']->id,
                    'starts_at' => now()->subDays(7)->toDateString(),
                    'ends_at' => now()->addDays($index % 2 === 0 ? 23 : 83)->toDateString(),
                ]);
            }

            // =========================================================================
            // 6. (Gym Classes)
            // =========================================================================

            $pastClass1 = GymClass::create([
                'class_type_id' => $classTypes['crossfit']->id,
                'trainer_id' => $trainerTariq->id,
                'starts_at' => now()->subDays(12)->setTime(17, 0),
                'ends_at' => now()->subDays(12)->setTime(18, 0),
                'capacity' => 12,
            ]);

            $pastClass2 = GymClass::create([
                'class_type_id' => $classTypes['boxing']->id,
                'trainer_id' => $trainerKareem->id,
                'starts_at' => now()->subDays(9)->setTime(18, 0),
                'ends_at' => now()->subDays(9)->setTime(19, 0),
                'capacity' => 10,
            ]);

            $pastClass3 = GymClass::create([
                'class_type_id' => $classTypes['spinning']->id,
                'trainer_id' => $trainerLina->id,
                'starts_at' => now()->subDays(6)->setTime(16, 0),
                'ends_at' => now()->subDays(6)->setTime(17, 0),
                'capacity' => 15,
            ]);

            $yesterdayPendingClass = GymClass::create([
                'class_type_id' => $classTypes['strength']->id,
                'trainer_id' => $trainerTariq->id,
                'starts_at' => now()->subDay()->setTime(18, 0),
                'ends_at' => now()->subDay()->setTime(19, 0),
                'capacity' => 10,
            ]);

            $withinThreeHoursClass = GymClass::create([
                'class_type_id' => $classTypes['pilates']->id,
                'trainer_id' => $trainerSara->id,
                'starts_at' => now()->addHours(2),
                'ends_at' => now()->addHours(3),
                'capacity' => 12,
            ]);

            $fullClassStart = now()->endOfWeek()->diffInHours(now()) >= 12
                ? now()->addHours(6)
                : now()->addWeek()->startOfWeek()->addDays(1)->setTime(10, 0);

            $fullClass = GymClass::create([
                'class_type_id' => $classTypes['yoga']->id,
                'trainer_id' => $trainerSara->id,
                'starts_at' => $fullClassStart,
                'ends_at' => $fullClassStart->copy()->addHour(),
                'capacity' => 3,
            ]);


            $sameWeekSecondClassStart = $fullClassStart->copy()->addHours(3);
            $sameWeekSecondClass = GymClass::create([
                'class_type_id' => $classTypes['crossfit']->id,
                'trainer_id' => $trainerTariq->id,
                'starts_at' => $sameWeekSecondClassStart,
                'ends_at' => $sameWeekSecondClassStart->copy()->addHour(),
                'capacity' => 12,
            ]);

            $upcomingClasses = [
                GymClass::create([
                    'class_type_id' => $classTypes['hiit']->id,
                    'trainer_id' => $trainerKareem->id,
                    'starts_at' => now()->addDays(2)->setTime(17, 0),
                    'ends_at' => now()->addDays(2)->setTime(18, 0),
                    'capacity' => 15,
                ]),

                GymClass::create([
                    'class_type_id' => $classTypes['spinning']->id,
                    'trainer_id' => $trainerLina->id,
                    'starts_at' => now()->addDays(2)->setTime(19, 0),
                    'ends_at' => now()->addDays(2)->setTime(20, 0),
                    'capacity' => 16,
                ]),

                GymClass::create([
                    'class_type_id' => $classTypes['strength']->id,
                    'trainer_id' => $trainerTariq->id,
                    'starts_at' => now()->addDays(3)->setTime(16, 0),
                    'ends_at' => now()->addDays(3)->setTime(17, 30),
                    'capacity' => 10,
                ]),

                GymClass::create([
                    'class_type_id' => $classTypes['yoga']->id,
                    'trainer_id' => $trainerSara->id,
                    'starts_at' => now()->addDays(4)->setTime(10, 0),
                    'ends_at' => now()->addDays(4)->setTime(11, 0),
                    'capacity' => 14,
                ]),

                GymClass::create([
                    'class_type_id' => $classTypes['boxing']->id,
                    'trainer_id' => $trainerKareem->id,
                    'starts_at' => now()->addDays(5)->setTime(18, 30),
                    'ends_at' => now()->addDays(5)->setTime(19, 30),
                    'capacity' => 12,
                ]),

                GymClass::create([
                    'class_type_id' => $classTypes['pilates']->id,
                    'trainer_id' => $trainerSara->id,
                    'starts_at' => now()->addDays(6)->setTime(11, 0),
                    'ends_at' => now()->addDays(6)->setTime(12, 0),
                    'capacity' => 12,
                ]),
            ];

            // =========================================================================
            // 7.(Bookings — Confirmed, Waitlisted, Cancelled, Attended, NoShow)
            // =========================================================================

            foreach ([$pastClass1, $pastClass2, $pastClass3] as $pastClass) {
                Booking::create([
                    'user_id' => $suspendedMember->id,
                    'gym_class_id' => $pastClass->id,
                    'status' => BookingStatus::NoShow,
                ]);
            }

            Booking::create([
                'user_id' => $twoStrikesMember->id,
                'gym_class_id' => $pastClass1->id,
                'status' => BookingStatus::NoShow,
            ]);

            Booking::create([
                'user_id' => $twoStrikesMember->id,
                'gym_class_id' => $pastClass2->id,
                'status' => BookingStatus::NoShow,
            ]);

            Booking::create([
                'user_id' => $twoStrikesMember->id,
                'gym_class_id' => $yesterdayPendingClass->id,
                'status' => BookingStatus::Confirmed, // بانتظار تسجيل حضور/غياب من المدرب
            ]);

            Booking::create([
                'user_id' => $mainMember->id,
                'gym_class_id' => $pastClass1->id,
                'status' => BookingStatus::Attended,
            ]);

            Booking::create([
                'user_id' => $mainMember->id,
                'gym_class_id' => $pastClass3->id,
                'status' => BookingStatus::Cancelled,
                'cancelled_at' => now()->subDays(7),
            ]);

            Booking::create([
                'user_id' => $extraMembers[0]->id,
                'gym_class_id' => $yesterdayPendingClass->id,
                'status' => BookingStatus::Confirmed, // بانتظار تسجيل حضور/غياب من المدرب
            ]);

            Booking::create([
                'user_id' => $mainMember->id,
                'gym_class_id' => $withinThreeHoursClass->id,
                'status' => BookingStatus::Confirmed,
            ]);

            Booking::create([
                'user_id' => $mainMember->id,
                'gym_class_id' => $fullClass->id,
                'status' => BookingStatus::Confirmed,
            ]);

            Booking::create([
                'user_id' => $quotaFullMember->id,
                'gym_class_id' => $fullClass->id,
                'status' => BookingStatus::Confirmed,
            ]);

            Booking::create([
                'user_id' => $extraMembers[1]->id,
                'gym_class_id' => $fullClass->id,
                'status' => BookingStatus::Confirmed,
                'promoted_at' => now()->subHours(5), 
            ]);

            Booking::create([
                'user_id' => $waitlistMember->id,
                'gym_class_id' => $fullClass->id,
                'status' => BookingStatus::Waitlisted,
                'created_at' => now()->subHours(4),
                'updated_at' => now()->subHours(4),
            ]);

            Booking::create([
                'user_id' => $extraMembers[0]->id,
                'gym_class_id' => $fullClass->id,
                'status' => BookingStatus::Waitlisted,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ]);
            
            Booking::create([
                'user_id' => $quotaFullMember->id,
                'gym_class_id' => $sameWeekSecondClass->id,
                'status' => BookingStatus::Confirmed,
            ]);

            Booking::create([
                'user_id' => $extraMembers[2]->id,
                'gym_class_id' => $upcomingClasses[0]->id,
                'status' => BookingStatus::Confirmed,
            ]);

            Booking::create([
                'user_id' => $extraMembers[3]->id,
                'gym_class_id' => $upcomingClasses[1]->id,
                'status' => BookingStatus::Confirmed,
            ]);

            Booking::create([
                'user_id' => $extraMembers[4]->id,
                'gym_class_id' => $upcomingClasses[2]->id,
                'status' => BookingStatus::Confirmed,
            ]);
        });
    }
}