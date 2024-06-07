<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCulturalEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    final public function up(): void
    {
        Schema::create('cultural_events', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->date('event_date');
            $table->text('didactic_materials')->nullable();
            $table->text('event_description')->nullable();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('cult_ev_child', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultural_event_id')->constrained('cultural_events')->onDelete('cascade');
            $table->foreignId('child_id')->constrained('childrens')->onDelete('cascade');
            $table->string('role');
        });
        Schema::create('cult_ev_visitior', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cultural_event_id')->constrained('cultural_events')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('parrents')->onDelete('cascade');
            $table->string('reaction');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    final public function down(): void
    {
        Schema::dropIfExists('cultural_events');
    }
}
