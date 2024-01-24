<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeScheduleResource\Pages;
use App\Models\TimeSchedule;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TimeScheduleResource extends Resource
{
    protected static ?string $model = TimeSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        Select::make('role_id')
                            ->label('Schedule For')
                            ->options(DB::table('roles')->whereNotIn('name',  [User::ROLE_ADMIN, User::ROLE_PATIENT])->pluck('name', 'id'))
                            ->required()
                            ->native(false)
                            ->preload()
                            ->live()
                            ->searchable(),
                        Select::make('user_id')
                            ->label('User')
                            ->options(fn (Get $get): Collection => User::query()->withWhereHas('roles', function ($query) use ($get) {
                                return $query->where('id', $get('role_id'));
                            })->pluck('name', 'id'))
                            ->required()
                            ->native(false)
                            ->searchable()
                    ])->columns()->visible(fn ($record) => $record === null),
                Section::make('')
                    ->schema([
                        TimePicker::make('start_time')
                            ->datalist([
                                '07:00',
                                '08:00',
                                '09:00',
                                '15:00',
                                '16:00',
                                '22:00',
                                '00:00'
                            ])
                        ->seconds(false)
                        ->required(),
                        TimePicker::make('end_time')
                            ->datalist([
                                '15:00',
                                '16:00',
                                '17:00',
                                '18:00',
                                '19:00',
                                '20:00',
                                '22:00',
                            ])
                            ->required()
                            ->after('start_time')
                            ->seconds(false),
                        Select::make('week_day_off')
                            ->label('Week off day')
                            ->options([
                                'sunday' => 'Sunday',
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                            ])
                            ->required()
                            ->native(false)
                            ->searchable(),
                        Select::make('appointment_duration')
                            ->label('Appointment Duration')
                            ->options([
                                '00:10' => '10 minutes',
                                '00:15' => '15 minutes',
                                '00:30' => '30 minutes',
                            ])
                            ->native(false)
                        ->visible(function ($record, Get $get) {
                            if ($record) {
                                return $record->user->hasRole(User::ROLE_DOCTOR);
                            }

                            $roleName = DB::table('roles')->where('id', $get('role_id'))->first();
                            return $roleName?->name === User::ROLE_DOCTOR;
                        })
                    ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.roles.name')
                    ->label('Role'),
                TextColumn::make('start_time')
                    ->getStateUsing(function ($record) {
                        return Carbon::parse($record->start_time)->format('h:i a');
                    }),
                TextColumn::make('end_time')
                    ->getStateUsing(function ($record) {
                        return Carbon::parse($record->end_time)->format('h:i a');
                    }),
                TextColumn::make('week_day_off')
                    ->label('Day off')
                    ->badge()
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimeSchedules::route('/'),
            'create' => Pages\CreateTimeSchedule::route('/create'),
            'edit' => Pages\EditTimeSchedule::route('/{record}/edit'),
        ];
    }
}
