<?php

namespace App\Filament\Admin\Resources\GymClassResource\Pages;

use App\Filament\Admin\Resources\GymClassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGymClasses extends ListRecords {
    protected static string $resource = GymClassResource::class;

    protected function getHeaderActions(): array {
        return [CreateAction::make(),];
    }
}
