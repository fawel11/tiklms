<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('employee_history_id');
            $table->integer('shift_details_id')->nullable();
            $table->time('in_time_original')->nullable();
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->string('in_device_id')->nullable();
            $table->string('out_device_id')->nullable();
            $table->dateTime('present_date_time')->nullable();
            $table->date('present_date')->nullable();
            $table->integer('present_day')->nullable();
            $table->integer('present_month')->nullable();
            $table->integer('present_year')->nullable();
            $table->boolean('late_status')->nullable();
            $table->text('log_details')->nullable();
            $table->string('remarks',500)->nullable();

            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendances');
    }
};
