<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UseCases\BillUseCase;
use App\UseCases\StudentUseCase;
use App\UseCases\SemesterUseCase;
use App\UseCases\ClassroomUseCase;
use App\Entities\ResponseEntity;
use Illuminate\Http\Request;

class BillController extends Controller
{
    protected $billUseCase;
    protected $studentUseCase;
    protected $semesterUseCase;
    protected $classroomUseCase;

    public function __construct(
        BillUseCase $billUseCase, 
        StudentUseCase $studentUseCase,
        SemesterUseCase $semesterUseCase,
        ClassroomUseCase $classroomUseCase
    ) {
        $this->billUseCase    = $billUseCase;
        $this->studentUseCase = $studentUseCase;
        $this->semesterUseCase = $semesterUseCase;
        $this->classroomUseCase = $classroomUseCase;
    }

    /**
     * Halaman utama: Pencarian siswa untuk pembayaran SPP
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $students = null;

        if ($search) {
            $students = $this->studentUseCase->search($search);

            // Untuk setiap siswa, ambil tagihan bulan ini
            foreach ($students as $student) {
                $bills = $this->billUseCase->getStudentBills($student->id);
                $student->bills = $bills;

                // Hitung saudara seKK
                $siblings = $this->billUseCase->getStudentsInSameKK($student->family_card_number);
                $student->sibling_count = $siblings->count();
            }
        }

        return view('_admin.bill.index', compact('search', 'students'));
    }

    /**
     * Halaman rekap tagihan (daftar semua tagihan dengan filter)
     */
    public function recap(Request $request)
    {
        $filters = $request->only(['month', 'year', 'status', 'search']);

        // Default filter bulan & tahun saat ini
        if (empty($filters['month'])) $filters['month'] = now()->month;
        if (empty($filters['year']))  $filters['year']  = now()->year;

        $bills = $this->billUseCase->getPaginated(20, $filters);

        return view('_admin.bill.recap', compact('bills', 'filters'));
    }

    /**
     * Detail tagihan seorang siswa (kalender SPP)
     */
    public function studentDetail($studentId)
    {
        $student = $this->studentUseCase->getById($studentId);
        if (!$student) abort(404);

        $bills    = $this->billUseCase->getStudentBills($studentId);
        $siblings = $this->billUseCase->getStudentsInSameKK($student->family_card_number);

        return view('_admin.bill.student_detail', compact('student', 'bills', 'siblings'));
    }

    /**
     * Proses pembayaran (langsung dari kalender siswa)
     */
    public function pay(Request $request, $billId)
    {
        $request->validate([
            'payment_type'   => 'required|in:full,partial',
            'payment_method' => 'required|in:cash,qris,transfer,other',
            'amount'         => 'nullable|numeric|min:1',
            'payment_date'   => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:500',
        ]);

        // Jika full: set amount ke null, BillUseCase akan bayar seluruh sisa
        $amount = ($request->payment_type === 'full') ? null : $request->amount;

        // Jika partial: wajib ada amount
        if ($request->payment_type === 'partial' && empty($amount)) {
            return redirect()->back()->withErrors(['amount' => 'Jumlah cicilan harus diisi.'])->withInput();
        }

        $data = [
            'amount'           => $amount,
            'payment_method'   => $request->payment_method,
            'payment_date'     => $request->payment_date,
            'reference_number' => $request->reference_number,
            'notes'            => $request->notes,
            'verified_by'      => auth()->id(),
        ];

        $result = $this->billUseCase->recordPayment($billId, $data);

        if (!$result['status']) {
            $msg = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->with('error', $msg);
        }

        // Ambil status terbaru untuk pesan yang sesuai
        $bill = $this->billUseCase->getById($billId);
        $message = ($bill->status === 'paid')
            ? 'Pembayaran LUNAS berhasil dicatat!'
            : 'Cicilan berhasil dicatat! Sisa tagihan akan ditagih pada pembayaran berikutnya.';

        return redirect()->route('admin.spp.student', $bill->student_id)
            ->with('success', $message);
    }

    /**
     * Update jatuh tempo (opsi admin manual)
     */
    public function updateDueDate(Request $request, $billId)
    {
        $request->validate(['due_date' => 'required|date']);

        $result = $this->billUseCase->updateDueDate($billId, $request->due_date);

        if (!$result['status']) {
            return redirect()->back()->with('error', 'Gagal mengubah jatuh tempo.');
        }

        return redirect()->back()->with('success', 'Jatuh tempo berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $result = $this->billUseCase->delete($id);

        if (!$result['status']) {
            $msg = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->with('error', $msg);
        }

        return redirect()->back()->with('success', ResponseEntity::MSG_SUCCESS_DELETE);
    }

    /**
     * Tampilan Matrix Pembayaran (Grid)
     */
    public function matrix(Request $request)
    {
        $filters = $request->only(['classroom_id', 'semester_id']);
        
        $classrooms = $this->classroomUseCase->getAll();
        $semesters  = $this->semesterUseCase->getAll();
        
        $data = $this->billUseCase->getPaymentMatrix($filters);

        return view('_admin.bill.matrix', compact('classrooms', 'semesters', 'filters', 'data'));
    }

    /**
     * Sinkronisasi Tagihan (Mencegah Bug: Siswa/Tarif Baru belum ada tagihan)
     */
    public function sync(Request $request)
    {
        // Cari tahun ajaran yang sedang aktif
        $activeAY = \Illuminate\Support\Facades\DB::table(\App\Entities\DatabaseEntity::TBL_ACADEMIC_YEARS)
            ->where('is_active', true)
            ->first();

        if (!$activeAY) {
            return redirect()->back()->with('error', 'Gagal Sinkronisasi: Belum ada Tahun Ajaran yang aktif.');
        }

        // Cari semua semester untuk tahun ajaran aktif tersebut
        $semesters = \Illuminate\Support\Facades\DB::table(\App\Entities\DatabaseEntity::TBL_SEMESTERS)
            ->where('academic_year_id', $activeAY->id)
            ->get();

        if ($semesters->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal Sinkronisasi: Belum ada data Semester untuk Tahun Ajaran yang aktif.');
        }

        $totalGenerated = 0;
        foreach ($semesters as $semester) {
            $result = $this->billUseCase->autoGenerateBillsForSemester($semester->id);
            if ($result['status']) {
                $totalGenerated += $result['count'];
            }
        }

        return redirect()->back()->with('success', 'Sinkronisasi berhasil! ' . $totalGenerated . ' tagihan baru telah ditambahkan.');
    }
}
