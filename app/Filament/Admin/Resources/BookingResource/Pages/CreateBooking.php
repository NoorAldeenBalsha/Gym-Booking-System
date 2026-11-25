<?php

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use App\Models\GymClass;
use App\Models\Booking;
use App\Contracts\BookingServiceInterface;
use App\Exceptions\BookingRuleException;
use Filament\Notifications\Notification;
use \Illuminate\Database\Eloquent\Model;

class CreateBooking extends CreateRecord {
    protected static string $resource = BookingResource::class;

    protected function handleRecordCreation(array $data): Model {
        try {
            $member = User::findOrFail($data['user_id']);
            $class = GymClass::findOrFail($data['gym_class_id']);
            
            return app(BookingServiceInterface::class)->book($member, $class);
        } catch (BookingRuleException $e) {
            Notification::make()->title($e->getMessage())->danger()->send();

            $this->halt();
            return new Booking();
        }
    }
}

