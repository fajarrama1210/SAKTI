# Walkthrough: Stress Test Sistem SAKTI

## Ringkasan

Membuat suite stress test komprehensif untuk sistem SAKTI yang menguji performa, keandalan, dan konsistensi data di bawah beban besar. **17 test case**, semua **PASS** dalam **4.19 detik**.

## File yang Dibuat

| File | Fungsi |
|------|--------|
| [StressTestSeeder.php](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/database/seeders/StressTestSeeder.php) | Seeder data massal: 1000 siswa, 30 kelas, 5000+ tagihan |
| [StressTest.php](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/tests/Feature/StressTest.php) | 17 PHPUnit feature test (SQLite in-memory) |
| [StressTestCommand.php](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/Console/Commands/StressTestCommand.php) | Artisan `stress:test` untuk benchmark endpoint real-time |

## Hasil Test (17/17 PASS ✅)

```
✓ it can create hundreds of students quickly                     0.38s
✓ it creates enrollment for all students                         0.03s
✓ it can auto generate bills for hundreds of students            0.33s
✓ it detects sibling kk constraint bug                           0.03s
✓ it handles rapid sequential payments                           0.34s
✓ it handles mass graduation with debt checking                  0.36s
✓ it detects cancelled status missing from bills enum            0.36s
✓ dashboard loads under heavy data                               0.39s
✓ spp search performs well with many students                    0.35s
✓ bill recap paginates correctly with many bills                 0.34s
✓ it prevents duplicate enrollment                               0.03s
✓ it prevents deleting paid bills                                0.32s
✓ transaction report handles large datasets                      0.05s
✓ it prevents deleting academic year with semesters              0.03s
✓ it prevents deleting semester with bills                       0.31s
✓ it doesnt consume excessive memory                             0.32s
✓ it handles classroom change correctly                          0.03s

Tests: 17 passed (44 assertions) — Duration: 4.19s
```

## 🐛 BUG DITEMUKAN (2 Critical)

### BUG #1: `family_card_number` UNIQUE Constraint

> [!CAUTION]
> **Lokasi**: [create_students_table.php](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/database/migrations/2026_02_24_002001_create_students_table.php) — baris 13

**Masalah**: Kolom `family_card_number` pada tabel `students` dibuat sebagai `UNIQUE`. Migration selanjutnya ([add_status_to_students](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/database/migrations/2026_03_16_144728_add_status_to_students_and_update_bills_for_semester.php)) mencoba menghapus constraint ini via `DROP INDEX`, tapi perintah ini **tidak bekerja di SQLite** dan mungkin tidak reliable di semua MySQL versi.

**Dampak**: Fitur **"bayar 1 saudara, semua saudara seKK otomatis lunas"** di [BillUseCase::recordPayment](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/UseCases/BillUseCase.php#L283-L355) **TIDAK AKAN PERNAH BEKERJA** karena database mencegah 2 siswa memiliki `family_card_number` yang sama.

**Fix**: Buat migration baru yang secara eksplisit menghapus UNIQUE constraint dan menggantinya dengan INDEX biasa:

```php
Schema::table('students', function (Blueprint $table) {
    $table->dropUnique('students_family_card_number_unique');
    $table->index('family_card_number');
});
```

---

### BUG #2: `bills.status` ENUM Tidak Lengkap

> [!CAUTION]
> **Lokasi**: [add_status_to_students migration](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/database/migrations/2026_03_16_144728_add_status_to_students_and_update_bills_for_semester.php) — baris 39

**Masalah**: `bills.status` didefinisikan sebagai `ENUM('unpaid', 'partial', 'paid')`, tapi kode [EnrollmentUseCase::processDropout](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/UseCases/EnrollmentUseCase.php#L249-L251) mencoba set status ke `'cancelled'`.

**Dampak**: Fitur **DO (Drop Out) siswa** gagal total saat mencoba membatalkan tagihan masa depan. Di MySQL non-strict mode, value mungkin tersimpan tapi tidak valid. Di strict mode atau SQLite, langsung error.

**Fix**: Buat migration baru:

```php
// Mengubah ENUM bills.status untuk menambahkan 'cancelled'
DB::statement("ALTER TABLE bills MODIFY COLUMN status ENUM('unpaid', 'partial', 'paid', 'cancelled') DEFAULT 'unpaid'");
```

## Cara Menjalankan

### PHPUnit Feature Tests (otomatis, in-memory)
```bash
php artisan test --filter=StressTest
```

### Database Seeder (data massal di MySQL lokal)
```bash
php artisan db:seed --class=StressTestSeeder
```

### Artisan Load Test (benchmark endpoint)
```bash
php artisan stress:test --seed        # seed + test
php artisan stress:test --iterations=5  # 5x iterasi per endpoint
```
