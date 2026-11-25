<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ClassTypeResource\Pages;
use App\Models\ClassType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ClassTypeResource extends Resource {
    protected static ?string $model = ClassType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->unique(ignoreRecord: true)->maxLength(255),
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('description')->limit(50)->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array{
        return [
            'index' => Pages\ListClassTypes::route('/'),
            'create' => Pages\CreateClassType::route('/create'),
            'edit' => Pages\EditClassType::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole('admin') ?? false;
    }
}