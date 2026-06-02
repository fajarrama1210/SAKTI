<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Carbon\Carbon;

class SplitPaymentTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;
    private $studentId;
    private $academicYearId;
    private $semesterId;
    private $classroomId;
    private $paymentTypeIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBaseData();
    }

    /** @test */
    public function it_can_pay_specifically_by_payment_type_and_allocate_correctly()
    {
        $billUseCase = app(\App\UseCases\BillUseCase::class);

        // Generate tagihan
        $genResult = $billUseCase->autoGenerateBillsForSemester($this->semesterId);
        $this->assertTrue($genResult['status']);

        // Ambil salah satu bill
        $bill = DB::table('bills')->first();
        $this->assertNotNull($bill);

        // Ambil bill items
        $billItems = DB::table('bill_items as bi')
            ->join('payment_types as pt', 'bi.payment_type_id', '=', 'pt.id')
            ->select('bi.*', 'pt.name as pt_name')
            ->where('bi.bill_id', $bill->id)
            ->get();

        $this->assertCount(3, $billItems); // SPP, Uang Gedung, Kegiatan

        $sppItem = $billItems->firstWhere('pt_name', 'SPP');
        $uangGedungItem = $billItems->firstWhere('pt_name', 'Uang Gedung');

        // Lakukan pembayaran khusus untuk SPP
        $payResult = $billUseCase->recordPayment($bill->id, [
            'bill_item_id'   => $sppItem->id,
            'payment_method' => 'cash',
            'payment_date'   => now()->toDateString(),
            'verified_by'    => $this->adminUser->id,
            'amount'         => null, // Null = Bayar Lunas sisa item ini
        ]);

        $this->assertTrue($payResult['status']);

        // Verifikasi alokasi pembayaran
        $payment = DB::table('payments')->where('bill_id', $bill->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals($sppItem->amount, $payment->amount);

        // Cek alokasi hanya ke SPP
        $allocations = DB::table('payment_allocations')->where('payment_id', $payment->id)->get();
        $this->assertCount(1, $allocations);
        $this->assertEquals($sppItem->id, $allocations[0]->bill_item_id);
        $this->assertEquals($sppItem->amount, $allocations[0]->amount);

        // Verifikasi deskripsi jurnal transaksi (Buku Kas) mengandung nama jenis pembayaran
        $transaction = DB::table('transactions')->where('payment_id', $payment->id)->first();
        $this->assertNotNull($transaction);
        $this->assertStringContainsString('(SPP)', $transaction->description);

        // Verifikasi data invoice (melalui query dari InvoiceController)
        $invoiceItems = DB::table('payment_allocations as pa')
            ->join('bill_items as bi', 'pa.bill_item_id', '=', 'bi.id')
            ->join('payment_types as pt', 'bi.payment_type_id', '=', 'pt.id')
            ->select('pa.amount', 'pt.name as type_name')
            ->where('pa.payment_id', $payment->id)
            ->get();

        $this->assertCount(1, $invoiceItems);
        $this->assertEquals('SPP', $invoiceItems[0]->type_name);
        $this->assertEquals($sppItem->amount, $invoiceItems[0]->amount);
    }

    private function seedBaseData(): void
    {
        $this->adminUser = \App\Models\User::factory()->create([
            'name'  => 'Admin Tester',
            'email' => 'admin@test.com',
            'role'  => 'admin',
        ]);

        $majorId = DB::table('majors')->insertGetId([
            'name'       => 'RPL',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->classroomId = DB::table('classrooms')->insertGetId([
            'major_id'    => $majorId,
            'grade_level' => 10,
            'name'        => "Kelas 10 RPL",
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->studentId = DB::table('students')->insertGetId([
            'nisn'               => '0012345678',
            'id_number'          => 'NIS000001',
            'name'               => 'Siswa Test',
            'classroom_id'       => $this->classroomId,
            'family_card_number' => '3271234567890123',
            'qr_code'            => 'QR-TEST',
            'status'             => 'aktif',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $this->academicYearId = DB::table('academic_years')->insertGetId([
            'name'       => 'TA 2025/2026',
            'start_date' => '2025-07-01',
            'end_date'   => '2026-06-30',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->semesterId = DB::table('semesters')->insertGetId([
            'academic_year_id' => $this->academicYearId,
            'name'             => 'Ganjil 2025/2026',
            'start_month'      => 7,
            'end_month'        => 12,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Jenis Pembayaran
        foreach (['SPP', 'Uang Gedung', 'Kegiatan'] as $pt) {
            $this->paymentTypeIds[] = DB::table('payment_types')->insertGetId([
                'name'       => $pt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tarif Pembayaran
        $amounts = [200000, 1000000, 150000];
        foreach ($this->paymentTypeIds as $idx => $ptId) {
            DB::table('payment_rates')->insert([
                'academic_year_id' => $this->academicYearId,
                'payment_type_id'  => $ptId,
                'grade_level'      => 10,
                'major_id'         => null,
                'amount'           => $amounts[$idx],
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        // Enrollment
        DB::table('student_enrollments')->insert([
            'student_id'       => $this->studentId,
            'classroom_id'     => $this->classroomId,
            'academic_year_id' => $this->academicYearId,
            'status'           => 'aktif',
            'enrolled_at'      => '2025-07-15',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }
}
