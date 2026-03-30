<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->char('family_card_number', 16)->index();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('month');
            $table->year('year');
            $table->unsignedInteger('total_amount');
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->date('due_date');
            $table->timestamps();

            $table->unique(['family_card_number', 'academic_year_id', 'month', 'year'], 'bills_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
