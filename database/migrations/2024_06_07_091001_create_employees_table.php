<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    final public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->foreignId('position_id')->nullable()->constrained('positions')->onDelete('set null');
            $table->string('phone')->nullable();
            $table->string('contract_number')->nullable();
            $table->string('passport_data')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_title')->nullable();
            $table->string('EDRPOU_bank_code')->nullable();
            $table->string('code_IBAN')->nullable();
            $table->string('medical_card_number')->nullable();
            $table->date('employment_date')->nullable();
            $table->date('date_dismissal')->nullable();
            $table->timestamps();
        });

        Schema::create('employees_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('date_start')->nullable();
            $table->date('date_finish')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    final public function down(): void
    {
        Schema::dropIfExists('employees_groups');
        Schema::dropIfExists('employees');
    }
}
