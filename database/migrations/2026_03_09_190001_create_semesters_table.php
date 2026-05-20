<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name', 40); 
            $table->tinyInteger('start_month'); 
            $table->tinyInteger('end_month');   
            $table->timestamps();
        });


        Schema::table('bills', function (Blueprint $table) {
            $table->foreignId('semester_id')->nullable()->after('academic_year_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
        });

        Schema::dropIfExists('semesters');
    }
};
