<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRateStoreRequest;
use App\UseCases\PaymentRateUseCase;
use App\UseCases\AcademicYearUseCase;
use App\UseCases\PaymentTypeUseCase;
use App\UseCases\MajorUseCase;
use App\Entities\ResponseEntity;

class PaymentRateController extends Controller
{
    protected $paymentRateUseCase;
    protected $academicYearUseCase;
    protected $paymentTypeUseCase;
    protected $majorUseCase;

    public function __construct(
        PaymentRateUseCase $paymentRateUseCase,
        AcademicYearUseCase $academicYearUseCase,
        PaymentTypeUseCase $paymentTypeUseCase,
        MajorUseCase $majorUseCase
    ) {
        $this->paymentRateUseCase = $paymentRateUseCase;
        $this->academicYearUseCase = $academicYearUseCase;
        $this->paymentTypeUseCase = $paymentTypeUseCase;
        $this->majorUseCase = $majorUseCase;
    }

    public function index()
    {
        $paymentRates = $this->paymentRateUseCase->getPaginated();
        return view('_admin.payment_rate.list', compact('paymentRates'));
    }

    public function create()
    {
        $academicYears = $this->academicYearUseCase->getAll();
        $paymentTypes = $this->paymentTypeUseCase->getAll();
        $majors = $this->majorUseCase->getAll();
        return view('_admin.payment_rate.add', compact('academicYears', 'paymentTypes', 'majors'));
    }

    public function store(PaymentRateStoreRequest $request)
    {
        $result = $this->paymentRateUseCase->store($request->validated());

        if (!$result['status']) {
            $errorMessage = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        return redirect()->route('admin.payment-rates.index')->with('success', ResponseEntity::MSG_SUCCESS_CREATE);
    }

    public function edit($id)
    {
        $paymentRate = $this->paymentRateUseCase->getById($id);
        if (!$paymentRate) abort(404);

        $academicYears = $this->academicYearUseCase->getAll();
        $paymentTypes = $this->paymentTypeUseCase->getAll();
        $majors = $this->majorUseCase->getAll();
        return view('_admin.payment_rate.edit', compact('paymentRate', 'academicYears', 'paymentTypes', 'majors'));
    }

    public function update(PaymentRateStoreRequest $request, $id)
    {
        $result = $this->paymentRateUseCase->update($id, $request->validated());

        if (!$result['status']) {
            $errorMessage = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        return redirect()->route('admin.payment-rates.index')->with('success', ResponseEntity::MSG_SUCCESS_UPDATE);
    }

    public function destroy($id)
    {
        $result = $this->paymentRateUseCase->delete($id);

        if (!$result['status']) {
            return redirect()->back()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.payment-rates.index')->with('success', ResponseEntity::MSG_SUCCESS_DELETE);
    }
}
