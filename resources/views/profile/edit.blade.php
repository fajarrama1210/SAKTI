@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="sakti-page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
            <div>
                <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                    <i class="fas fa-user-circle me-2"></i> Profil Pengguna
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
        <!-- User Card Overview: xs/sm full-width, md 5-col, lg 4-col -->
        <div class="col-12 col-md-5 col-lg-4 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-body text-center p-4">
                    <div class="position-relative d-inline-block mb-3">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="Foto Profil" class="rounded-circle img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #059669; box-shadow: 0 8px 20px rgba(5,150,105,.2);">
                        @else
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 100px; height: 100px; background: linear-gradient(135deg, #059669, #34d399); box-shadow: 0 8px 20px rgba(5,150,105,.3);">
                                <span class="text-white font-weight-bold" style="font-size: 2.5rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <!-- Upload Button Overlay -->
                        <button type="button" class="btn btn-sm btn-dark rounded-circle position-absolute bottom-0 end-0 m-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; padding: 0; border: 2px solid #fff; z-index: 2; box-shadow: 0 4px 6px rgba(0,0,0,.1);" onclick="document.getElementById('avatarInput').click();">
                            <i class="fas fa-camera text-white" style="font-size: 0.75rem;"></i>
                        </button>
                    </div>

                    <form id="avatarForm" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="d-none">
                        @csrf
                        <input type="file" name="avatar" id="avatarInput" accept=".jpg,.jpeg,.png" onchange="document.getElementById('avatarForm').submit();">
                    </form>

                    <h5 class="font-weight-bold mb-1" style="color: var(--dark-text);">{{ $user->name }}</h5>
                    <p class="text-xs mb-3" style="color: var(--muted-text);">Pengguna Terdaftar</p>
                    <span class="badge px-3 py-2 text-capitalize" style="background: #ecfdf5; color: #059669; font-weight: 700; font-size: .78rem;">
                        {{ str_replace('_', ' ', $user->role) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Detailed Profile Information: xs/sm full-width, md 7-col, lg 8-col -->
        <div class="col-12 col-md-7 col-lg-8 mb-4">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-info-circle me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Informasi Profil & Akun
                    </h3>
                </div>
                <div class="card-body p-4">
                    <h6 class="text-uppercase text-xxs font-weight-bold mb-3" style="color: var(--muted-text); letter-spacing: .7px;">Detail Pengguna</h6>
                    
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control bg-light border-0" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">Email</label>
                                <input type="email" name="email" class="form-control bg-light border-0" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>

                        <div class="text-end mt-2">
                            <button type="submit" class="btn btn-sakti-primary">
                                <i class="fas fa-save me-2"></i> Perbarui Profil
                            </button>
                        </div>
                    </form>
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
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('put')

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">Password Saat Ini</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-lock" style="color: var(--muted-text);"></i></span>
                                    <input type="password" name="current_password" id="current_password" class="form-control bg-light border-0" placeholder="Masukkan password saat ini" required>
                                    <button class="btn bg-light border-0 px-3 my-0 toggle-password-btn" type="button" data-target="current_password" style="box-shadow: none; z-index: 4; color: var(--muted-text);">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="text-xs font-weight-bold d-block mb-1" style="color: var(--muted-text);">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-key" style="color: var(--muted-text);"></i></span>
                                    <input type="password" name="password" id="password" class="form-control bg-light border-0" placeholder="Minimal 8 karakter" required>
                                    <button class="btn bg-light border-0 px-3 my-0 toggle-password-btn" type="button" data-target="password" style="box-shadow: none; z-index: 4; color: var(--muted-text);">
                                        <i class="fas fa-eye"></i>
                                    </button>
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
