<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UseCases\BillUseCase;
use App\UseCases\StudentUseCase;
use App\Entities\ResponseEntity;
use Illuminate\Http\Request;

class BillController extends Controller
{
    protected $billUseCase;
    protected $studentUseCase;

    public function __construct(BillUseCase $billUseCase, StudentUseCase $studentUseCase)
    {
        $this->billUseCase    = $billUseCase;
        $this->studentUseCase = $studentUseCase;
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
            'payment_method' => 'required|in:cash,transfer,other',
        ]);

        $data = [
            'payment_method'   => $request->payment_method,
            'payment_date'     => now()->toDateString(),
            'reference_number' => $request->reference_number,
            'notes'            => $request->notes,
            'verified_by'      => auth()->id(),
        ];

        $result = $this->billUseCase->recordPayment($billId, $data);

        if (!$result['status']) {
            $msg = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->with('error', $msg);
        }

        // Kembali ke halaman detail siswa
        $bill = $this->billUseCase->getById($billId);

        return redirect()->route('admin.spp.student', $bill->student_id)
            ->with('success', 'Pembayaran berhasil dicatat! Saudara dengan KK yang sama juga otomatis lunas.');
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
}
