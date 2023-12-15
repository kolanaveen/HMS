<?php

namespace App\Filament\Resources;

use Exception;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Profile;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('patient_id')
                    ->relationship('patient', 'name')
                    ->label('Patient')
                    ->preload()
                    ->createOptionForm([
                        Section::make('')
                            ->columns(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->required()
                                    ->columns(2),
                                TextInput::make('last_name')
                                    ->required(),
                                TextInput::make('email')
                                    ->required()
                                    ->email(),
                                Select::make('department_id')
                                    ->label('Department')
                                    ->relationship('profile.department', 'name')
                                    ->native(false)
                                    ->required(),
                                TextInput::make('password')
                                    ->required()
                                    ->password()
                            ])
                    ])
                    ->createOptionUsing(
                        function (array $data, callable $set) {
                            $user = self::storePatientRecord($data);
                            $set('patient_id', $user->id);
                        }
                    )
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $patientId = $get('patient_id');

                        if ($patientId) {
                            $user = User::where('id', $patientId)->with(['profile'])->first();

                            $set('first_name', $user->profile->first_name);
                            $set('last_name', $user->profile->last_name);
                        } else {
                            $set('first_name', null);
                            $set('last_name', null);
                        }
                    })
                    ->columnSpan('full')
                    ->native(false)
                    ->live(),
                TextInput::make('first_name')
                    ->label('Patient First Name')
                    ->visible(fn (callable $get): bool => ! is_null($get('patient_id')))
                    ->required(),
                TextInput::make('last_name')
                    ->label('Patient Last Name')
                    ->visible(fn (callable $get): bool => ! is_null($get('patient_id')))
                    ->required(),
                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Department')
                    ->required(),
                Select::make('doctor_id')
                    ->label('Doctor')
                    ->options(function (callable $get) {
                        $departmentId = $get('department_id');

                        if ($departmentId) {
                            return User::whereHas('roles', function ($query) {
                                return $query->where('name', User::ROLE_DOCTOR);
                            })->whereHas('profile', function ($query) use ($departmentId) {
                                return $query->where('department_id', $departmentId);
                            })->pluck('name', 'id')->toArray();
                        }
                    })
                    ->required()
                    ->native(false),
                Textarea::make('notes')
                    ->columnSpan('full'),
                DatePicker::make('appointment_date')
                    ->required()
                    ->native(false)
                    ->default(today())
                    ->minDate(today()),
                Select::make('status')
                    ->options([
                        'confirmed' => 'Confirmed',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled'
                    ])
                    ->required()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('doctor.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('appointment_date')
                    ->date()
                    ->sortable(),
                SelectColumn::make('status')
                    ->options([
                        'confirmed' => 'Confirmed',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                    ])
                    ->rules(['required'])
                    ->afterStateUpdated(function ($record, $state) {
                        $record->update(['status' => $state]);

                        Notification::make()
                            ->title('Status updated successfully')
                            ->success()
                            ->duration(2000)
                            ->send();
                    }),
                TextColumn::make('created_at')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }

    public static function storePatientRecord(array $data): User | null
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            $user->assignRole('Patient');

            Profile::create([
                'user_id' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'department_id' => $data['department_id']
            ]);
            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::debug($e->getMessage());

            return null;
        }
    }
}
