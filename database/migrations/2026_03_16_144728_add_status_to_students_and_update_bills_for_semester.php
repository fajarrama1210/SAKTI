<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modifikasi tabel students: hapus unique KK, tambah status
        Schema::table('students', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'lulus', 'keluar'])->default('aktif')->after('qr_code');
        });

        try {
            DB::statement('ALTER TABLE students DROP INDEX students_family_card_number_unique');
        } catch (\Exception $e) {
            // Index mungkin sudah tidak ada
        }

        // 2. Drop tabel keuangan lama (data dummy)
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('bills');
        Schema::enableForeignKeyConstraints();

        // 3. Buat ulang bills — PER SISWA PER BULAN
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('restrict');
            $table->foreignId('academic_year_id')->constrained()->onDelete('restrict');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('restrict');
            $table->tinyInteger('month');
            $table->year('year');
            $table->unsignedInteger('total_amount');
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->date('due_date');
            $table->timestamps();

            // 1 siswa hanya punya 1 tagihan per bulan per tahun
            $table->unique(['student_id', 'month', 'year'], 'bills_student_month_year_unique');
        });

        // 4. Buat ulang bill_items (rincian jenis bayar)
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('restrict');
            $table->foreignId('payment_type_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('amount');
            $table->timestamps();
        });

        // 5. Buat ulang payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->onDelete('restrict');
            $table->unsignedInteger('amount');
            $table->enum('payment_method', ['cash', 'transfer', 'other'])->default('cash');
            $table->date('payment_date');
            $table->string('reference_number')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->unique('family_card_number');
        });

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('bills');
        Schema::enableForeignKeyConstraints();

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
};
