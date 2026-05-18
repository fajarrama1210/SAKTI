<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionStoreRequest;
use App\UseCases\TransactionUseCase;
use App\Entities\ResponseEntity;

class TransactionController extends Controller
{
    protected $transactionUseCase;

    public function __construct(TransactionUseCase $transactionUseCase)
    {
        $this->transactionUseCase = $transactionUseCase;
    }

    public function index()
    {
        $transactions = $this->transactionUseCase->getPaginated();
        return view('_admin.transaction.list', compact('transactions'));
    }

    public function create()
    {
        return view('_admin.transaction.add');
    }

    public function store(TransactionStoreRequest $request)
    {
        $data = $request->validated();
        $data['recorded_by'] = auth()->id();

        $result = $this->transactionUseCase->store($data);
        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }
        return redirect()->route('admin.transactions.index')->with('success', ResponseEntity::MSG_SUCCESS_CREATE);
    }

    public function show($id)
    {
        $transaction = $this->transactionUseCase->getById($id);
        if (!$transaction) abort(404);

        return view('_admin.transaction.show', compact('transaction'));
    }

    public function edit($id)
    {
        $transaction = $this->transactionUseCase->getById($id);
        if (!$transaction) abort(404);
        if ($transaction->payment_id) abort(403, 'Transaksi otomatis tidak dapat diedit manual.');

        return view('_admin.transaction.edit', compact('transaction'));
    }

    public function update(TransactionStoreRequest $request, $id)
    {
        $result = $this->transactionUseCase->update($id, $request->validated());

        if (!$result['status']) {
            $msg = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->withInput()->with('error', $msg);
        }

        return redirect()->route('admin.transactions.index')->with('success', ResponseEntity::MSG_SUCCESS_UPDATE);
    }

    public function destroy($id)
    {
        $result = $this->transactionUseCase->delete($id);
        if (!$result['status']) {
            $msg = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->with('error', $msg);
        }
        return redirect()->route('admin.transactions.index')->with('success', ResponseEntity::MSG_SUCCESS_DELETE);
    }
}
