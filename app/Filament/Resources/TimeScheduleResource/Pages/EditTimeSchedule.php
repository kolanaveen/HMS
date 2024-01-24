<?php

namespace App\Filament\Resources\TimeScheduleResource\Pages;

use App\Filament\Resources\TimeScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimeSchedule extends EditRecord
{
    protected static string $resource = TimeScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
