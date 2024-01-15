<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\EmployeeHistory;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use File;

class UserController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->middleware('auth:sanctum');
        $this->user = $user;
    }

    public function index(Request $request)
    {
        $paginate = $request->paginate ?? 10;
        $user_list = $this->user
            ->orderBy('created_at', 'DESC')
            ->paginate($paginate);

        $photo = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOH2aZnIHWjMQj2lQUOWIL2f4Hljgab0ecZQ&usqp=CAU';


        $user_list->transform(function ($value) use ($photo) {

            $roles = implode(',', ($value->roles->pluck('name')->toArray() ?? []));
            return [
                'id' => $value->id,
                'employee_id' => $value->employee_id,
                'full_name' => ($value->first_name ?? '') . ' ' . ($value->last_name ?? ''),
                'designation' => $value->desingation->name ?? 'Software Engineer',
                'role' => $roles,
                'picture' => $value->picture ?? $photo,
                'status' => $value->status ?? '',
                'email' => $value->email ?? '',
                'updated_at' => $value->updated_at ? date('d M Y h:i:s', strtotime($value->updated_at)) : '',
            ];
        });


        return response()->json($user_list, 200);

    }

    public function getUser(Request $request, $id)
    {


        $value = $this->user
            ->find($id);


        $user = [
            'id' => $value->id,
            'first_name' => ($value->first_name ?? ''),
            'last_name' => ($value->last_name ?? ''),
            'full_name' => ($value->first_name ?? '') . ' ' . ($value->last_name ?? ''),
            'designation' => $value->desingation->name ?? 'Software Engineer',
            'joining_date' => $value->joining_date ?? '',
            'birth_date' => $value->birth_date ?? '',
            'role_ids' => implode(',', ($value->roles->pluck('id')->toArray() ?? [])),
            'picture' => $value->picture ?? null,
            'status' => $value->status ?? '',
            'address' => $value->address ?? '',
            'email' => $value->email ?? '',
            'updated_at' => $value->updated_at ? date('d M Y h:i:s', strtotime($value->updated_at)) : '',
        ];


        return response()->json($user, 200);

    }


    public function create(Request $request)
    {

        try {

            // \Log::info($request);


            $this->validate($request, [
                'email' => 'required | unique:users,email',
                'first_name' => 'required',
                'last_name' => 'required',
                'role_ids' => 'required',
                'address' => 'required',
                'birth_date' => 'required',
                'joining_date' => 'required',
                'designation_id' => 'required',
                // 'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);


            // check if the application is valid
            /*  if (!$request->parent_id && !$request->is_displayable) {
                  return response()->json(['message' => 'The user is approved already!'], 410);
              }*/

            $new_user = ($this->user);
            //$new_leve->id = 300;
            $new_user->email = $request->email;
            $new_user->password = bcrypt($request->email ?? '12345678');
            $new_user->first_name = $request->first_name;
            $new_user->last_name = $request->last_name;
            $new_user->address = $request->address;
            $new_user->birth_date = $request->birth_date;
            $new_user->joining_date = $request->joining_date;
            $new_user->status = true;
            $new_user->picture = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOH2aZnIHWjMQj2lQUOWIL2f4Hljgab0ecZQ&usqp=CAU';;
            //  $new_user->user_id = auth()->user()->id ?? 0;
            //  $new_user->applied_at = (new Carbon())->now();


            if ($new_user->save()) {
                // Employee history

                $history = new EmployeeHistory([
                    'designation_id' => $request->designation_id,
                    'joining_date' => $request->joining_date,
                    'status' => true,
                    'project_id' => $request->project_id ?? 1,]);
                $new_user->empHistories()->save($history);

            }
            return response()->json(['message' => 'Employee has successfully been created!'], 200);

        } catch (ValidationException $exception) {
            return response()->json(['message' => $exception->getMessage(), 'errors' => $exception->validator->getMessageBag()->toArray()], 422);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'errors' => []], 500);

        }
    }

    public function update(Request $request)
    {
        try {

            \Log::info($request);


            $this->validate($request, [
                'email' => 'required | unique:users,email,' . $request->id,
                'first_name' => 'required',
                'last_name' => 'required',
                'role_ids' => 'required',
                'address' => 'required',
                'birth_date' => 'required',
                'joining_date' => 'required',
                // 'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $the_user = $this->user->find($request->id);

            if ($request->request) {
                $the_user->password = bcrypt($request->password);
            }

            $the_user->email = $request->email;
            $the_user->first_name = $request->first_name;
            $the_user->last_name = $request->last_name;
            $the_user->address = $request->address;
            $the_user->roles()->sync($request->role_ids);
            $the_user->address = $request->address;
            $the_user->birth_date = $request->birth_date;
            $the_user->joining_date = $request->joining_date;
            $the_user->status = $request->status;
            $the_user->picture = $request->picture;;

            $the_user->save();
            return response()->json(['message' => $the_user->first_name . ' has successfully been updated!'], 200);

        } catch (ValidationException $exception) {
            return response()->json(['message' => $exception->getMessage(), 'errors' => $exception->validator->getMessageBag()->toArray()], 422);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'errors' => []], 500);

        }
    }

    public function drop(Request $request, $id)
    {
        try {

            $the_user = $this->user->find($request->id);

        } catch (ValidationException $exception) {
            return response()->json(['message' => $exception->getMessage(), 'errors' => $exception->validator->getMessageBag()->toArray()], 422);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'errors' => []], 500);

        }
    }

    //DESIGNATION==================

    public function designationList(Request $request)
    {
        $paginate = $request->paginate ?? 10;
        $role_list = (new Designation())
            ->orderBy('created_at', 'DESC')
            ->get();


        $role_list->transform(function ($value) {

            return [
                'id' => $value->id,
                'name' => $value->name
            ];
        });


        return response()->json($role_list, 200);


    }

}
