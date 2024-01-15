<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
    use HasFactory;


    function user()
    {
        return $this->belongsTo(User::class);
    }

    function updatedBy()
    {
        return $this->belongsTo(User::class,'updated_by');
    }
}
