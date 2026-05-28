@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
    <style>
        #qrcode img, #qrcode canvas {
            max-width: 100%;
            max-height: 100%;
            height: auto;
        }

    </style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="sakti-page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
            <div>
                <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                    <i class="fas fa-user-circle me-2"></i> Profil Siswa
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Informasi profil dan pengaturan akun Anda.
                </p>
            </div>
        </div>
    </div>

    @php
        $avatarUrl = null;
        if ($user->avatar && $user->avatar !== '0') {
            try {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                    $avatarUrl = asset('storage/' . $user->avatar);
                } else {
                    $avatarUrl = \Illuminate\Support\Facades\Storage::disk('s3')->url($user->avatar);
                }
            } catch (\Exception $e) {
                $avatarUrl = asset('storage/' . $user->avatar);
            }
        }
    @endphp

    <div class="row">
        <!-- Student Card Overview: xs full-width, md 5-col, lg 4-col -->
        <div class="col-12 col-md-5 col-lg-4 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-body text-center p-4">
                    <div class="position-relative d-inline-block mb-3">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="Foto Profil" class="rounded-circle img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #059669; box-shadow: 0 8px 20px rgba(5,150,105,.2);">
                        @else
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 100px; height: 100px; background: linear-gradient(135deg, #059669, #34d399); box-shadow: 0 8px 20px rgba(5,150,105,.3);">
                                <span class="text-white font-weight-bold" style="font-size: 2.5rem;">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <!-- Upload Button Overlay -->
                        <button type="button" class="btn btn-sm btn-dark rounded-circle position-absolute bottom-0 end-0 m-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; padding: 0; border: 2px solid #fff; z-index: 2; box-shadow: 0 4px 6px rgba(0,0,0,.1);" onclick="document.getElementById('avatarInput').click();">
                            <i class="fas fa-camera text-white" style="font-size: 0.75rem;"></i>
                        </button>
                    </div>

                    <form id="avatarForm" action="{{ route('student.profile.avatar') }}" method="POST" enctype="multipart/form-data" class="d-none">
                        @csrf
                        <input type="file" name="avatar" id="avatarInput" accept=".jpg,.jpeg,.png" onchange="document.getElementById('avatarForm').submit();">
                    </form>

                    <h5 class="font-weight-bold mb-1" style="color: var(--dark-text);">{{ $student->name }}</h5>
                    <p class="text-xs mb-3" style="color: var(--muted-text);">Siswa Terdaftar ({{ $student->status }})</p>
                    <div class="p-3 mb-3" style="background: #f8fafc; border-radius: 16px;">
                        <div class="d-flex flex-column align-items-center justify-content-center p-3" style="background: #fff; border-radius: 12px; border: 1px dashed #d1d5db; width: 140px; height: 140px; margin: 0 auto; padding: 10px !important;">
                            <div id="qrcode" style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;"></div>
                        </div>
                    </div>
                    <span class="badge px-3 py-2" style="background: #ecfdf5; color: #059669; font-weight: 700; font-size: .78rem;">
                        Kelas {{ $student->grade_level }} - {{ $student->classroom_name }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Detailed Profile Information: xs full-width, md 7-col, lg 8-col -->
        <div class="col-12 col-md-7 col-lg-8 mb-4">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-info-circle me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Informasi Profil & Akun
                    </h3>
                </div>
                <div class="card-body p-4">
                    <h6 class="text-uppercase text-xxs font-weight-bold mb-3" style="color: var(--muted-text); letter-spacing: .7px;">Detail Akademik & Kependudukan</h6>
                    <div class="row mb-4">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">Nama Lengkap</label>
                            <div class="p-2 text-sm font-weight-bold" style="background: #f8fafc; border-radius: 8px; color: var(--dark-text);">{{ $student->name }}</div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">NISN</label>
                            <div class="p-2 text-sm font-weight-bold" style="background: #f8fafc; border-radius: 8px; color: var(--dark-text);">{{ $student->nisn }}</div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">NIK/KIP</label>
                            <div class="p-2 text-sm font-weight-bold" style="background: #f8fafc; border-radius: 8px; color: var(--dark-text);">{{ $student->id_number ?? '-' }}</div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">No. Kartu Keluarga</label>
                            <div class="p-2 text-sm font-weight-bold" style="background: #f8fafc; border-radius: 8px; color: var(--dark-text);">{{ $student->family_card_number }}</div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">Jurusan</label>
                            <div class="p-2 text-sm font-weight-bold" style="background: #f8fafc; border-radius: 8px; color: var(--dark-text);">{{ $student->major_name }}</div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">Kelas Aktif</label>
                            <div class="p-2 text-sm font-weight-bold" style="background: #f8fafc; border-radius: 8px; color: var(--dark-text);">{{ $student->classroom_name }} (Tingkat {{ $student->grade_level }})</div>
                        </div>
                    </div>

                    <hr style="border-color: #e2e8f0; margin: 16px 0;">

                    <h6 class="text-uppercase text-xxs font-weight-bold mb-3" style="color: var(--muted-text); letter-spacing: .7px;">Informasi Akun Portal</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">Email / Username</label>
                            <div class="p-2 text-sm font-weight-bold" style="background: #f8fafc; border-radius: 8px; color: var(--dark-text);">{{ $user->email }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">Role Akses</label>
                            <div class="p-2 text-sm font-weight-bold text-capitalize" style="background: #f8fafc; border-radius: 8px; color: var(--dark-text);">{{ $user->role }}</div>
                        </div>
                        <div class="col-md-12">
                            <div class="p-3" style="background: linear-gradient(135deg, #059669, #34d399); border-radius: 12px; color: #fff;">
                                <i class="fas fa-info-circle me-2"></i>
                                <span class="text-xs">Password default akun Anda adalah <strong>NISN</strong> Anda. Hubungi administrator sekolah jika Anda ingin mengubah email atau data kependudukan Anda.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Password Card -->
            <div class="card dashboard-card mt-4">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-lock me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Ganti Password
                    </h3>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('student.profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">Password Saat Ini</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-lock" style="color: var(--muted-text);"></i></span>
                                    <input type="password" name="current_password" id="current_password" class="form-control bg-light border-0 @error('current_password') is-invalid @enderror" placeholder="Masukkan password saat ini" required>
                                    <button class="btn bg-light border-0 px-3 my-0 toggle-password-btn" type="button" data-target="current_password" style="box-shadow: none; z-index: 4; color: var(--muted-text);">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-key" style="color: var(--muted-text);"></i></span>
                                    <input type="password" name="password" id="password" class="form-control bg-light border-0 @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                                    <button class="btn bg-light border-0 px-3 my-0 toggle-password-btn" type="button" data-target="password" style="box-shadow: none; z-index: 4; color: var(--muted-text);">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-key" style="color: var(--muted-text);"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control bg-light border-0" placeholder="Ulangi password baru" required>
                                    <button class="btn bg-light border-0 px-3 my-0 toggle-password-btn" type="button" data-target="password_confirmation" style="box-shadow: none; z-index: 4; color: var(--muted-text);">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-2">
                            <button type="submit" class="btn btn-sakti-primary">
                                <i class="fas fa-save me-2"></i> Perbarui Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleButtons = document.querySelectorAll('.toggle-password-btn');
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                if (targetInput) {
                    const type = targetInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    targetInput.setAttribute('type', type);
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                }
            });
        });
    });
</script>
@if ($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Swal.fire({
            icon: "error",
            title: "Gagal Memperbarui Data",
            text: "{{ $errors->first() }}"
        });
    });
</script>
@endif
@endpush
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $student->nisn }}",
            width: 120,
            height: 120,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    });
</script>
@endpush
