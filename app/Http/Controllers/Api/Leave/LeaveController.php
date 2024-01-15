<?php

namespace App\Http\Controllers\Api\Leave;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LeaveController extends Controller
{
    private $leave;

    public function __construct(LeaveApplication $leave)
    {
        $this->middleware('auth:sanctum');
        $this->leave = $leave;
    }

    public function index(Request $request)
    {
        $paginate = $request->paginate ?? 5;
        $photo = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOH2aZnIHWjMQj2lQUOWIL2f4Hljgab0ecZQ&usqp=CAU';
        //$photo='https://avatar-management--avatars.us-west-2.prod.public.atl-paas.net/623045446a682400697010b4/6c83b619-c275-43e9-95b4-e969dd00cb09/128';
        $leave_list = $this->leave
           // ->orderBy('id', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->paginate($paginate);

//            $unread_notification_count = $auth_user->whereNull('read_at')->count() ?? 0;


        $leave_list->transform(function ($value) use($photo){

            return [
                'id' => $value->id,
                'full_name' => ($value->user->first_name ?? '') .' '. ($value->user->last_name ?? '')  ,
                'designation' => $value->user->desingation->name ?? 'Software Engineer',
                'picture' => $value->user->picture ?? $photo,
                'policy' => $value->policy->name ?? '',
                'from_date' => $value->from_date ? date('d M Y', strtotime($value->from_date)) : '',
                'to_date' => $value->to_date ? date('d M Y', strtotime($value->to_date)) : '',
                'total_day' => $value->totalDay($value->from_date,$value->to_date) ?? '',
                'remarks' => $value->remarks ?? '',
                'status' => $value->status ?? '',
                'loading' => false,
                'loading_deny' => false,
                'cancelled_reason' => $value->cancelled_reason ?? '',
                'applied_at' => $value->applied_at ? date('d M Y h:i:s', strtotime($value->applied_at)) : '',
                'updated_at' => $value->updated_at ? date('d M Y h:i:s', strtotime($value->updated_at)) : '',
            ];
        });



        return response()->json($leave_list, 200);

    }


    public function applyLeave(Request $request, LeaveApplication $leave)
    {
        try {


            $this->validate($request, [
                'from_date' => 'required',
                'to_date' => 'required',
                'policy_id' => 'required',
                'leave_reason' => 'required',
            ]);


            // check if the application is valid
            /*  if (!$request->parent_id && !$request->is_displayable) {
                  return response()->json(['message' => 'The leave is approved already!'], 410);
              }*/

            $new_leve=($this->leave);
            //$new_leve->id = 300;
            $new_leve->status = 'pending';
            $new_leve->from_date =$request->from_date;
            $new_leve->to_date =$request->to_date;
            $new_leve->remarks =$request->leave_reason;
            $new_leve->policy_id =$request->policy_id;
            $new_leve->user_id =auth()->user()->id ?? 0;
            $new_leve->applied_at = (new Carbon())->now();


            $new_leve->save();
            return response()->json(['message' => 'You have successfully applied!'], 200);

        } catch (ValidationException $exception) {
            return JsonResponse::create(['message' => $exception->getMessage(), 'errors' => $exception->validator->getMessageBag()->toArray()], 422);
        } catch (Exception $exception) {
            return JsonResponse::create(['message' => $exception->getMessage()], 500);

        }
    }
    public function updateLeave(Request $request)
    {
        try {


            $this->validate($request, [
                'status' => 'required',
            ]);


            // check if the application is valid
            /*  if (!$request->parent_id && !$request->is_displayable) {
                  return response()->json(['message' => 'The leave is approved already!'], 410);
              }*/

            $the_leve=($this->leave->findOrFail($request->id));
            //$new_leve->id = 300;
            $the_leve->status = $request->status;
            //$new_leve->applied_at = (new Carbon())->now();


            $the_leve->save();
            return response()->json(['message' => $the_leve->user->first_name. '"s application has been '.$request->status], 200);

        } catch (ValidationException $exception) {
            return JsonResponse::create(['message' => $exception->getMessage(), 'errors' => $exception->validator->getMessageBag()->toArray()], 422);
        } catch (Exception $exception) {
            return JsonResponse::create(['message' => $exception->getMessage()], 500);

        }
    }
    public function approveOrDenyLeave(Request $request)
    {
        try {

            \Log::info($request);


            $this->validate($request, [
                'status' => 'required',
                'denyOrApp' => 'required',
                'cancelled_reason'  => 'required_if:denyOrApp,deny',
            ]);


            // check if the application is valid
            /*  if (!$request->parent_id && !$request->is_displayable) {
                  return response()->json(['message' => 'The leave is approved already!'], 410);
              }*/

            $the_leve=($this->leave->findOrFail($request->id));

            if ($request->denyOrApp=='deny'){
            if ($the_leve->status=='denied'){
                return response()->json(['message' => $the_leve->user->first_name. '"s application has already been '.$the_leve->status], 410);
            }
                $the_leve->status =  'denied' ;

            }
              if ($request->denyOrApp=='approve'){
                  if ($the_leve->status=='approved'){
                      return response()->json(['message' => ($the_leve->user->first_name ?? ''). '"s application has already been '.$the_leve->status], 410);
                  }
                  $the_leve->status =  'approved';
            }

            $the_leve->cancelled_reason = $request->cancelled_reason ?? null;
            $the_leve->save();
            return response()->json(['message' => $the_leve->user->first_name. '"s application has been '.$the_leve->status], 200);

        } catch (ValidationException $exception) {
            return JsonResponse::create(['message' => $exception->getMessage(), 'errors' => $exception->validator->getMessageBag()->toArray()], 422);
        } catch (Exception $exception) {
            return JsonResponse::create(['message' => $exception->getMessage()], 500);

        }
    }

}
