<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Add subject_id column (nullable first)
            $table->foreignId('subject_id')->after('id')->nullable()->constrained()->onDelete('cascade');
        });

        // Populate subject_id from class_section_id
        DB::statement('
            UPDATE quizzes 
            SET subject_id = (
                SELECT subject_id 
                FROM class_sections 
                WHERE class_sections.id = quizzes.class_section_id
            )
        ');

        // Make subject_id not nullable
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable(false)->change();
        });

        // Drop the class_section_id column
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['class_section_id']);
            $table->dropColumn('class_section_id');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('class_section_id')->after('id')->constrained()->onDelete('cascade');
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });
    }
};
