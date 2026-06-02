<?php

namespace App\Imports;

use App\Entities\DatabaseEntity;
use App\UseCases\StudentUseCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToCollection, WithHeadingRow
{
    protected $studentUseCase;
    private $importErrors = [];

    public function __construct(StudentUseCase $studentUseCase)
    {
        $this->studentUseCase = $studentUseCase;
    }

    private $classroomCache = [];

    public function collection(Collection $rows)
    {
        $rowNumber = 1; // Baris 1 adalah header
        $importedStudentIds = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $rowNumber++;

                // Cek apakah baris kosong (abaikan jika NISN dan Nama kosong)
                if (empty($row['nisn']) && empty($row['nama_lengkap'])) {
                    continue;
                }

                // Validasi kolom wajib
                if (empty($row['nisn'])) {
                    $this->addError($rowNumber, "NISN tidak boleh kosong");
                    continue;
                }
                if (empty($row['nama_lengkap'])) {
                    $this->addError($rowNumber, "Nama Lengkap tidak boleh kosong");
                    continue;
                }

                // Cari classroom_id berdasarkan KELAS dan JURUSAN
                $classGrade = $row['kelas'] ?? null;
                $majorName  = $row['jurusan'] ?? null;
                $classroomId = $this->findClassroomId($classGrade, $majorName);

                if (!$classroomId) {
                    $msg = "Kelas '" . ($classGrade ?? '-') . "' dengan Jurusan '" . ($majorName ?? '-') . "' tidak ditemukan di master data.";
                    $this->addError($rowNumber, $msg);
                    continue;
                }

                $data = [
                    'id_number'          => !empty($row['nik_16_digit']) ? (string) $row['nik_16_digit'] : null,
                    'family_card_number' => (string) ($row['nomor_kk_16_digit'] ?? ''),
                    'nisn'               => (string) $row['nisn'],
                    'name'               => $row['nama_lengkap'],
                    'classroom_id'       => $classroomId,
                ];

                $result = $this->studentUseCase->store($data, false);

                if (!$result['status']) {
                    $friendlyMessage = $this->translateErrorMessage($result['message'] ?? 'Gagal disimpan');
                    $this->addError($rowNumber, $friendlyMessage . " (NISN: {$data['nisn']})");
                } else {
                    $importedStudentIds[] = $result['student_id'];
                }
            }

            // Jalankan sinkronisasi tagihan secara massal untuk semua siswa yang diimport
            if (!empty($importedStudentIds) && empty($this->importErrors)) {
                $activeAY = DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)->where('is_active', true)->first();
                if ($activeAY) {
                    $billUseCase = app(\App\UseCases\BillUseCase::class);
                    $syncResult = $billUseCase->syncBillsForStudents($importedStudentIds, $activeAY->id);
                    if (!$syncResult['status']) {
                        $this->addError("Sistem", "Gagal sinkronisasi tagihan siswa: " . ($syncResult['message'] ?? 'Unknown error'));
                    }
                }
            }

            // Evaluasi akhir transaksi: Jika ada error sedikitpun, batalkan semua insert!
            if (!empty($this->importErrors)) {
                DB::rollBack();
            } else {
                DB::commit();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError("Sistem", "Terjadi kesalahan fatal: " . $e->getMessage());
        }
    }

    private function addError($row, $message)
    {
        $this->importErrors[] = "Baris {$row}: {$message}";
    }

    public function getErrors()
    {
        return $this->importErrors;
    }

    private function translateErrorMessage($message)
    {
        if (str_contains($message, 'Duplicate entry')) {
            if (str_contains($message, 'nisn')) return "NISN sudah terdaftar di sistem";
            if (str_contains($message, 'id_number')) return "NIK sudah terdaftar di sistem";
            return "Data ini sudah ada di sistem (Duplikat)";
        }

        if (str_contains($message, 'Integrity constraint violation')) {
            return "Ada data yang tidak lengkap atau tidak sesuai aturan sistem";
        }

        return $message; // Jika tidak ada pola yang cocok, tampilkan aslinya
    }

    private function findClassroomId($grade, $majorName): ?int
    {
        if (!$grade || !$majorName) return null;

        $key = "{$grade}-{$majorName}";
        if (array_key_exists($key, $this->classroomCache)) {
            return $this->classroomCache[$key];
        }

        $id = DB::table(DatabaseEntity::TBL_CLASSROOMS . ' as c')
            ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->where('c.grade_level', $grade)
            ->where('m.name', $majorName)
            ->value('c.id');

        $this->classroomCache[$key] = $id;

        return $id;
    }
}
