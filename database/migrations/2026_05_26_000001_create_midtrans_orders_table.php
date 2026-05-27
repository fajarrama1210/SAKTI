<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel untuk menyimpan setiap sesi order Midtrans.
     * Berfungsi sebagai audit trail pembayaran QRIS.
     */
    public function up(): void
    {
        Schema::create('midtrans_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique()->comment('Format: SAKTI-{bill_id}-{timestamp}-{random}');
            $table->unsignedBigInteger('bill_id')->comment('FK ke tabel bills');
            $table->unsignedBigInteger('student_id')->comment('FK ke tabel students');
            $table->bigInteger('amount')->comment('Jumlah yang harus dibayar (dalam Rupiah)');
            $table->string('snap_token')->nullable()->comment('Token Snap dari Midtrans');
            $table->string('transaction_id')->nullable()->comment('Transaction ID dari Midtrans');
            $table->enum('status', ['pending', 'settlement', 'expire', 'cancel', 'deny', 'failure'])
                  ->default('pending')
                  ->comment('Status dari Midtrans');
            $table->longText('raw_response')->nullable()->comment('Raw JSON response dari webhook Midtrans');
            $table->timestamps();

            // Index untuk performa query
            $table->index('bill_id');
            $table->index('student_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('midtrans_orders');
    }
};
