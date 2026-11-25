<?php

namespace App\Filament\Admin\Resources\ClassTypeResource\Pages;

use App\Filament\Admin\Resources\ClassTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassType extends EditRecord {
    protected static string $resource = ClassTypeResource::class;

    protected function getHeaderActions(): array {
        return [DeleteAction::make(), ];
    }
}
