<?php

namespace App\Filament\Admin\Resources\SubscriptionResource\Pages;

use App\Filament\Admin\Resources\SubscriptionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubscription extends EditRecord {
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array {
        return [DeleteAction::make(),];
    }
}
