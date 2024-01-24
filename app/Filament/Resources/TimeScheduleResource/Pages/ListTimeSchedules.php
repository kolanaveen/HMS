<?php

namespace App\Filament\Resources\TimeScheduleResource\Pages;

use App\Filament\Resources\TimeScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimeSchedules extends ListRecords
{
    protected static string $resource = TimeScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
