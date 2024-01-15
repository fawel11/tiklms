<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', 'AuthController@register');
Route::post('/v1/login', 'AuthController@login')->name('login');
Route::post('/v1/logout', 'AuthController@logout')->middleware('auth:sanctum')->name('logout');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {

    Route::group(['prefix' => 'users'], function () {

        Route::get('get-user-data', 'UserController@index')->name('user-get-user-data');
        Route::post('create', 'UserController@create')->name('user-create-user');
        Route::post('update', 'UserController@update')->name('user-update-user');

        //DESIGNATION==================
        Route::get('designations', 'UserController@designationList')->name('designation-get-designation-list');
        Route::get('{id}', 'UserController@getUser')->name('user-get-user');


    });
    Route::group(['prefix' => 'roles'], function () {

        Route::get('list', 'RoleController@index')->name('role-get-role-list');

    });

    Route::group(['namespace' => 'Leave', 'prefix' => 'leaves'], function () {

        Route::get('get-leave-data', 'LeaveController@index')->name('leave-get-leave-data');
        Route::post('apply-leave', 'LeaveController@applyLeave')->name('leave-apply-leave');
        Route::post('update-leave', 'LeaveController@updateLeave')->name('leave-update-leave');
        Route::post('approve-or-deny-leave', 'LeaveController@approveOrDenyLeave')->name('leave-approve-or-deny-leave');
    });

    Route::group(['namespace' => 'Attendance', 'prefix' => 'attendances'], function () {

        Route::get('get-attendance-data', 'AttendanceController@index')->name('attendance-get-attendance-data');

    });

});


