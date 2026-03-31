<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicYearStoreRequest;
use App\UseCases\AcademicYearUseCase;
use App\Entities\ResponseEntity;

class AcademicYearController extends Controller
{
    protected $academicYearUseCase;

    public function __construct(AcademicYearUseCase $academicYearUseCase)
    {
        $this->academicYearUseCase = $academicYearUseCase;
    }

    public function index()
    {
        $academicYears = $this->academicYearUseCase->getPaginated();
        return view('_admin.academic_year.list', compact('academicYears'));
    }

    public function create()
    {
        return view('_admin.academic_year.add');
    }

    public function store(AcademicYearStoreRequest $request)
    {
        $result = $this->academicYearUseCase->store($request->validated());

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.academic-years.index')->with('success', ResponseEntity::MSG_SUCCESS_CREATE);
    }

    public function edit($id)
    {
        $academicYear = $this->academicYearUseCase->getById($id);
        if (!$academicYear) abort(404);

        return view('_admin.academic_year.edit', compact('academicYear'));
    }

    public function update(AcademicYearStoreRequest $request, $id)
    {
        $result = $this->academicYearUseCase->update($id, $request->validated());

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.academic-years.index')->with('success', ResponseEntity::MSG_SUCCESS_UPDATE);
    }

    public function destroy($id)
    {
        $result = $this->academicYearUseCase->delete($id);

        if (!$result['status']) {
            $message = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->route('admin.academic-years.index')->with('error', $message);
        }

        return redirect()->route('admin.academic-years.index')->with('success', ResponseEntity::MSG_SUCCESS_DELETE);
    }
}
