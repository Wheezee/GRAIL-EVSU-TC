<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Add subject_id column (nullable first, then we'll populate it)
            $table->foreignId('subject_id')->after('id')->nullable()->constrained()->onDelete('cascade');
        });

        // Populate subject_id from class_section_id
        DB::statement('
            UPDATE activities 
            SET subject_id = (
                SELECT subject_id 
                FROM class_sections 
                WHERE class_sections.id = activities.class_section_id
            )
        ');

        // Make subject_id not nullable
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable(false)->change();
        });

        // Drop the class_section_id column
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['class_section_id']);
            $table->dropColumn('class_section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Add back class_section_id column
            $table->foreignId('class_section_id')->after('id')->constrained()->onDelete('cascade');
            
            // Drop the subject_id column
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });
    }
};
