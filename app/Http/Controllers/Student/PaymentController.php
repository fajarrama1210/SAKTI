<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Endpoint: POST /student/bills/{billId}/pay-qris
     * Membuat Snap Token Midtrans untuk siswa membayar tagihan via QRIS.
     * Mengembalikan JSON (dipanggil via AJAX dari frontend).
     */
    public function createQrisToken(Request $request, int $billId)
    {
        try {
            $studentId = Auth::user()->student_id;
            if (!$studentId) {
                return response()->json(['success' => false, 'message' => 'Akun tidak terhubung ke data siswa.'], 403);
            }

            // Ambil data siswa yang login
            $student = DB::table('students')->where('id', $studentId)->first();
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
            }

            // Ambil tagihan dan PASTIKAN milik siswa yang login (Authorization check)
            $bill = DB::table('bills as b')
                ->join('academic_years as ay', 'b.academic_year_id', '=', 'ay.id')
                ->select('b.*', 'ay.name as academic_year_name')
                ->where('b.id', $billId)
                ->where('b.student_id', $studentId) // <-- Kunci otorisasi
                ->first();

            if (!$bill) {
                return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan atau bukan milik Anda.'], 403);
            }

            if ($bill->status === 'paid') {
                return response()->json(['success' => false, 'message' => 'Tagihan ini sudah lunas.'], 400);
            }

            // Buat transaksi QRIS via Core API
            $result = $this->midtransService->createQrisCharge($bill, $student);

            if (!$result['status']) {
                return response()->json(['success' => false, 'message' => $result['message']], 500);
            }

            return response()->json([
                'success'      => true,
                'order_id'     => $result['order_id'],
                'qr_string'    => $result['qr_string'],
                'qr_image_url' => $result['qr_image_url'],
                'amount'       => $result['amount'],
                'expires_in'   => $result['expires_in'],
            ]);
        } catch (\Throwable $e) {
            Log::error('[PaymentController] createQrisToken Error', [
                'user_id' => Auth::id(),
                'bill_id' => $billId,
                'message' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server.'], 500);
        }
    }

    /**
     * Endpoint: GET /student/bills/{billId}/payment-status
     * Polling: Cek apakah pembayaran untuk tagihan sudah settlement.
     * Menggunakan direct API check ke Midtrans sebagai fallback jika webhook tidak sampai.
     */
    public function checkPaymentStatus(Request $request, int $billId)
    {
        try {
            $studentId = Auth::user()->student_id;

            // Pastikan tagihan milik siswa yang login
            $bill = DB::table('bills')
                ->where('id', $billId)
                ->where('student_id', $studentId)
                ->first();

            if (!$bill) {
                return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan.'], 403);
            }

            // Jika sudah paid dari DB, langsung return (webhook sudah jalan)
            if ($bill->status === 'paid') {
                return response()->json([
                    'success'      => true,
                    'bill_status'  => 'paid',
                    'order_status' => 'settlement',
                    'is_paid'      => true,
                ]);
            }

            // Ambil order_id terbaru untuk tagihan ini
            $order = DB::table('midtrans_orders')
                ->where('bill_id', $billId)
                ->orderByDesc('created_at')
                ->first();

            if ($order && $order->status !== 'settlement') {
                // Cek langsung ke Midtrans API (fallback webhook)
                $this->midtransService->checkTransactionStatus($order->order_id);
            }

            // Re-query bill setelah cek Midtrans (status mungkin sudah berubah)
            $bill = DB::table('bills')->where('id', $billId)->first();
            $order = DB::table('midtrans_orders')
                ->where('bill_id', $billId)
                ->orderByDesc('created_at')
                ->first();

            return response()->json([
                'success'      => true,
                'bill_status'  => $bill->status,
                'order_status' => $order->status ?? null,
                'is_paid'      => $bill->status === 'paid',
            ]);
        } catch (\Throwable $e) {
            Log::error('[PaymentController] checkPaymentStatus Error', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Server error.'], 500);
        }
    }
}
