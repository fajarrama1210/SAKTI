<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UseCases\EnrollmentUseCase;
use App\UseCases\StudentUseCase;
use App\UseCases\ClassroomUseCase;
use App\UseCases\AcademicYearUseCase;
use App\Entities\ResponseEntity;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    protected $enrollmentUseCase;
    protected $studentUseCase;
    protected $classroomUseCase;
    protected $academicYearUseCase;

    public function __construct(
        EnrollmentUseCase $enrollmentUseCase,
        StudentUseCase $studentUseCase,
        ClassroomUseCase $classroomUseCase,
        AcademicYearUseCase $academicYearUseCase
    ) {
        $this->enrollmentUseCase = $enrollmentUseCase;
        $this->studentUseCase = $studentUseCase;
        $this->classroomUseCase = $classroomUseCase;
        $this->academicYearUseCase = $academicYearUseCase;
    }

    /**
     * Halaman utama: daftar penempatan siswa per tahun ajaran
     */
    public function index(Request $request)
    {
        $academicYears = $this->academicYearUseCase->getAll();
        $activeAY = $this->academicYearUseCase->getActive();

        // Fallback: Jika tidak ada yang aktif, ambil yang paling baru (pertama dari list karena order by start_date desc)
        $fallbackAYId = $activeAY->id ?? ($academicYears->first()->id ?? null);
        $selectedAY = $request->get('academic_year_id', $fallbackAYId);

        $filters = $request->only(['status', 'classroom_id', 'search']);
        $classrooms = $this->classroomUseCase->getAll();

        if ($selectedAY) {
            $enrollments = $this->enrollmentUseCase->getByAcademicYear($selectedAY, 20, $filters);
        } else {
            // Pastikan $enrollments tetap berupa objek paginasi meskipun kosong untuk menghindari error di view
            $enrollments = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return view('_admin.enrollment.index', compact(
            'academicYears', 'selectedAY', 'enrollments', 'classrooms', 'filters'
        ));
    }

    /**
     * Form daftarkan siswa ke kelas + tahun ajaran
     */
    public function create(Request $request)
    {
        $academicYears = $this->academicYearUseCase->getAll();
        $classrooms = $this->classroomUseCase->getAll();

        // Ambil siswa yang belum punya enrollment di TA terpilih
        $selectedAY = $request->get('academic_year_id');
        $students = $this->studentUseCase->getPaginated(999); // semua siswa untuk dropdown

        return view('_admin.enrollment.add', compact('academicYears', 'classrooms', 'students', 'selectedAY'));
    }

    /**
     * Simpan enrollment baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrolled_at' => 'nullable|date',
        ]);

        $result = $this->enrollmentUseCase->enroll($request->all());

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.enrollments.index', ['academic_year_id' => $request->academic_year_id])
            ->with('success', 'Siswa berhasil didaftarkan ke kelas!');
    }

    /**
     * Proses DO (Drop Out) siswa
     */
    public function dropout(Request $request, $enrollmentId)
    {
        $request->validate([
            'exit_date' => 'required|date',
            'exit_reason' => 'required|string|max:255',
        ]);

        $result = $this->enrollmentUseCase->processDropout($enrollmentId, [
            'exit_date' => $request->exit_date,
            'exit_reason' => $request->exit_reason,
        ]);

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER);
        }

        $cancelledMsg = ($result['cancelled_bills'] ?? 0) > 0
            ? " {$result['cancelled_bills']} tagihan setelah tanggal DO dibatalkan."
            : "";

        return redirect()->back()->with('success', "Siswa berhasil di-DO.{$cancelledMsg}");
    }

    /**
     * Pindah kelas siswa (dalam tahun ajaran yang sama)
     */
    public function changeClassroom(Request $request, $enrollmentId)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        $result = $this->enrollmentUseCase->changeClassroom($enrollmentId, $request->classroom_id);

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->back()->with('success', 'Kelas siswa berhasil diperbarui!');
    }

    /**
     * Halaman kelulusan massal
     */
    public function graduationForm(Request $request)
    {
        $academicYears = $this->academicYearUseCase->getAll();
        $activeAY = $this->academicYearUseCase->getActive();
        $selectedAY = $request->get('academic_year_id', $activeAY->id ?? null);
        $classrooms = $this->classroomUseCase->getAll();

        $students = collect();
        if ($selectedAY) {
            // Ambil siswa kelas XII (grade_level = 12) yang aktif di TA ini
            $students = $this->enrollmentUseCase->getByAcademicYear($selectedAY, 999, [
                'status' => 'aktif',
            ]);
        }

        return view('_admin.enrollment.graduation', compact(
            'academicYears', 'selectedAY', 'students', 'classrooms'
        ));
    }

    /**
     * Proses kelulusan massal
     */
    public function processGraduation(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'graduation_date' => 'nullable|date',
        ]);

        $result = $this->enrollmentUseCase->processGraduation(
            $request->academic_year_id,
            $request->student_ids,
            $request->graduation_date
        );

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER);
        }

        $msg = "{$result['graduated_count']} siswa berhasil diluluskan.";
        if (!empty($result['students_with_debt'])) {
            $debtNames = collect($result['students_with_debt'])->pluck('name')->implode(', ');
            $msg .= " Perhatian: beberapa siswa masih memiliki tunggakan ({$debtNames}).";
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Hapus enrollment
     */
    public function destroy($id)
    {
        $result = $this->enrollmentUseCase->delete($id);

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->back()->with('success', 'Penempatan berhasil dihapus.');
    }
}
