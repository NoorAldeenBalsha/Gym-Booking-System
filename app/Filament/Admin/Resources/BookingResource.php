<?php

namespace App\Filament\Admin\Resources;

use App\Contracts\AttendanceServiceInterface;
use App\Contracts\BookingServiceInterface;
use App\Enums\BookingStatus;
use App\Exceptions\BookingRuleException;
use App\Filament\Admin\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\GymClass;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BookingResource extends Resource {
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form {
        return $form
                ->schema([
                    Select::make('user_id')->label('Member')
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query->where('role', 'member'),
                        )
                        ->searchable()->preload()->required(),
                    Select::make('gym_class_id')
                        ->relationship(
                            name: 'gymClass',
                            titleAttribute: 'id',
                            modifyQueryUsing: fn (Builder $query) => $query->with('classType')->where('starts_at', '>', now())
                        )
                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->classType->name} - {$record->starts_at->format('Y-m-d H:i')}")
                        ->preload()
                        ->required(),

                    Select::make('status')
                        ->options([
                            BookingStatus::Confirmed->value => BookingStatus::Confirmed->label(),
                            BookingStatus::Waitlisted->value => BookingStatus::Waitlisted->label(),
                        ])
                        ->default(BookingStatus::Confirmed->value)
                        ->required(),
                ]);
    }        

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Member')->searchable()->sortable(),
                TextColumn::make('gymClass.classType.name')->label('Class')->searchable()->sortable(),
                TextColumn::make('gymClass.starts_at') ->label('Class Time')->dateTime('M d, Y H:i')->sortable(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(function (BookingStatus|string $state): string {
                        $enum = $state instanceof BookingStatus ? $state : BookingStatus::from($state);
                        return $enum->label();
                    })
                    ->color(function (BookingStatus|string $state): string {
                        $enum = $state instanceof BookingStatus ? $state : BookingStatus::from($state);
                        return match ($enum) {
                            BookingStatus::Confirmed => 'info',
                            BookingStatus::Waitlisted => 'warning',
                            BookingStatus::Attended => 'success',
                            BookingStatus::NoShow, BookingStatus::Cancelled => 'danger',
                        };
                    })
                    ->sortable(),

                TextColumn::make('promoted_at')->dateTime('M d, H:i')->placeholder('—') ->sortable()->toggleable(),
                TextColumn::make('cancelled_at')->dateTime('M d, H:i')->placeholder('—')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at') ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        BookingStatus::Confirmed->value => BookingStatus::Confirmed->label(),
                        BookingStatus::Waitlisted->value => BookingStatus::Waitlisted->label(),
                        BookingStatus::Attended->value => BookingStatus::Attended->label(),
                        BookingStatus::NoShow->value => BookingStatus::NoShow->label(),
                        BookingStatus::Cancelled->value => BookingStatus::Cancelled->label(),
                    ]),
            ])
            ->actions([
                Action::make('cancel')
                    ->label('Cancel')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function (Booking $record): bool {
                        /** @var \App\Models\User|null $user */
                        $user = Auth::user();
                        return ($user?->hasRole('admin') ?? false) && $record->canBeCancelled();
                    })
                    ->action(function (Booking $record): void {
                        try {
                            app(BookingServiceInterface::class)->cancel($record);
                            Notification::make()->title('Booking cancelled')->success()->send();
                        } catch (BookingRuleException $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),

                Action::make('attended')
                    ->label('Attended')
                    ->color('success')
                    ->visible(fn (Booking $record): bool => $record->gymClass->hasEnded() && $record->status === BookingStatus::Confirmed)
                    ->action(function (Booking $record): void {
                        try {
                            app(AttendanceServiceInterface::class)->markAttendance($record, BookingStatus::Attended);
                            Notification::make()->title('Marked as Attended')->success()->send();
                        } catch (BookingRuleException $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),

                Action::make('no_show')
                    ->label('No Show')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record): bool => $record->gymClass->hasEnded() && $record->status === BookingStatus::Confirmed)
                    ->action(function (Booking $record): void {
                        try {
                            app(AttendanceServiceInterface::class)->markAttendance($record, BookingStatus::NoShow);
                            Notification::make()->title('Marked as No Show')->warning()->send();
                        } catch (BookingRuleException $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
        ];
    }

    public static function canViewAny(): bool {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return ($user?->hasRole('admin') || $user?->hasRole('trainer'));
    }

    public static function canCreate(): bool {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole('admin') ?? false;
    }

    public static function canEdit(Model $record): bool {
        return false;
    }

    public static function canDelete(Model $record): bool {
        return false;
    }

    public static function getEloquentQuery(): Builder {
        $query = parent::getEloquentQuery();

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user?->hasRole('trainer')) {
            $query->whereHas('gymClass', fn (Builder $q) => $q->where('trainer_id', $user->id));
        }

        return $query;
    }
}