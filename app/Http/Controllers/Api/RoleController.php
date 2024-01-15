<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $role;

    public function __construct(Role $role)
    {
        // $this->middleware('auth:sanctum');
        $this->role = $role;
    }

    public function index(Request $request)
    {
        $paginate = $request->paginate ?? 10;
        $role_list = $this->role
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
