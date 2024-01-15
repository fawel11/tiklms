<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeHistory extends Model
{
    use HasFactory;

    protected $table = 'employee_histories';


    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

//ATTENDANCE =============================


    public function attendance()
    {
        return $this->hasOne(EmployeeAttendance::class, 'employee_history_id');
    }

    public function attendances()
    {
        return $this->hasMany(EmployeeAttendance::class, 'employee_history_id');
    }


    public function presentToday()
    {
        $today = Carbon::now()->toDateString();
        return $this->hasOne(EmployeeAttendance::class, 'employee_history_id')
            ->whereDate('present_date', $today);
    }

    public function leaveDetail()
    {
        return $this->hasOne(LeaveDetail::class, 'employee_history_id');
    }

    public function hasApplied($date)//
    {
        return $this->hasOne(LeaveApplication::class, 'employee_history_id')
            ->where('from_date', '<=', $date)
            ->where('to_date', '>=', $date)->exists();
    }
}
