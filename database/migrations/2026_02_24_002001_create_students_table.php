<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->char('family_card_number', 16)->unique()->index();
            $table->string('name', 100);
            $table->char('nisn', 10)->unique()->index();
            $table->foreignId('classroom_id')->constrained()->onDelete('restrict');
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
