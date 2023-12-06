<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Document;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DocumentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DocumentResource\RelationManagers;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationGroup = 'Patients';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('patient_id')
                    ->relationship('patient', 'name')
                    ->required(),
                Select::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->required(),
                DatePicker::make('date')
                    ->required()
                    ->native(false),
                Select::make('status')
                    ->options([
                        0 => 'Inactive',
                        1 => 'Active'
                    ])
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpan('full'),
                FileUpload::make('document')
                    ->acceptedFileTypes(['application/pdf'])
                    ->storeFiles(false)
                    ->columnSpan('full')
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
                TextColumn::make('date')
                    ->date(),
                TextColumn::make('description')
                    ->wrap(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function (string $state): string {
                        return $state == 0 ? 'Inactive' : 'Active';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success'
                    }),
                TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true)
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
                    ->before(function(Document $record) {
                        if (! is_null($record->filepath)) {
                            Storage::disk('public')->delete($record->filepath);
                        }
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
