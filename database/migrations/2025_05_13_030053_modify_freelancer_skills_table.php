<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('freelancer_skills', function (Blueprint $table) {
            $table->dropColumn(['proficiency_level', 'years_experience']);
        });
    }


    public function down(): void
    {
        Schema::table('freelancer_skills', function (Blueprint $table) {
            $table->enum('proficiency_level', ['beginner', 'intermediate', 'expert']);
            $table->integer('years_experience')->nullable();
        });
    }
};
