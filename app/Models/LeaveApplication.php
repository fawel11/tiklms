<?php

namespace App\Models;


use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LeaveApplication extends Model
{
    use HasFactory;

    protected $table = 'leave_applications';
    public $timestamps = true;

    const LEAVE_TYPE = 'type';
    const LEAVE_DETAILS = 'leave_details';
    const MAXIMUM_DAYS = 'max_days';
    const STATUS = 'status';

    protected $nonEditableFields = [];

    protected $fillable = [
        self::LEAVE_TYPE,
        self::LEAVE_DETAILS,
        self::MAXIMUM_DAYS,
        self::STATUS
    ];


    function setAppliedtAtAtribute($date)
    {
        $this->attributes['applied_at'] = (new Carbon())->now();
    }


    function user()
    {
        return $this->belongsTo(User::class);
    }


    function policy()
    {
        return $this->belongsTo(LeavePolicy::class);
    }

    function totalDay($from, $to)
    {

        $datetime1 = new DateTime($from);
        $datetime2 = new DateTime($to);
        $interval = $datetime1->diff($datetime2);
        $days = $interval->format('%a');//now do whatever you like with $days
        return $days + 1;
    }

}
