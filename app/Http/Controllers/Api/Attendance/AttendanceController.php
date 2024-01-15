<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeHistory;
use App\Models\LeaveApplication;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private $attendance;
    private $employee;
    private $employeeHistory;

    public function __construct(EmployeeAttendance $attendance, User $employee, EmployeeHistory $employeeHistory)
    {
        $this->middleware('auth:sanctum');
        $this->attendance = $attendance;
        $this->employee = $employee;
        $this->employeeHistory = $employeeHistory;
    }

    public function index(Request $request)
    {
        // try {

        $paginate = $request->paginate ?? 2;
        $search_txt = $request->search_txt ?? null;
        $txt = '%' . $search_txt . '%';

        $today = Carbon::now()->toDateString();
        $att_date = $request->att_date ?? $today;


        $photo = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOH2aZnIHWjMQj2lQUOWIL2f4Hljgab0ecZQ&usqp=CAU';
        //$photo='https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/623045446a682400697010b4/6c83b619-c275-43e9-95b4-e969dd00cb09/128';
        $att_list = $this->employeeHistory
            // ->orderBy('id', 'DESC')
            ->when($search_txt, function ($qu) use ($txt) {
                $qu->whereHas('employee', function ($q) use ($txt) {
                    return $q->where(function ($query) use ($txt) {
                        $query->where('employee_id', 'LIKE', $txt)
                            ->orWhere('email', 'LIKE', $txt)
                            ->orWhere('phone_number', 'LIKE', $txt)
                            ->orWhere('first_name', $txt)
                            ->orWhere('last_name', $txt);
                    });
                });
            })
            ->with(['attendance' => function ($q) use ($att_date) {
                $q->where('present_date', $att_date);
            }, 'leaveDetail' => function ($q) use ($att_date) {
                $q->where('leave_date', $att_date);
            }])
            ->orderBy('created_at', 'DESC')
            ->paginate($paginate);

//            $unread_notification_count = $auth_user->whereNull('read_at')->count() ?? 0;


        $att_list->transform(function ($value) use ($photo,$att_date) {

            $start_time = new Carbon($value->attendance?->in_time);
            $end_time = new Carbon($value->attendance?->out_time);
            $time_difference_in_minutes = $end_time->diffInHours($start_time);//you also find difference in hours using diffInHours()


            return [
                'id' => $value->id,
                'attendance_id' => $value->attendance->id ?? null,

                'full_name' => ($value->employee->first_name ?? '') . ' ' . ($value->last_name ?? ''),
                'designation' => $value->desingation->name ?? 'Software Engineer',
                'picture' => $value->employee->picture ?? $photo,
                'in_time' => $value->attendance?->in_time ? date('h:i A', strtotime($value->attendance->in_time)) : '',
                'out_time' => $value->attendance?->out_time ? date('h:i A', strtotime($value->attendance->out_time)) : '',
                'present' => ($value->attendance ? 1 : 0),
                'in_leave' => ($value->leaveDetail ? 1 : 0),
                'att_status' => ($value->leaveDetail) ? "In Leave" : (($value->attendance) ? "Present" : ($value->hasApplied($att_date) ? 'Leave- Request Raised': "Absent") ),

                'present_date' => $value->attendance->present_date ?? null,
                'present_type' => $value->attendance->present_type ?? 'web',
                'work_time' => ($time_difference_in_minutes) . ' hours' ?? '',
                'present_date_time' => $value->attendance?->present_date_time ? date('d M Y', strtotime($value->attendance->present_date_time)) : '',
                'remarks' => $value->attendance->note ?? null,
                'late_status' => $value->late_status ?? '',
                'loading' => false,
                'show' => false,
                'updated_by' => $value->attendance->updatedBy->first_name ?? '',
                'updated_at' => $value->attendance?->updated_at ? date('d M Y h:i:s', strtotime($value->attendance->updated_at)) : '',
            ];
        });


        return response()->json($att_list, 200);
        /*  } catch (Exception $exception) {
              return response()->json(['message' => $exception->getMessage()], 500);

          }*/


    }

}
