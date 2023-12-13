<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientHistory extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo|Builder
     */
    public function patient(): BelongsTo|Builder
    {
        return $this->belongsTo(User::class, 'patient_id')
            ->whereHas('roles', function ($query) {
                return $query->where('name', User::ROLE_PATIENT);
            });
    }
}
