<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    final public function up(): void
    {
        Schema::create('additional_classes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('orientation');
            $table->string('age_restrictions');
            $table->integer('limit_visitors');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('educational_program_id')->nullable()->constrained('educational_programs')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('add_class_child', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('childrens')->onDelete('cascade');
            $table->foreignId('additional_class_id')->constrained('additional_classes')->onDelete('cascade');
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
        Schema::dropIfExists('add_class_child');
        Schema::dropIfExists('additional_classes');
    }
}
