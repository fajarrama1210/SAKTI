# Stress Test Sistem SAKTI

## Tujuan
Membuat suite stress test komprehensif untuk menguji ketahanan, performa, dan keandalan sistem SAKTI (Sistem Administrasi Keuangan Terpadu Instansi) di bawah beban tinggi.

## Konteks
Sistem SAKTI adalah aplikasi Laravel untuk manajemen keuangan sekolah yang mencakup:
- Manajemen siswa, kelas, jurusan
- Enrollment (penempatan siswa per tahun ajaran)
- Pembayaran SPP dengan logika saudara seKK
- Transaksi keuangan umum (income/expense)
- Laporan & export (Excel/PDF)
- Auto-generate tagihan saat semester dibuat

## Pendekatan Stress Test

Kita akan membuat **3 layer** stress test:

### 1. Database Seeder Massal (`StressTestSeeder`)
Mengisi database dengan volume data besar untuk simulasi kondisi produksi:
- **1000+ siswa** tersebar di berbagai kelas & jurusan
- **5000+ tagihan** (bills) dengan berbagai status
- **2000+ pembayaran** (payments)
- **3000+ transaksi** keuangan
- Data saudara seKK (family_card_number duplikat)

### 2. Feature Test - Laravel PHPUnit (`StressTest.php`)
Test otomatis yang menguji:
- **Concurrent-like payment processing** - bayar banyak tagihan berturut-turut
- **Mass enrollment** - daftarkan ratusan siswa sekaligus
- **Mass graduation** - luluskan ratusan siswa dengan cek tunggakan
- **Auto-generate bills** - generate tagihan untuk ratusan siswa
- **Sibling billing cascade** - bayar 1 saudara, semua saudara lunas
- **Heavy query pages** - Dashboard, Report, SPP recap dengan data besar
- **Edge cases** - hapus data berelasi, duplikat enrollment, dll

### 3. Artisan Command untuk Load Test (`stress:test`)
Command interaktif yang:
- Seed data massal
- Hit semua endpoint utama secara berulang
- Ukur response time & memory usage
- Generate laporan performa

## Proposed Changes

### Database Seeder
#### [NEW] [StressTestSeeder.php](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/database/seeders/StressTestSeeder.php)
- Buat 5 jurusan, 30 kelas, 1000 siswa, 3 tahun ajaran, semester, payment types, rates
- Generate tagihan & pembayaran massal

### Feature Tests
#### [NEW] [StressTest.php](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/tests/Feature/StressTest.php)
- 12+ test cases menguji performa & keandalan
- Menggunakan `RefreshDatabase` trait dengan SQLite in-memory

### Artisan Command
#### [NEW] [StressTestCommand.php](file:///d:/Matkul%20Semester%202/WORKSHOP%20ANALISIS%20DAN%20PERANCANGAN%20SISTEM%20INFORMASI/SAKTI/app/Console/Commands/StressTestCommand.php)
- Command `php artisan stress:test` untuk load testing endpoint secara real-time
- Output tabel response time per endpoint

## Verification Plan

### Automated Tests
```bash
php artisan test --filter=StressTest
```

### Manual Verification
```bash
php artisan db:seed --class=StressTestSeeder
php artisan stress:test
```
