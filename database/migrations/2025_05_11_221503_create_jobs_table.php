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
        Schema::create('jobs', function (Blueprint $table) {
            $table->foreignId('employer_id')->constrained('employer_profiles')->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('set null');
            $table->string('position_name');
            $table->string('location');
            $table->decimal('offered_salary', 10, 2);
            $table->text('job_description');
            $table->integer('experience_years');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->enum('type', ['fulltime', 'parttime', 'contract']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
