<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('category', 60)->nullable()->after('type');
            $table->foreignId('payment_id')->nullable()->after('amount')->constrained()->nullOnDelete();
            $table->foreignId('recorded_by')->nullable()->after('payment_id')->constrained('users')->nullOnDelete();

            // Hapus kolom lama
            $table->dropForeign(['spp_payment_id']);
            $table->dropColumn('spp_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');
            $table->dropForeign(['recorded_by']);
            $table->dropColumn('recorded_by');
            $table->dropColumn('category');

            $table->foreignId('spp_payment_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
