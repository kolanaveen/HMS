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

    protected function handleRecordCreation(array $data): Model
    {
        return TimeSchedule::create([
            'user_id' => $data['user_id'],
            'start_time' => $data['start_time'],
            'end_time'=> $data['end_time'],
            'day_off_number'=> $data['day_off_number'],
            'appointment_duration' => $data['appointment_duration'] ?? null,
        ]);
    }
}
