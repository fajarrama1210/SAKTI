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
        $semesters = \Illuminate\Support\Facades\DB::table('semesters as s')
            ->join('academic_years as ay', 's.academic_year_id', '=', 'ay.id')
            ->select('s.id', 's.name as semester_name', 'ay.name as academic_year_name')
            ->orderBy('ay.name', 'desc')
            ->orderBy('s.name', 'asc')
            ->get();

        return view('_admin.payment_type.add', compact('semesters'));
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

        $semesters = \Illuminate\Support\Facades\DB::table('semesters as s')
            ->join('academic_years as ay', 's.academic_year_id', '=', 'ay.id')
            ->select('s.id', 's.name as semester_name', 'ay.name as academic_year_name')
            ->orderBy('ay.name', 'desc')
            ->orderBy('s.name', 'asc')
            ->get();

        return view('_admin.payment_type.edit', compact('paymentType', 'semesters'));
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
