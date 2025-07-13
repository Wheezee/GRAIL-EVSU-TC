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
        Schema::table('subjects', function (Blueprint $table) {
            // Drop the global unique constraint on code
            $table->dropUnique(['code']);
            
            // Add composite unique constraint on code and teacher_id
            // This allows different teachers to have the same subject code
            $table->unique(['code', 'teacher_id'], 'subjects_code_teacher_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('subjects_code_teacher_unique');
            
            // Restore the global unique constraint on code
            $table->unique(['code']);
        });
    }
};
