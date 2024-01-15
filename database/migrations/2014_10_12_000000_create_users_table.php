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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->string('emergency_number')->nullable();
            $table->integer('project_id')->nullable();
            $table->integer('project_manager_id')->nullable();
            $table->string('address')->nullable();
            $table->integer('designation_id')->nullable();
            $table->string('picture')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('marital_status')->default(0);
            $table->string('gender')->default('male');
            $table->date('joining_date')->nullable();
            $table->date('birth_date')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
