<?php

namespace App\Filament\Resources\TimeScheduleResource\Pages;

use App\Filament\Resources\TimeScheduleResource;
use App\Models\TimeSchedule;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTimeSchedule extends CreateRecord
{
    protected static string $resource = TimeScheduleResource::class;

    /**
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['role_id']);

        return $data;
    }
}
