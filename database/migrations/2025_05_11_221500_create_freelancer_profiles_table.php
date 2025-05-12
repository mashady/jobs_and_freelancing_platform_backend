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
        Schema::create('freelancer_profiles', function (Blueprint $table) {
             $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('set null');
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->text('bio')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('job_title')->nullable();
            $table->decimal('min_hourly_rate', 10, 2)->nullable();
            $table->decimal('max_hourly_rate', 10, 2)->nullable();
            $table->enum('english_level', ['beginner', 'intermediate', 'fluent', 'native'])->nullable();
            $table->string('payment_method')->nullable();
            $table->string('resume')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelancer_profiles');
    }
};
