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
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();

            $table->integer('user_id')->nullable();
            $table->integer('employee_history_id')->nullable();
            $table->string('status')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->text('remarks')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->integer('policy_id')->nullable();
            $table->dateTime('applied_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_applications');
    }
};
