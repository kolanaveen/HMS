<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'first_name',
        'last_name',
        'national_id_number',
        'avatar',
        'address',
        'birth_date',
        'gender',
        'mobile_number',
        'emergency_number',
        'blood_group',
        'department_id'
    ];

    /**
     * Get the user associated with the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user associated with the profile.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
