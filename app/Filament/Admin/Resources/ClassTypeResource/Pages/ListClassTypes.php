<?php

namespace App\Filament\Admin\Resources\ClassTypeResource\Pages;

use App\Filament\Admin\Resources\ClassTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClassTypes extends ListRecords {
    protected static string $resource = ClassTypeResource::class;

    protected function getHeaderActions(): array {
        return [CreateAction::make(),];
    }
}
