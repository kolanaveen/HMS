<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    /**
     * Get the patient associated with the document.
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id')
            ->whereHas('roles', function ($query) {
                return $query->where('name', User::ROLE_PATIENT);
            });
    }

    /**
     * Get the doctor associated with the document.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id')
            ->whereHas('roles', function ($query) {
                return $query->where('name', User::ROLE_DOCTOR);
            });
    }
}
