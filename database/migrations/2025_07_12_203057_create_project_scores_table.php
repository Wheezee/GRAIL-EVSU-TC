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
        Schema::create('project_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->boolean('is_late')->default(false);
            $table->integer('resubmission_count')->default(0);
            $table->timestamp('resubmission_date')->nullable();
            $table->timestamps();
            
            $table->unique(['project_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_scores');
    }
};
