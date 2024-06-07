<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationalEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    final public function up(): void
    {
        Schema::create('educational_events', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->date('event_date');
            $table->text('didactic_materials')->nullable();
            $table->text('developed_skills')->nullable();
            $table->text('event_description')->nullable();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('educational_program_id')->constrained('educational_programs')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('edu_ev_child', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educational_event_id')->constrained('educational_events')->onDelete('cascade');
            $table->foreignId('child_id')->constrained('childrens')->onDelete('cascade');
            $table->string('estimation_mark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    final public function down(): void
    {
        Schema::dropIfExists('edu_ev_child');
        Schema::dropIfExists('educational_events');
    }
}
