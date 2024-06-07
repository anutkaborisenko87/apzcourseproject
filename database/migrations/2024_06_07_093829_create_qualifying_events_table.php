<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQualifyingEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    final public function up(): void
    {
        Schema::create('qualifying_events', function (Blueprint $table) {
            $table->id();
            $table->string('qualifying_event_title');
            $table->text('qualifying_event_description');
            $table->date('date_begining');
            $table->date('date_finish');
            $table->timestamps();
        });
        Schema::create('emp_qualif_evs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qualif_ev_id')->unique()->constrained('qualifying_events')->onDelete('cascade');
            $table->foreignId('employee_id')->unique()->constrained('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    final public function down(): void
    {
        Schema::dropIfExists('employee_qualifying_events');
        Schema::dropIfExists('qualifying_events');
    }
}
