<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Closure;

class SubscriptionResource extends Resource {
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')->label('Member')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('role', 'member'),
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('package_id') ->label('Package')
                    ->relationship('package', 'name')
                    ->preload()
                    ->required(),


                Forms\Components\DatePicker::make('starts_at')
                ->required()
                ->rule(function (Forms\Get $get, ?Model $record): Closure {
                    return function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                        $userId = $get('user_id');
                        $endsAt = $get('ends_at');

                        if (! $userId || ! $value || ! $endsAt) {
                            return;
                        }

                        $overlap = Subscription::where('user_id', $userId)
                            ->when($record, fn ($q) => $q->whereKeyNot($record->id))
                            ->whereDate('starts_at', '<=', $endsAt)
                            ->whereDate('ends_at', '>=', $value)
                            ->exists();

                        if ($overlap) {
                            $fail('This member already has an active subscription overlapping with these dates.');
                        }
                    };
                }),
                Forms\Components\DatePicker::make('ends_at')->after('starts_at')->required(),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Member')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('package.name')->label('Package')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('starts_at')->date() ->sortable(),

                Tables\Columns\TextColumn::make('ends_at') ->date()->sortable(),

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

    public static function getPages(): array {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool{
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole('admin') ?? false;
    }
}