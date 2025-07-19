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
        Schema::create('grading_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('activities')->default(20);
            $table->unsignedTinyInteger('quizzes')->default(20);
            $table->unsignedTinyInteger('exams')->default(30);
            $table->unsignedTinyInteger('recitation')->default(15);
            $table->unsignedTinyInteger('projects')->default(15);
            $table->timestamps();
            $table->unique('subject_id'); // Only one set of weights per subject
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grading_weights');
    }
}; 