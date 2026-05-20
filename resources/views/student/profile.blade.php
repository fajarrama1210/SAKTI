@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Student Card Overview -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="avatar avatar-xl bg-gradient-success rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <span class="text-white font-weight-bold" style="font-size: 2.5rem;">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </span>
                    </div>
                    <h5 class="font-weight-bold mb-1 text-dark">{{ $student->name }}</h5>
                    <p class="text-xs text-muted mb-3">Siswa Terdaftar ({{ $student->status }})</p>
                    <div class="bg-light p-3 border-radius-lg mb-3">
                        <div class="d-flex flex-column align-items-center justify-content-center bg-white p-3 border-radius-md" style="border: 1px dashed #ced4da;">
                            <i class="fas fa-qrcode fa-4x text-dark"></i>
                            <span class="text-xxs font-weight-bold text-muted mt-2">{{ $student->qr_code }}</span>
                        </div>
                    </div>
                    <span class="badge bg-success-soft text-success text-xs font-weight-bold">
                        Kelas {{ $student->grade_level }} - {{ $student->classroom_name }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Detailed Profile Information -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header pb-0 bg-transparent border-0">
                    <h6 class="font-weight-bold text-dark mb-0">Informasi Profil & Akun</h6>
                </div>
                <div class="card-body p-4">
                    <h6 class="text-uppercase text-muted text-xxs font-weight-bold mb-3">Detail Akademik & Kependudukan</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="text-xs text-muted font-weight-bold d-block mb-1">Nama Lengkap</label>
                            <div class="p-2 bg-light border-radius-sm text-sm font-weight-bold text-dark">
                                {{ $student->name }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-xs text-muted font-weight-bold d-block mb-1">Nomor Induk Siswa Nasional (NISN)</label>
                            <div class="p-2 bg-light border-radius-sm text-sm font-weight-bold text-dark">
                                {{ $student->nisn }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-xs text-muted font-weight-bold d-block mb-1">Nomor Identitas (NIK/KIP)</label>
                            <div class="p-2 bg-light border-radius-sm text-sm font-weight-bold text-dark">
                                {{ $student->id_number ?? '-' }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-xs text-muted font-weight-bold d-block mb-1">Nomor Kartu Keluarga (KK)</label>
                            <div class="p-2 bg-light border-radius-sm text-sm font-weight-bold text-dark">
                                {{ $student->family_card_number }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-xs text-muted font-weight-bold d-block mb-1">Jurusan</label>
                            <div class="p-2 bg-light border-radius-sm text-sm font-weight-bold text-dark">
                                {{ $student->major_name }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-xs text-muted font-weight-bold d-block mb-1">Kelas Aktif</label>
                            <div class="p-2 bg-light border-radius-sm text-sm font-weight-bold text-dark">
                                {{ $student->classroom_name }} (Tingkat {{ $student->grade_level }})
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark my-3">

                    <h6 class="text-uppercase text-muted text-xxs font-weight-bold mb-3">Informasi Akun Portal</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-xs text-muted font-weight-bold d-block mb-1">Email / Username Login</label>
                            <div class="p-2 bg-light border-radius-sm text-sm font-weight-bold text-dark">
                                {{ $user->email }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-xs text-muted font-weight-bold d-block mb-1">Role Akses</label>
                            <div class="p-2 bg-light border-radius-sm text-sm font-weight-bold text-dark text-capitalize">
                                {{ $user->role }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 shadow-inner text-white mb-0" style="background-color: #5e72e4;">
                                <i class="fas fa-info-circle me-2"></i>
                                <span class="text-xs">Password default akun Anda adalah <strong>NISN</strong> Anda. Hubungi administrator sekolah jika Anda ingin mengubah email atau data kependudukan Anda.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Password Card -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header pb-0 bg-transparent border-0">
                    <h6 class="font-weight-bold text-dark mb-0">Ganti Password</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('student.profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="text-xs text-muted font-weight-bold d-block mb-1">Password Saat Ini</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" name="current_password" id="current_password" class="form-control bg-light border-0 @error('current_password') is-invalid @enderror" placeholder="Masukkan password saat ini" required>
                                    <button class="btn bg-light border-0 text-muted px-3 my-0 toggle-password-btn" type="button" data-target="current_password" style="box-shadow: none; z-index: 4;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-xs text-muted font-weight-bold d-block mb-1">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-key text-muted"></i></span>
                                    <input type="password" name="password" id="password" class="form-control bg-light border-0 @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                                    <button class="btn bg-light border-0 text-muted px-3 my-0 toggle-password-btn" type="button" data-target="password" style="box-shadow: none; z-index: 4;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-xs text-muted font-weight-bold d-block mb-1">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-key text-muted"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control bg-light border-0" placeholder="Ulangi password baru" required>
                                    <button class="btn bg-light border-0 text-muted px-3 my-0 toggle-password-btn" type="button" data-target="password_confirmation" style="box-shadow: none; z-index: 4;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-2">
                            <button type="submit" class="btn text-white" style="background-color: #1a8a5c;">
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
            title: "Gagal Memperbarui Password",
            text: "{{ $errors->first() }}"
        });
    });
</script>
@endif
@endpush
@endsection
