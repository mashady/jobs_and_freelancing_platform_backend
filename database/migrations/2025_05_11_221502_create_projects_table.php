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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('employer_profiles')->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->decimal('budget_min', 10, 2);
            $table->decimal('budget_max', 10, 2);
            $table->integer('duration')->comment('in days');
            $table->enum('english_level', ['beginner', 'intermediate', 'fluent', 'native']);
            $table->string('project_language');
            $table->enum('general_level', ['entry', 'intermediate', 'expert']);
            $table->enum('status', ['open', 'in-progress', 'completed', 'cancelled'])->default('open');
            $table->date('deadline');
            $table->enum('project_type', ['fixed', 'hourly'])->default('fixed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
