<?php

namespace App\Services;

use App\Entities\DatabaseEntity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    /**
     * Buat transaksi QRIS langsung via Core API.
     * Tidak menggunakan Snap sehingga UI Midtrans tidak muncul.
     * QR code dikembalikan ke frontend untuk ditampilkan di modal kustom.
     *
     * @param  object $bill
     * @param  object $student
     * @return array
     */
    public function createQrisCharge(object $bill, object $student): array
    {
        try {
            if ($bill->status === 'paid') {
                return ['status' => false, 'message' => 'Tagihan ini sudah lunas.'];
            }

            $paidAmount      = DB::table(DatabaseEntity::TBL_PAYMENTS)->where('bill_id', $bill->id)->sum('amount');
            $remainingAmount = (int) ($bill->total_amount - $paidAmount);

            if ($remainingAmount <= 0) {
                return ['status' => false, 'message' => 'Tagihan sudah terlunasi.'];
            }

            // Order ID unik: SAKTI-{bill_id}-{timestamp}-{random6}
            $orderId = 'SAKTI-' . $bill->id . '-' . time() . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

            $bulan = \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F');

            // Nama item maks 50 karakter (validasi Midtrans)
            $itemName = 'SPP ' . $bulan . ' ' . $bill->year;

            // Parameter Core API QRIS
            $params = [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => $remainingAmount,
                ],
                'item_details' => [
                    [
                        'id'       => 'SPP-' . $bill->id,
                        'price'    => $remainingAmount,
                        'quantity' => 1,
                        'name'     => $itemName,
                    ],
                ],
                'customer_details' => [
                    'first_name' => $student->name,
                    'email'      => optional(DB::table('users')->where('student_id', $student->id)->first())->email ?? 'student@sakti.id',
                    'phone'      => $student->phone ?? '08000000000',
                ],
                'qris' => [
                    'acquirer' => 'gopay',
                ],
            ];

            // Simpan order sebagai pending dulu
            DB::table('midtrans_orders')->insert([
                'order_id'   => $orderId,
                'bill_id'    => $bill->id,
                'student_id' => $student->id,
                'amount'     => $remainingAmount,
                'status'     => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Panggil Core API Midtrans
            $response = CoreApi::charge($params);

            Log::info('[MidtransService] Raw QRIS Response', [
                'order_id' => $orderId,
                'response' => json_encode($response),
            ]);

            $qrString = $response->qr_string ?? null;
            $transId  = $response->transaction_id ?? null;

            // ✅ SOLUSI: Ambil URL QR image langsung dari Midtrans (bukan generate ulang)
            // Response Midtrans menyediakan actions[0]->url = URL gambar QR asli Midtrans.
            // Menggunakan URL ini memastikan simulator sandbox dapat membaca QR dengan benar.
            $qrImageUrl = null;
            $actions    = $response->actions ?? [];
            foreach ($actions as $action) {
                if (($action->name ?? '') === 'generate-qr-code') {
                    $qrImageUrl = $action->url ?? null;
                    break;
                }
            }

            // Fallback: jika actions kosong tapi qr_string tersedia, gunakan qrserver.com
            // (hanya untuk keperluan display, simulator tetap harus menggunakan URL Midtrans)
            if (!$qrImageUrl && $qrString) {
                Log::warning('[MidtransService] actions.generate-qr-code tidak ditemukan, fallback ke qrserver.com', [
                    'order_id' => $orderId,
                ]);
                $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&ecc=M&data=' . urlencode($qrString);
            }

            // Update order dengan transaction_id dan qr_image_url
            DB::table('midtrans_orders')->where('order_id', $orderId)->update([
                'transaction_id' => $transId,
                'updated_at'     => now(),
            ]);

            if (!$qrString && !$qrImageUrl) {
                return ['status' => false, 'message' => 'Gagal mendapatkan kode QRIS dari Midtrans. Coba lagi.'];
            }

            return [
                'status'      => true,
                'order_id'    => $orderId,
                'qr_string'   => $qrString,
                'qr_image_url'=> $qrImageUrl,
                'amount'      => $remainingAmount,
                'expires_in'  => 900, // 15 menit dalam detik
            ];
        } catch (\Throwable $e) {
            Log::error('[MidtransService] createQrisCharge Error', [
                'bill_id' => $bill->id ?? null,
                'message' => $e->getMessage(),
            ]);
            return ['status' => false, 'message' => 'Gagal membuat pembayaran QRIS: ' . $e->getMessage()];
        }
    }

    /**
     * Cek status transaksi langsung ke Midtrans API (fallback untuk dev lokal tanpa webhook).
     * Jika sudah settlement/capture, langsung record pembayaran ke DB.
     *
     * @param  string $orderId
     * @return array
     */
    public function checkTransactionStatus(string $orderId): array
    {
        try {
            $status = Transaction::status($orderId);

            Log::info('[MidtransService] checkTransactionStatus', [
                'order_id'           => $orderId,
                'transaction_status' => $status->transaction_status ?? null,
            ]);

            $transactionStatus = $status->transaction_status ?? '';
            $fraudStatus       = $status->fraud_status ?? 'accept';
            $transactionId     = $status->transaction_id ?? '';
            $paymentType       = $status->payment_type ?? 'qris';

            $order = DB::table('midtrans_orders')->where('order_id', $orderId)->first();
            if (!$order) {
                return ['settled' => false, 'status' => $transactionStatus];
            }

            // Jika sudah settlement tapi di DB masih pending — proses sekarang
            $isSettled = $transactionStatus === 'settlement'
                || ($transactionStatus === 'capture' && $fraudStatus === 'accept');

            if ($isSettled && $order->status !== 'settlement') {
                $this->recordMidtransPayment($order, $transactionId, $paymentType);
                DB::table('midtrans_orders')->where('order_id', $orderId)->update([
                    'status'         => 'settlement',
                    'transaction_id' => $transactionId,
                    'updated_at'     => now(),
                ]);
            }

            return [
                'settled' => $isSettled || $order->status === 'settlement',
                'status'  => $transactionStatus,
            ];
        } catch (\Throwable $e) {
            Log::warning('[MidtransService] checkTransactionStatus Error', [
                'order_id' => $orderId,
                'message'  => $e->getMessage(),
            ]);
            return ['settled' => false, 'status' => 'unknown', 'error' => $e->getMessage()];
        }
    }

    /**
     * Handle Webhook Notification dari Midtrans.
     * Verifikasi signature key SHA512 sebelum memproses.
     */
    public function handleWebhook(): array
    {
        try {
            $rawBody = file_get_contents('php://input');
            $notif   = json_decode($rawBody, true);

            if (empty($notif)) {
                return ['status' => false, 'message' => 'Payload kosong.'];
            }

            $orderId           = $notif['order_id'] ?? '';
            $statusCode        = $notif['status_code'] ?? '';
            $grossAmount       = $notif['gross_amount'] ?? '0';
            $transactionStatus = $notif['transaction_status'] ?? '';
            $fraudStatus       = $notif['fraud_status'] ?? '';
            $paymentType       = $notif['payment_type'] ?? '';
            $transactionId     = $notif['transaction_id'] ?? '';

            // Verifikasi signature key (SHA512) — mencegah request palsu
            $serverKey         = config('midtrans.server_key');
            $signatureKey      = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            $receivedSignature = $notif['signature_key'] ?? '';

            if (!hash_equals($signatureKey, $receivedSignature)) {
                Log::warning('[MidtransService] Invalid signature', ['order_id' => $orderId]);
                return ['status' => false, 'message' => 'Signature tidak valid.'];
            }

            $order = DB::table('midtrans_orders')->where('order_id', $orderId)->lockForUpdate()->first();
            if (!$order) {
                return ['status' => false, 'message' => 'Order tidak ditemukan.'];
            }

            // Idempotency — cegah proses ganda
            if ($order->status === 'settlement') {
                return ['status' => true, 'message' => 'Sudah diproses.'];
            }

            $newOrderStatus = $transactionStatus;

            if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
                $newOrderStatus = 'settlement';
                $this->recordMidtransPayment($order, $transactionId, $paymentType);
            }

            DB::table('midtrans_orders')->where('order_id', $orderId)->update([
                'status'         => $newOrderStatus,
                'transaction_id' => $transactionId,
                'raw_response'   => json_encode($notif),
                'updated_at'     => now(),
            ]);

            Log::info('[MidtransService] Webhook OK', ['order_id' => $orderId, 'status' => $newOrderStatus]);
            return ['status' => true, 'message' => 'OK'];
        } catch (\Throwable $e) {
            Log::error('[MidtransService] Webhook Error', ['message' => $e->getMessage()]);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Rekam pembayaran sukses dari Midtrans ke tabel payments SAKTI.
     */
    private function recordMidtransPayment(object $order, string $transactionId, string $paymentType): void
    {
        DB::beginTransaction();
        try {
            $bill = DB::table(DatabaseEntity::TBL_BILLS . ' as b')
                ->join(DatabaseEntity::TBL_STUDENTS . ' as s', 'b.student_id', '=', 's.id')
                ->select('b.*', 's.name as student_name', 's.family_card_number')
                ->where('b.id', $order->bill_id)
                ->lockForUpdate()
                ->first();

            if (!$bill) { DB::rollBack(); return; }

            $alreadyPaid = DB::table(DatabaseEntity::TBL_PAYMENTS)->where('reference_number', $transactionId)->exists();
            if ($alreadyPaid) { DB::rollBack(); return; }

            $paidSoFar = DB::table(DatabaseEntity::TBL_PAYMENTS)->where('bill_id', $bill->id)->sum('amount');
            $remaining = $bill->total_amount - $paidSoFar;
            $payAmount = min((int) $order->amount, (int) $remaining);

            if ($payAmount <= 0) { DB::rollBack(); return; }

            $paymentId = DB::table(DatabaseEntity::TBL_PAYMENTS)->insertGetId([
                'bill_id'          => $bill->id,
                'amount'           => $payAmount,
                'payment_method'   => 'qris',
                'payment_date'     => now()->toDateString(),
                'reference_number' => $transactionId,
                'verified_by'      => null,
                'notes'            => 'Pembayaran QRIS via Midtrans. Order: ' . $order->order_id,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // Alokasi ke bill_items
            $billItems        = DB::table(DatabaseEntity::TBL_BILL_ITEMS)->where('bill_id', $bill->id)->get();
            $amountToAllocate = $payAmount;

            foreach ($billItems as $item) {
                if ($amountToAllocate <= 0) break;
                $itemPaid      = DB::table('payment_allocations')->where('bill_item_id', $item->id)->sum('amount');
                $itemRemaining = $item->amount - $itemPaid;
                if ($itemRemaining > 0) {
                    $allocate = min($amountToAllocate, $itemRemaining);
                    DB::table('payment_allocations')->insert([
                        'payment_id'   => $paymentId,
                        'bill_item_id' => $item->id,
                        'amount'       => $allocate,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                    $amountToAllocate -= $allocate;
                }
            }

            $newTotalPaid = $paidSoFar + $payAmount;
            $newStatus    = ($newTotalPaid >= $bill->total_amount) ? 'paid' : 'partial';

            DB::table(DatabaseEntity::TBL_BILLS)->where('id', $bill->id)->update([
                'status'     => $newStatus,
                'updated_at' => now(),
            ]);

            $bulan = \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F');
            DB::table(DatabaseEntity::TBL_TRANSACTIONS)->insert([
                'date'        => now()->toDateString(),
                'type'        => 'income',
                'category'    => 'SPP',
                'description' => 'QRIS: SPP ' . $bulan . ' ' . $bill->year . ' - ' . $bill->student_name,
                'amount'      => $payAmount,
                'payment_id'  => $paymentId,
                'recorded_by' => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            DB::commit();
            Log::info('[MidtransService] Payment recorded', ['bill_id' => $bill->id, 'amount' => $payAmount]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[MidtransService] recordPayment Error', ['message' => $e->getMessage()]);
        }
    }
}
