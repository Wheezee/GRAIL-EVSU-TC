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
        Schema::create('activity_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 5, 2)->nullable(); // e.g., 85.50
            $table->boolean('is_late')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            // Prevent duplicate scores for same student and activity
            $table->unique(['activity_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_scores');
    }
};
