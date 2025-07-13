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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('max_score', 5, 2); // e.g., 100.00
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->integer('order')->default(0); // For ordering activities
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
