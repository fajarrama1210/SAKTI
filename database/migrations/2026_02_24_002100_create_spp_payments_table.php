<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spp_payments', function (Blueprint $table) {
            $table->id();
            $table->char('family_card_number', 16)->index();
            $table->tinyInteger('month');
            $table->year('year');
            $table->dateTime('payment_date')->nullable();
            $table->enum('payment_method', ['cash', 'qris'])->nullable();
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->integer('total_amount');
            $table->string('snap_token')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('spp_payments');
    }
};
