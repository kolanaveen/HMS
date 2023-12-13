<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientHistoryResource\Pages;
use App\Filament\Resources\PatientHistoryResource\RelationManagers;
use App\Models\PatientHistory;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientHistoryResource extends Resource
{
    protected static ?string $model = PatientHistory::class;

    protected static ?string $navigationGroup = 'Patients';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('patient_id')
                    ->relationship('patient', 'name')
                    ->native(false)
                    ->required(),
                DatePicker::make('date')
                    ->required()
                    ->default(today())
                    ->native(false),
                TextInput::make('title')
                    ->required(),
                Textarea::make('food_allergies')
                        ->maxLength(225),
                Textarea::make('bleed_tendency')
                    ->maxLength(225),
                Textarea::make('heart_disease')
                    ->maxLength(225),
                Textarea::make('blood_pressure')
                    ->maxLength(225),
                Textarea::make('diabetic')
                    ->maxLength(225),
                Textarea::make('surgery')
                    ->maxLength(225),
                Textarea::make('accident')
                    ->maxLength(225),
                Textarea::make('family_medical_history')
                    ->maxLength(225),
                Textarea::make('current_medication')
                    ->maxLength(225),
                Textarea::make('female_pregnancy')
                    ->maxLength(225),
                Textarea::make('breast_feeding')
                    ->maxLength(225),
                Textarea::make('health_insurance')
                    ->maxLength(225),
                Select::make('low_income')
                    ->options([
                        0 => 'No',
                        1 => 'Yes'
                    ])
                ->native(false),
                Textarea::make('reference')
                    ->maxLength(225),
                Textarea::make('others')
                    ->maxLength(225),
                Select::make('status')
                    ->options([
                        0 => 'Inactive',
                        1 => 'Active'
                    ])
                    ->native(false)
                ->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title'),
                TextColumn::make('status')
                ->getStateUsing(function ($record) {
                    if ($record->status) {
                        return 'Active';
                    } else {
                        return 'In Active';
                    }
                })->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Active' => 'success',
                    'In Active' => 'warning'
                })
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        '0' => 'Inactive',
                        '1' => 'Active',
                    ])
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
            'index' => Pages\ListPatientHistories::route('/'),
            'create' => Pages\CreatePatientHistory::route('/create'),
            'edit' => Pages\EditPatientHistory::route('/{record}/edit'),
        ];
    }
}
