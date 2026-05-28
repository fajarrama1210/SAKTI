<?php

namespace App\UseCases;

use App\Entities\DatabaseEntity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionUseCase
{
    public function getPaginated($perPage = 15)
    {
        return DB::table(DatabaseEntity::TBL_TRANSACTIONS . ' as t')
            ->leftJoin(DatabaseEntity::TBL_USERS . ' as u', 't.recorded_by', '=', 'u.id')
            ->select('t.*', 'u.name as recorded_by_name')
            ->orderBy('t.date', 'desc')
            ->orderBy('t.id', 'desc')
            ->paginate($perPage);
    }

    public function getById($id)
    {
        return DB::table(DatabaseEntity::TBL_TRANSACTIONS . ' as t')
            ->leftJoin(DatabaseEntity::TBL_USERS . ' as u', 't.recorded_by', '=', 'u.id')
            ->select('t.*', 'u.name as recorded_by_name')
            ->where('t.id', $id)
            ->first();
    }

    /**
     * Catat transaksi manual (uang masuk / uang keluar)
     */
    public function store(array $data): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_TRANSACTIONS)->insert([
                'date' => $data['date'],
                'type' => $data['type'], // income / expense
                'category' => $data['category'] ?? null,
                'description' => $data['description'],
                'amount' => $data['amount'],
                'payment_id' => null, // manual, bukan dari SPP
                'recorded_by' => $data['recorded_by'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("TransactionStore Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($id, array $data): array
    {
        DB::beginTransaction();
        try {
            $trx = DB::table(DatabaseEntity::TBL_TRANSACTIONS)->where('id', $id)->first();
            if ($trx && $trx->payment_id) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Transaksi otomatis dari pembayaran SPP tidak bisa diedit manual.'];
            }

            DB::table(DatabaseEntity::TBL_TRANSACTIONS)->where('id', $id)->update([
                'date' => $data['date'],
                'type' => $data['type'],
                'category' => $data['category'] ?? null,
                'description' => $data['description'],
                'amount' => $data['amount'],
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("TransactionUpdate Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id): array
    {
        DB::beginTransaction();
        try {
            $trx = DB::table(DatabaseEntity::TBL_TRANSACTIONS)->where('id', $id)->first();
            if ($trx && $trx->payment_id) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Transaksi otomatis dari pembayaran SPP tidak bisa dihapus manual.'];
            }

            DB::table(DatabaseEntity::TBL_TRANSACTIONS)->where('id', $id)->delete();
            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("TransactionDelete Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    private function buildReportQuery(array $filters)
    {
        $query = DB::table(DatabaseEntity::TBL_TRANSACTIONS . ' as t')
            ->leftJoin(DatabaseEntity::TBL_USERS . ' as u', 't.recorded_by', '=', 'u.id')
            ->select('t.*', 'u.name as recorded_by_name');

        if (!empty($filters['type'])) {
            $query->where('t.type', $filters['type']);
        }

        if (!empty($filters['months']) && is_array($filters['months'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['months'] as $month) {
                    $q->orWhereMonth('t.date', $month);
                }
            });
        }

        if (!empty($filters['month'])) {
            $query->whereMonth('t.date', $filters['month']);
        }

        if (!empty($filters['year'])) {
            $query->whereYear('t.date', $filters['year']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('t.description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('t.category', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('u.name', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query;
    }

    /**
     * Ambil data transaksi untuk laporan (dengan filter)
     */
    public function getReport(array $filters)
    {
        $query = $this->buildReportQuery($filters);
        $query->orderBy('t.date', 'asc')->orderBy('t.id', 'asc');

        if (!empty($filters['is_export'])) {
            return $query->get();
        }

        $perPage = $filters['per_page'] ?? 50;
        return $query->paginate($perPage);
    }

    public function getReportTotals(array $filters)
    {
        $query = $this->buildReportQuery($filters);
        
        $income = (clone $query)->where('t.type', 'income')->sum('t.amount');
        $expense = (clone $query)->where('t.type', 'expense')->sum('t.amount');
        
        return [
            'income' => $income,
            'expense' => $expense,
        ];
    }
}
