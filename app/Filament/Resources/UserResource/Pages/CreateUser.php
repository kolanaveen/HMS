<?php

namespace App\Filament\Resources\UserResource\Pages;

use Exception;
use App\Models\User;
use Filament\Actions;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $data['profile']['first_name'] . ' ' . $data['profile']['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            $role = Role::find($data['role']);
            $user->assignRole($role->name);

            Profile::create([
                'user_id' => $user->id,
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

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::debug($e->getMessage());

            return null;
        }
    }
}
