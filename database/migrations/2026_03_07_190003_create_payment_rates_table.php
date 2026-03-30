<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_type_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('grade_level');
            $table->foreignId('major_id')->nullable()->constrained()->onDelete('cascade');
            $table->unsignedInteger('amount');
            $table->timestamps();

            $table->unique(['academic_year_id', 'payment_type_id', 'grade_level', 'major_id'], 'payment_rates_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_rates');
    }
};
