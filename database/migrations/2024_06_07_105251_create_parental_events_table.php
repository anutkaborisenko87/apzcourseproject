<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentalEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    final public function up(): void
    {
        Schema::create('parental_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('object')->nullable();
            $table->text('event_description')->nullable();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('parent_ev_parent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parental_event_id')->constrained('parental_events')->onDelete('cascade');
            $table->foreignId('parrent_id')->constrained('parrents')->onDelete('cascade');
            $table->text('result')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    final public function down(): void
    {
        Schema::dropIfExists('parent_ev_parent');
        Schema::dropIfExists('parental_events');
    }
}
