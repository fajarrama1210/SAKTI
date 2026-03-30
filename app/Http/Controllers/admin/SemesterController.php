<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SemesterStoreRequest;
use App\UseCases\SemesterUseCase;
use App\UseCases\AcademicYearUseCase;
use App\Entities\ResponseEntity;

class SemesterController extends Controller
{
    protected $semesterUseCase;
    protected $academicYearUseCase;

    public function __construct(SemesterUseCase $semesterUseCase, AcademicYearUseCase $academicYearUseCase)
    {
        $this->semesterUseCase = $semesterUseCase;
        $this->academicYearUseCase = $academicYearUseCase;
    }

    public function index()
    {
        $semesters = $this->semesterUseCase->getPaginated();
        return view('_admin.semester.list', compact('semesters'));
    }

    public function create()
    {
        $academicYears = $this->academicYearUseCase->getAll();
        return view('_admin.semester.add', compact('academicYears'));
    }

    public function store(SemesterStoreRequest $request)
    {
        $result = $this->semesterUseCase->store($request->validated());
        if (!$result['status']) {
            $msg = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->withInput()->with('error', $msg);
        }

        $successMsg = ResponseEntity::MSG_SUCCESS_CREATE;
        if (!empty($result['bill_message'])) {
            $successMsg .= ' ' . $result['bill_message'];
        }

        return redirect()->route('admin.semesters.index')->with('success', $successMsg);
    }

    public function edit($id)
    {
        $semester = $this->semesterUseCase->getById($id);
        if (!$semester) abort(404);
        $academicYears = $this->academicYearUseCase->getAll();
        return view('_admin.semester.edit', compact('semester', 'academicYears'));
    }

    public function update(SemesterStoreRequest $request, $id)
    {
        $result = $this->semesterUseCase->update($id, $request->validated());
        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }
        return redirect()->route('admin.semesters.index')->with('success', ResponseEntity::MSG_SUCCESS_UPDATE);
    }

    public function destroy($id)
    {
        $result = $this->semesterUseCase->delete($id);
        if (!$result['status']) {
            $message = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->route('admin.semesters.index')->with('error', $message);
        }
        return redirect()->route('admin.semesters.index')->with('success', ResponseEntity::MSG_SUCCESS_DELETE);
    }

    /**
     * API: Ambil daftar semester berdasarkan tahun ajaran (untuk dropdown dinamis)
     */
    public function getByAcademicYear($academicYearId)
    {
        $semesters = $this->semesterUseCase->getByAcademicYear($academicYearId);
        return response()->json($semesters);
    }
}
