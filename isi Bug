# Bug Report & Connected Stress Test Data Generator untuk SAKTI

## Hasil Analisis Bugs

Setelah membaca seluruh codebase — model, migration, controller, usecase, seeder — saya menemukan **11 bug/masalah** yang perlu diperhatikan:

---

### 🔴 BUG KRITIS (Crash / Data Error)

#### BUG-1: `family_card_number` UNIQUE constraint vs Logika Saudara seKK
- **File**: [students migration](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/database/migrations/2026_02_24_002001_create_students_table.php#L13) + [BillUseCase::processSiblingDiscounts](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/UseCases/BillUseCase.php#L451-L517)
- **Masalah**: `family_card_number` dibuat `->unique()`, padahal fitur utama SAKTI adalah **saudara se-KK bisa berbagi tagihan**. Constraint ini mencegah 2 siswa punya KK yang sama.
- **Impact**: Fitur diskon saudara 100% broken. Data di StressTestSeeder juga pakai random KK per siswa (karena unique).
- **Fix**: Buat migration baru yang mengubah UNIQUE menjadi INDEX biasa.

#### BUG-2: `bills.status` ENUM tidak punya 'cancelled'
- **File**: [bills migration](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/database/migrations/2026_03_07_190004_create_bills_table.php#L18) + [EnrollmentUseCase::processDropout](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/UseCases/EnrollmentUseCase.php#L258-L261)
- **Masalah**: bills ENUM hanya `['unpaid','partial','paid']`, tapi `processDropout()` coba set `'cancelled'` → CRASH di MySQL strict mode.
- **Fix**: Buat migration untuk menambah `'cancelled'` ke ENUM.

#### BUG-3: `payments.payment_method` ENUM tidak punya 'transfer' dan 'other'
- **File**: [payments migration](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/database/migrations/2026_03_07_190006_create_payments_table.php#L15) + [BillController::pay](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/Http/Controllers/Admin/BillController.php#L93) + [BillUseCase::processSiblingDiscounts](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/UseCases/BillUseCase.php#L480)
- **Masalah**: payments ENUM hanya `['cash','qris']`, tapi controller validate `'cash,transfer,other'`, dan sibling logic pakai `'other'`. Insert `'transfer'` atau `'other'` → CRASH.
- **Fix**: Buat migration untuk expand ENUM ke `['cash','qris','transfer','other']`.

#### BUG-4: `bills` tabel punya `student_id` di seeder tapi migration pakai `family_card_number`
- **File**: [bills migration](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/database/migrations/2026_03_07_190004_create_bills_table.php) vs [BillUseCase](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/UseCases/BillUseCase.php#L301-L312)
- **Masalah**: Migration bills punya `family_card_number` + unique constraint `['family_card_number', 'academic_year_id', 'month', 'year']`, tapi **BillUseCase insert pakai `student_id` dan tidak set `family_card_number`**. Ini menyebabkan `family_card_number` di tabel bills kosong/NULL → insert gagal karena kolom ini NOT NULL (char(16)).
- **Fix**: BillUseCase `generateMissingBills()` harus include `family_card_number` saat insert bill, ATAU migration perlu diubah buat kolom `student_id` sebagai gantinya.

> [!CAUTION]
> BUG-4 adalah bug paling kritis: **setiap generate tagihan baru akan gagal** karena kolom `family_card_number` tidak diisi di bills table saat insert. Unique constraint juga pakai `family_card_number` bukan `student_id`.

---

### 🟡 BUG MEDIUM (Logic Error / Data Inconsistency)

#### BUG-5: `DashboardController` join `b.student_id` tapi bills migration pakai `family_card_number`
- **File**: [DashboardController](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/Http/Controllers/Admin/DashboardController.php#L67-L73)
- **Masalah**: Recent payments query join `'b.student_id' = 's.id'` tapi tabel `bills` tidak punya kolom `student_id` menurut migration original. Kemungkinan migration sudah berubah runtime tapi tidak ter-reflect di file.
- **Dampak**: Query dashboard bisa crash.

#### BUG-6: `Letter` model typo di cast: `dsubmission_date` (seharusnya `submission_date`)
- **File**: [Letter.php](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/Models/Letter.php#L13)
- **Masalah**: `'dsubmission_date' => 'date'` — typo `d` di depan. Field cast tidak akan bekerja.
- **Fix**: Ubah ke `'submission_date' => 'date'`.

#### BUG-7: `StressTestSeeder` insert `'cancelled'` ke bills.status
- **File**: [StressTestSeeder](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/database/seeders/StressTestSeeder.php#L373)
- **Masalah**: `$status = ... : 'cancelled'` tapi ENUM hanya `['unpaid','partial','paid']`. Insert akan gagal.
- **Terkait**: BUG-2. Fix BUG-2 akan fix ini juga.

#### BUG-8: `Bill` model tidak punya relasi `student()`
- **File**: [Bill.php](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/Models/Bill.php)
- **Masalah**: Tidak ada `student()` relationship, tapi BillController di line 116 akses `$bill->student_id`. Ini hanya bekerja karena pakai DB query builder raw, bukan Eloquent.
- **Fix**: Tambah relasi `student()` di Bill model.

#### BUG-9: `Student` model tidak punya relasi `bills()` dan `enrollments()`
- **File**: [Student.php](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/Models/Student.php)
- **Masalah**: Missing crucial relationships yang seharusnya ada di model keuangan.

#### BUG-10: `Transaction` model tidak punya relasi apapun
- **File**: [Transaction.php](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/Models/Transaction.php)
- **Masalah**: Tidak ada `payment()` atau `recordedBy()` relations padahal FK ada di DB.

#### BUG-11: `User` model relasi `student()` pakai `BelongsTo` bukan `HasOne`
- **File**: [User.php](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/Models/User.php#L50-L53)
- **Masalah**: Di Student punya `HasOne` user, tapi User punya `BelongsTo` student. Dari migration, users tidak punya `student_id` FK. Seharusnya relasi ini `HasOne` di User juga, atau tidak ada sama sekali karena FK tidak exist.

---

## Proposed Changes

### Phase 1: Fix Database Schema Bugs (Migration Fixes)

#### [NEW] `database/migrations/2026_05_19_100001_fix_critical_schema_bugs.php`
Migration untuk menyelesaikan 4 bug kritis sekaligus:
1. **Ubah** `family_card_number` di students dari UNIQUE ke INDEX biasa (BUG-1)
2. **Tambah** `'cancelled'` ke ENUM `bills.status` (BUG-2)
3. **Expand** ENUM `payments.payment_method` ke `['cash','qris','transfer','other']` (BUG-3)
4. **Tambah** kolom `student_id` FK ke `bills` jika belum ada, dan drop unique constraint lama + buat unique baru berbasis `student_id` (BUG-4)

---

### Phase 2: Fix Model Relationships

#### [MODIFY] `app/Models/Letter.php`
- Fix typo `dsubmission_date` → `submission_date` (BUG-6)

#### [MODIFY] `app/Models/Bill.php`
- Tambah relasi `student()`, `semester()` (BUG-8)

#### [MODIFY] `app/Models/Student.php`
- Tambah relasi `bills()`, `enrollments()` (BUG-9)

#### [MODIFY] `app/Models/Transaction.php`
- Tambah relasi `payment()`, `recordedBy()` (BUG-10)

---

### Phase 3: Connected Stress Test Data Generator (Baru)

#### [NEW] `database/seeders/ConnectedDataSeeder.php`
Seeder baru yang menghasilkan data **realistis dan terhubung ke seluruh sistem**:

1. **Admin & Users** — 2 admin + 5 operator
2. **Jurusan → Kelas** — 5 jurusan × 6 kelas = 30 kelas
3. **Tahun Ajaran** — 2 TA (2024/2025 lama + 2025/2026 aktif), masing-masing 2 semester
4. **Payment Types + Rates** — 4 jenis pembayaran × 3 grade × 5 jurusan-specific rates
5. **Siswa** — 500 siswa dengan **family groups** (150 KK → rata-rata 3.3 anak/KK) agar saudara seKK bisa ditest
6. **Enrollment** — Semua siswa enrolled di TA aktif + 300 siswa punya histori di TA lama (naik kelas)
7. **Tagihan (Bills + BillItems)** — Auto-generate via BillUseCase (bukan manual insert), memastikan `family_card_number` dan semua constraint benar
8. **Pembayaran (Payments + Allocations)** — 40% lunas penuh, 20% cicilan partial, 5% saudara lunas bareng, 35% belum bayar
9. **Transaksi (Transactions)** — 100 transaksi income (auto dari payment) + 200 expense manual
10. **Surat Izin (Letters)** — 50 surat dengan berbagai status
11. **Jadwal (Schedules)** — 150 jadwal pelajaran untuk 30 kelas
12. **Edge Cases** — 10 siswa DO, 20 siswa lulus, 5 pindah kelas

> [!IMPORTANT]
> Perbedaan kunci dari `StressTestSeeder` lama:
> - Data **benar-benar mengalir** melalui UseCases (bukan raw DB insert yang bypass validasi)
> - **Saudara seKK** beneran ada (2-4 siswa per KK)
> - **Histori enrollment** lintas tahun ajaran
> - **Partial payments** dan **payment allocations** yang benar
> - **Semua tabel** diisi termasuk letters, schedules
> - Bisa dipakai untuk demo/presentasi, bukan cuma stress test

#### [NEW] `app/Console/Commands/GenerateTestData.php`
Artisan command: `php artisan sakti:generate-data`
- Options: `--fresh` (reset dulu), `--scale=small|medium|large`
- Menjalankan `ConnectedDataSeeder` dengan progress bar

#### [MODIFY] `database/seeders/DatabaseSeeder.php`
- Tambah opsi call `ConnectedDataSeeder` yang bisa di-toggle

---

### Phase 4: Stress Test yang Benar-benar Test Semua Fitur

#### [NEW] `tests/Feature/SystemIntegrationTest.php`
Test end-to-end yang menggunakan data dari seeder connected:
1. **Test saudara seKK** — bayar 1 siswa, cek saudara otomatis lunas
2. **Test alur enrollment lengkap** — enroll → generate bills → bayar → naik kelas → enroll baru → generate bills baru
3. **Test DO dengan cancel tagihan** — pastikan bills jadi 'cancelled'
4. **Test payment allocation** — bayar partial, cek alokasi per item
5. **Test report accuracy** — angka di laporan = angka di database
6. **Test dashboard data integrity** — semua angka dashboard benar

---

## Open Questions

> [!IMPORTANT]
> **Tentang BUG-4 (bills schema)**: Saat ini migration `bills` pakai `family_card_number` tapi semua code (BillUseCase, StressTestSeeder) pakai `student_id`. 
> 
> **Apakah `student_id` sudah ditambahkan ke tabel bills di database yang running** (mungkin melalui manual alter atau migration yang belum saya temukan)? Kalau ya, saya akan membuat migration yang safe (cek dulu sebelum alter). Kalau belum, ini masalah kritis karena **semua generate tagihan pasti gagal**.

> [!IMPORTANT]
> **Tentang skala data**: Untuk seeder connected, saya planning 500 siswa. Apakah mau lebih besar (1000+) atau cukup 500 untuk demo + stress test? Skala bisa diatur via `--scale` option.

## Verification Plan

### Automated Tests
1. `php artisan migrate:fresh` — pastikan migration baru jalan
2. `php artisan sakti:generate-data --fresh` — pastikan data ter-seed tanpa error
3. `php artisan test --filter=SystemIntegrationTest` — jalankan test suite baru
4. `php artisan stress:test` — jalankan stress test command yang sudah ada
5. Browse via browser — cek semua halaman (dashboard, SPP, enrollment, report)

### Manual Verification
- Check di browser: `/admin/spp/siswa/{id}` — kalender SPP terisi
- Check: `/admin/spp/matrix` — grid pembayaran ada data
- Check: `/admin/reports/payment` — laporan ada angka benar
- Check: `/dashboard` — chart menunjukkan income/expense
