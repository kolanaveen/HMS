<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the patient associated with the appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id')
            ->whereHas('roles', function ($query) {
                return $query->where('name', User::ROLE_PATIENT);
            });
    }

     /**
     * Get the doctor associated with the appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id')
            ->whereHas('roles', function ($query) {
                return $query->where('name', User::ROLE_DOCTOR);
            });
    }

    /**
     * Get the department associated with the appointment.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
