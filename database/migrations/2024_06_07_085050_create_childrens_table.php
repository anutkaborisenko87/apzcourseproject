<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChildrensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    final public function up(): void
    {
        Schema::create('childrens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('set null');
            $table->text('mental_helth')->nullable();
            $table->string('birth_certificate')->nullable();
            $table->string('medical_card_number')->nullable();
            $table->string('social_status')->nullable();
            $table->year('enrollment_year')->nullable();
            $table->date('enrollment_date')->nullable();
            $table->year('graduation_year')->nullable();
            $table->date('graduation_date')->nullable();
            $table->timestamps();
        });
        Schema::create('child_parent_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('childrens')->onDelete('cascade');
            $table->foreignId('parrent_id')->constrained('parrents')->onDelete('cascade');
            $table->string('relations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    final public function down(): void
    {
        Schema::dropIfExists('child_parent_relations');
        Schema::dropIfExists('childrens');
    }
}
