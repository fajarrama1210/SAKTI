<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Exports\StudentTemplateExport;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Entities\ResponseEntity;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\UseCases\ClassroomUseCase;
use App\UseCases\StudentUseCase;

class StudentController extends Controller
{
    protected $studentUseCase;
    protected $classroomUseCase;

    public function __construct(StudentUseCase $studentUseCase, ClassroomUseCase $classroomUseCase)
    {
        $this->studentUseCase = $studentUseCase;
        $this->classroomUseCase = $classroomUseCase;
    }

    public function index()
    {
        $students = $this->studentUseCase->getPaginated();
        return view('_admin.student.list', compact('students'));
    }

    public function create()
    {
        $classrooms = $this->classroomUseCase->getAll();
        return view('_admin.student.add', compact('classrooms'));
    }

    public function store(StudentStoreRequest $request)
    {
        // Validasi lolos, sekarang kirim ke UseCase
        $result = $this->studentUseCase->store($request->validated());

        if (!$result['status']) {
            // TAMPILKAN PESAN ERROR ASLI DARI DATABASE
            $errorMessage = $result['message'] ?? 'Terjadi kesalahan sistem saat menyimpan ke database.';

            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }

        return redirect()->route('admin.students.index')->with('success', 'Data siswa dan Akun User berhasil dibuat!');
    }
    public function edit($id)
    {
        $student = $this->studentUseCase->getById($id);
        if (!$student) abort(404);

        $classrooms = $this->classroomUseCase->getAll();
        return view('_admin.student.update', compact('student', 'classrooms'));
    }

    public function update(StudentUpdateRequest $request, $id)
    {
        $result = $this->studentUseCase->update($id, $request->validated());

        if (!$result['status']) {
            $message = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->with('error', $message);
        }

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $result = $this->studentUseCase->delete($id);

        if (!$result['status']) {
            $message = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->with('error', $message);
        }

        return redirect()->route('admin.students.index')->with('success', 'Siswa dan akun loginnya berhasil dihapus!');
    }

    public function resetPassword($id)
    {
        $result = $this->studentUseCase->resetPassword($id);

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Password siswa berhasil direset ke NISN: ' . $result['nisn']);
    }

    public function downloadTemplate()
    {
        return Excel::download(new StudentTemplateExport, 'Template_Import_Siswa.xlsx');
    }

    public function import()
    {
        return view('_admin.student.import');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file_excel.required' => 'File Excel wajib diupload!',
            'file_excel.mimes' => 'Format file harus .xlsx atau .xls'
        ]);

        try {
            $import = new StudentImport($this->studentUseCase);
            Excel::import($import, $request->file('file_excel'));

            $errors = $import->getErrors();

            if (!empty($errors)) {
                return redirect()->back()
                    ->with('error', 'Beberapa data gagal diimport. Silakan cek detail di bawah.')
                    ->with('import_errors', $errors);
            }

            return redirect()->route('admin.students.index')->with('success', 'Semua data siswa dari Excel berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Import: Terjadi kesalahan fatal. Detail: ' . $e->getMessage());
        }
    }
}
