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
        Schema::create('class_section_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('enrollment_date')->default(now());
            $table->enum('status', ['enrolled', 'dropped', 'completed'])->default('enrolled');
            $table->timestamps();
            
            // Prevent duplicate enrollments
            $table->unique(['class_section_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_section_student');
    }
};
