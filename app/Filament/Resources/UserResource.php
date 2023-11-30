<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        FileUpload::make('profile.avatar')
                            ->disk('public')
                            ->directory('doctors/avatar')
                            ->image()
                    ])
                    ->columns(3),
                Section::make('Personal Details')
                    ->schema([
                        TextInput::make('profile.first_name')
                            ->required(),
                        TextInput::make('profile.last_name')
                            ->required(),
                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true),
                        TextInput::make('profile.national_id_number'),
                        TextInput::make('password')
                            ->required()
                            ->password()
                            ->confirmed()
                            ->visible(fn (?Model $record): bool => is_null($record) ),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->required()
                            ->visible(fn (?Model $record): bool => is_null($record) ),
                        Textarea::make('profile.address')
                            ->columnSpan(2),
                        DatePicker::make('profile.birth_date')
                            ->native(false),
                        Select::make('profile.gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female'
                            ]),
                        TextInput::make('profile.mobile_number')
                            ->tel(),
                        TextInput::make('profile.emergency_number')
                            ->tel(),
                        TextInput::make('profile.blood_group'),
                        Select::make('profile.department_id')
                            ->label('Department')
                            ->relationship('profile.department', 'name')
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('roles', function ($innerQuery) {
                return $innerQuery->where('name', '!=', 'Super Admin');
            }))
            ->columns([
                ImageColumn::make('profile.avatar'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email'),
                TextColumn::make('roles.name')
                    ->label('Role'),
                TextColumn::make('profile.national_id_number')
                    ->label('National ID Number'),
                TextColumn::make('profile.mobile_number')
                    ->label('Mobile Number'),
                TextColumn::make('profile.department.name'),
                TextColumn::make('created_at')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function(User $record) {
                        if (! is_null($record->profile->avatar)) {
                            Storage::disk('public')->delete($record->profile->avatar);
                        }
                        $record->profile()->delete();
                        $record->removeRole('Doctor');
                    })
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
