<?php

use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\MajorController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\PaymentTypeController;
use App\Http\Controllers\Admin\PaymentRateController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\LettersController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::prefix('majors')->name('majors.')->group(function () {
        Route::get('/', [MajorController::class, 'index'])->name('index');
        Route::get('/add', [MajorController::class, 'create'])->name('create');
        Route::post('/add', [MajorController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [MajorController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MajorController::class, 'update'])->name('update');
        Route::delete('/{id}', [MajorController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('classrooms')->name('classrooms.')->group(function () {
        Route::get('/', [ClassroomController::class, 'index'])->name('index');
        Route::get('/add', [ClassroomController::class, 'create'])->name('create');
        Route::post('/add', [ClassroomController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ClassroomController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ClassroomController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClassroomController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::get('/add', [ScheduleController::class, 'create'])->name('create');
        Route::post('/add', [ScheduleController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ScheduleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/{id}', [ScheduleController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/add', [StudentController::class, 'create'])->name('create');
        Route::post('/add', [StudentController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [StudentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [StudentController::class, 'update'])->name('update');
        Route::delete('/{id}', [StudentController::class, 'destroy'])->name('destroy');
        Route::get('/template', [StudentController::class, 'downloadTemplate'])->name('template');
        Route::get('/import', [StudentController::class, 'import'])->name('import.view'); // New route for the page
        Route::post('/import', [StudentController::class, 'importExcel'])->name('import');
    });

    Route::prefix('enrollments')->name('enrollments.')->group(function () {
        Route::get('/', [EnrollmentController::class, 'index'])->name('index');
        Route::get('/add', [EnrollmentController::class, 'create'])->name('create');
        Route::post('/add', [EnrollmentController::class, 'store'])->name('store');
        Route::post('/{id}/dropout', [EnrollmentController::class, 'dropout'])->name('dropout');
        Route::put('/{id}/change-classroom', [EnrollmentController::class, 'changeClassroom'])->name('change-classroom');
        Route::get('/graduation', [EnrollmentController::class, 'graduationForm'])->name('graduation');
        Route::post('/graduation', [EnrollmentController::class, 'processGraduation'])->name('graduation.process');
        Route::get('/promotion', [EnrollmentController::class, 'promotionForm'])->name('promotion');
        Route::post('/promotion', [EnrollmentController::class, 'processPromotion'])->name('promotion.process');
        Route::delete('/{id}', [EnrollmentController::class, 'destroy'])->name('destroy');
    });

    // ====== MODUL KEUANGAN ======

    Route::prefix('academic-years')->name('academic-years.')->group(function () {
        Route::get('/', [AcademicYearController::class, 'index'])->name('index');
        Route::get('/add', [AcademicYearController::class, 'create'])->name('create');
        Route::post('/add', [AcademicYearController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AcademicYearController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AcademicYearController::class, 'update'])->name('update');
        Route::delete('/{id}', [AcademicYearController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('semesters')->name('semesters.')->group(function () {
        Route::get('/', [SemesterController::class, 'index'])->name('index');
        Route::get('/add', [SemesterController::class, 'create'])->name('create');
        Route::post('/add', [SemesterController::class, 'store'])->name('store');
        Route::get('/api/{academicYearId}', [SemesterController::class, 'getByAcademicYear'])->name('api');
        Route::get('/{id}/edit', [SemesterController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SemesterController::class, 'update'])->name('update');
        Route::delete('/{id}', [SemesterController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('payment-types')->name('payment-types.')->group(function () {
        Route::get('/', [PaymentTypeController::class, 'index'])->name('index');
        Route::get('/add', [PaymentTypeController::class, 'create'])->name('create');
        Route::post('/add', [PaymentTypeController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PaymentTypeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PaymentTypeController::class, 'update'])->name('update');
        Route::delete('/{id}', [PaymentTypeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('payment-rates')->name('payment-rates.')->group(function () {
        Route::get('/', [PaymentRateController::class, 'index'])->name('index');
        Route::get('/add', [PaymentRateController::class, 'create'])->name('create');
        Route::post('/add', [PaymentRateController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PaymentRateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PaymentRateController::class, 'update'])->name('update');
        Route::delete('/{id}', [PaymentRateController::class, 'destroy'])->name('destroy');
    });

    // ====== SPP / PEMBAYARAN ======
    Route::prefix('spp')->name('spp.')->group(function () {
        Route::get('/', [BillController::class, 'index'])->name('index');
        Route::get('/rekap', [BillController::class, 'recap'])->name('recap');
        Route::get('/matrix', [BillController::class, 'matrix'])->name('matrix');
        Route::get('/siswa/{studentId}', [BillController::class, 'studentDetail'])->name('student');
        Route::post('/{billId}/bayar', [BillController::class, 'pay'])->name('pay');
        Route::put('/{billId}/due-date', [BillController::class, 'updateDueDate'])->name('due-date');
        Route::delete('/{id}', [BillController::class, 'destroy'])->name('destroy');
        Route::post('/sync', [BillController::class, 'sync'])->name('sync');
        // Invoice admin
        Route::get('/invoice/{paymentId}', [InvoiceController::class, 'show'])->name('invoice')->where('paymentId', '[0-9]+');
    });

    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/add', [TransactionController::class, 'create'])->name('create');
        Route::post('/add', [TransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show')->where('id', '[0-9]+');
        Route::get('/{id}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('destroy');
    });

    // ====== LAPORAN ======

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/payment', [ReportController::class, 'paymentReport'])->name('payment');
        Route::get('/payment/excel', [ReportController::class, 'paymentExportExcel'])->name('payment.excel');
        Route::get('/payment/pdf', [ReportController::class, 'paymentExportPdf'])->name('payment.pdf');

        Route::get('/transaction', [ReportController::class, 'transactionReport'])->name('transaction');
        Route::get('/transaction/excel', [ReportController::class, 'transactionExportExcel'])->name('transaction.excel');
        Route::get('/transaction/pdf', [ReportController::class, 'transactionExportPdf'])->name('transaction.pdf');
    });

    Route::prefix('letters')->name('letters.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\LettersController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\LettersController::class, 'show'])->name('show')->where('id', '[0-9]+');
        Route::put('/{id}/status', [\App\Http\Controllers\Admin\LettersController::class, 'updateStatus'])->name('update-status');
    });
});

Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Student\StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/bills', [App\Http\Controllers\Student\StudentController::class, 'bills'])->name('bills');
    Route::get('/schedules', [App\Http\Controllers\Student\StudentController::class, 'schedules'])->name('schedules');
    Route::get('/profile', [App\Http\Controllers\Student\StudentController::class, 'profile'])->name('profile');
    Route::put('/profile/password', [App\Http\Controllers\Student\StudentController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [App\Http\Controllers\Student\StudentController::class, 'updateAvatar'])->name('profile.avatar');
    Route::get('/letters', [App\Http\Controllers\Student\LetterController::class, 'index'])->name('letters.index');
    Route::post('/letters', [App\Http\Controllers\Student\LetterController::class, 'store'])->name('letters.store');

    // ===== PEMBAYARAN QRIS MIDTRANS =====
    Route::post('/bills/{billId}/pay-qris', [App\Http\Controllers\Student\PaymentController::class, 'createQrisToken'])
        ->name('bills.pay-qris')
        ->where('billId', '[0-9]+');
    Route::get('/bills/{billId}/payment-status', [App\Http\Controllers\Student\PaymentController::class, 'checkPaymentStatus'])
        ->name('bills.payment-status')
        ->where('billId', '[0-9]+');

    // ===== INVOICE =====
    Route::get('/invoice/{paymentId}', [InvoiceController::class, 'show'])
        ->name('invoice.show')
        ->where('paymentId', '[0-9]+');
});

Route::prefix('kepala-sekolah')->name('kepala-sekolah.')->middleware(['auth', 'role:kepala_sekolah'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\KepalaSekolah\PrincipalController::class, 'dashboard'])->name('dashboard');
    Route::get('/students', [App\Http\Controllers\KepalaSekolah\PrincipalController::class, 'students'])->name('students');
    Route::get('/transactions', [App\Http\Controllers\KepalaSekolah\PrincipalController::class, 'transactions'])->name('transactions');
    Route::get('/bills', [App\Http\Controllers\KepalaSekolah\PrincipalController::class, 'bills'])->name('bills');
});
