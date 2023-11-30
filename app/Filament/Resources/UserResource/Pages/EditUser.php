<?php

namespace App\Filament\Resources\UserResource\Pages;

use Exception;
use Filament\Actions;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $profile = Profile::where('user_id', $data['id'])->first()->toArray();

        if ($profile) {
            $data['profile'] = $profile;
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
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

            DB::commit();

            return $record;

        } catch (Exception $e) {
            DB::rollback();
            Log::debug($e->getMessage());

            return null;
        }
    }
}
