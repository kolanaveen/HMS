<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use Exception;
use Filament\Actions;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\DoctorResource;
use Illuminate\Support\Facades\Log;

class EditDoctor extends EditRecord
{
    protected static string $resource = DoctorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $profile = Profile::where('user_id', $data['id'])->first()->toArray();

        if ($profile) {
            $data['profile'] = $profile;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::beginTransaction();

        try {
            $record->update([
                'name' => $data['profile']['first_name'] . ' ' . $data['profile']['last_name'],
                'email' => $data['email']
            ]);

            $profile = $record->profile;

            $profile->update([
                'avatar' => $data['profile']['avatar'],
                'first_name' => $data['profile']['first_name'],
                'last_name' => $data['profile']['last_name'],
                'national_id_number' => $data['profile']['national_id_number'],
                'address' => $data['profile']['address'],
                'birth_date' => $data['profile']['birth_date'],
                'gender' => $data['profile']['gender'],
                'mobile_number' => $data['profile']['mobile_number'],
                'emergency_number' => $data['profile']['emergency_number'],
                'blood_group' => $data['profile']['blood_group'],
                'department_id' => $data['profile']['department_id']
            ]);

            return $record;

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::debug($e->getMessage());

            return null;
        }
    }
}
