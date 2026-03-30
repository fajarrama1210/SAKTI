<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentTypeStoreRequest;
use App\UseCases\PaymentTypeUseCase;
use App\Entities\ResponseEntity;

class PaymentTypeController extends Controller
{
    protected $paymentTypeUseCase;

    public function __construct(PaymentTypeUseCase $paymentTypeUseCase)
    {
        $this->paymentTypeUseCase = $paymentTypeUseCase;
    }

    public function index()
    {
        $paymentTypes = $this->paymentTypeUseCase->getPaginated();
        return view('_admin.payment_type.list', compact('paymentTypes'));
    }

    public function create()
    {
        return view('_admin.payment_type.add');
    }

    public function store(PaymentTypeStoreRequest $request)
    {
        $result = $this->paymentTypeUseCase->store($request->validated());

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.payment-types.index')->with('success', ResponseEntity::MSG_SUCCESS_CREATE);
    }

    public function edit($id)
    {
        $paymentType = $this->paymentTypeUseCase->getById($id);
        if (!$paymentType) abort(404);

        return view('_admin.payment_type.edit', compact('paymentType'));
    }

    public function update(PaymentTypeStoreRequest $request, $id)
    {
        $result = $this->paymentTypeUseCase->update($id, $request->validated());

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.payment-types.index')->with('success', ResponseEntity::MSG_SUCCESS_UPDATE);
    }

    public function destroy($id)
    {
        $result = $this->paymentTypeUseCase->delete($id);

        if (!$result['status']) {
            return redirect()->back()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.payment-types.index')->with('success', ResponseEntity::MSG_SUCCESS_DELETE);
    }
}
