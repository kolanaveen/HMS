<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    /**
     * Get the profiles associated with the department.
     */
    public function profiles()
    {
        return $this->hasMany(Department::class);
    }
}
