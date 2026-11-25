<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GymClassResource\Pages;
use App\Models\GymClass;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Closure;

class GymClassResource extends Resource {
    protected static ?string $model = GymClass::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Select::make('class_type_id')
                    ->label('Class Type')
                    ->relationship('classType', 'name')
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('trainer_id', null))
                    ->required(),

                Forms\Components\Select::make('trainer_id')
                    ->label('Trainer')
                    ->relationship(
                        name: 'trainer',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                            $query->where('role', 'trainer');

                            if ($classTypeId = $get('class_type_id')) {
                                $query->whereHas('classTypes', fn (Builder $q) => $q->where('class_types.id', $classTypeId));
                            }
                        }
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->rule(function (Forms\Get $get, ?\Illuminate\Database\Eloquent\Model $record): \Closure {
                        return function (string $attribute, mixed $value, \Closure $fail) use ($get, $record): void {
                            $startsAt = $get('starts_at');
                            $endsAt = $get('ends_at');

                            if (! $value || ! $startsAt || ! $endsAt) {
                                return;
                            }

                            $conflict = \App\Models\GymClass::where('trainer_id', $value)
                                ->when($record, fn (Builder $q) => $q->whereKeyNot($record->id))
                                ->where('starts_at', '<', $endsAt)
                                ->where('ends_at', '>', $startsAt)
                                ->exists();

                            if ($conflict) {
                                $fail('This trainer already has another class scheduled during this time.');
                            }
                        };
                    }),

                Forms\Components\DateTimePicker::make('starts_at')
                    ->seconds(false)
                    ->displayFormat('Y-m-d H:i')
                    ->required()
                    ->rules(fn (string $operation): array => $operation === 'create' ? ['after:now'] : []),

                Forms\Components\DateTimePicker::make('ends_at')
                    ->seconds(false)
                    ->displayFormat('Y-m-d H:i')
                    ->after('starts_at')
                    ->required(),

                Forms\Components\TextInput::make('capacity')
                    ->required()
                    ->integer()
                    ->minValue(1)
                    ->default(15)
                    ->suffix('spots'),
            ]);
    }

    public static function table(Table $table): Table{
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('classType.name')
                    ->label('Class')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('trainer.name')
                    ->label('Trainer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->suffix(' spots')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getPages(): array {
        return [
            'index' => Pages\ListGymClasses::route('/'),
            'create' => Pages\CreateGymClass::route('/create'),
            'edit' => Pages\EditGymClass::route('/{record}/edit'),
        ];
    }

    // Trainer sees only their own classes; Admin sees all classes
    public static function getEloquentQuery(): Builder {
        $query = parent::getEloquentQuery();

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user?->hasRole('trainer')) {$query->where('trainer_id', $user->id);}

        return $query;
    }

    // Both Admin and Trainer can view the Classes section
    public static function canViewAny(): bool {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return ($user?->hasRole('admin') || $user?->hasRole('trainer'));
    }

    // Only Admin can create, edit, or delete classes
    public static function canCreate(): bool {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole('admin') ?? false;
    }

    public static function canEdit(Model $record): bool {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole('admin') ?? false;
    }

    public static function canDelete(Model $record): bool {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole('admin') ?? false;
    }
}