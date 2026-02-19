@extends('layouts.dosen')

@section('title', 'Profil Saya')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">

            {{-- HEADER HALAMAN --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ url('/dosen/dashboard') }}" class="text-decoration-none text-secondary me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <div>
                    <h2 class="fw-bold text-dark m-0">Profil Dosen</h2>
                    <p class="text-muted small m-0">Kelola informasi akun dan integrasi Cloud.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <form action="{{ url('/dosen/profil') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-0">
                        
                        {{-- ================================================= --}}
                        {{-- KOLOM KIRI: FOTO & STATUS (SIDEBAR)               --}}
                        {{-- ================================================= --}}
                        <div class="col-md-4 bg-light border-end d-flex flex-column align-items-center text-center p-4">
                            
                            {{-- 1. FOTO PROFIL --}}
                            <div class="position-relative mb-3" style="width: 130px; height: 130px;">
                                <img id="previewFoto" 
                                     src="{{ $user->foto_profil && $user->foto_profil != 'default.png' ? asset('storage/profiles/' . $user->foto_profil) : asset('images/default_profile.png') }}" 
                                     class="w-100 h-100 rounded-circle border border-4 border-white shadow-sm object-fit-cover"
                                     style="object-fit: cover;"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}&background=random'">
                                
                                {{-- Tombol Edit Overlay --}}
                                <label for="file_foto" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle shadow-sm d-flex justify-content-center align-items-center" 
                                       style="width: 36px; height: 36px; cursor: pointer; border: 2px solid white;">
                                    <i class="bi bi-camera-fill small"></i>
                                </label>
                                <input type="file" name="foto" id="file_foto" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>

                            {{-- Nama & NIDN --}}
                            <h5 class="fw-bold text-dark mb-1">{{ $user->nama_lengkap }}</h5>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 mb-4">
                                NIDN: {{ $user->nim_nidn }}
                            </span>

                            <hr class="w-100 border-secondary-subtle my-2">

                            {{-- 2. STATUS GOOGLE DRIVE --}}
                            <div class="w-100 text-start mt-3">
                                <label class="small fw-bold text-muted text-uppercase mb-2">Integrasi Cloud</label>

                                @if($user->google_token)
                                    {{-- JIKA SUDAH TERHUBUNG --}}
                                    <div class="card border-success border-opacity-25 bg-success-subtle shadow-none">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-google"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-success mb-0" style="font-size: 0.9rem;">Google Drive</h6>
                                                    <small class="text-success fw-bold" style="font-size: 0.75rem;">
                                                        <i class="bi bi-check-circle-fill me-1"></i>Terhubung
                                                    </small>
                                                </div>
                                            </div>
                                            <p class="mb-0 mt-2 text-success opacity-75" style="font-size: 0.75rem; line-height: 1.2;">
                                                File tugas mahasiswa otomatis tersimpan di Drive Anda.
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    {{-- JIKA BELUM TERHUBUNG --}}
                                    <div class="card border-warning border-opacity-25 bg-warning-subtle shadow-none">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                    <i class="bi bi-exclamation-lg fw-bold"></i>
                                                </div>
                                                <h6 class="fw-bold text-warning-emphasis mb-0" style="font-size: 0.9rem;">Google Drive</h6>
                                            </div>
                                            <p class="mb-3 text-muted" style="font-size: 0.75rem; line-height: 1.2;">
                                                Hubungkan untuk menghemat penyimpanan server lokal.
                                            </p>
                                            <a href="{{ url('/google/connect') }}" class="btn btn-dark w-100 btn-sm fw-bold d-flex align-items-center justify-content-center gap-2">
                                                <i class="bi bi-google"></i> Hubungkan
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>

                        {{-- ================================================= --}}
                        {{-- KOLOM KANAN: FORM EDIT                            --}}
                        {{-- ================================================= --}}
                        <div class="col-md-8 p-4 p-lg-5 bg-white">
                            
                            {{-- Alert Messages --}}
                            @if(session('success'))
                                <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                    <div>{{ session('success') }}</div>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                    <div>{{ session('error') }}</div>
                                </div>
                            @endif

                            <h5 class="fw-bold text-dark mb-4">Edit Informasi</h5>

                            {{-- Nama Lengkap --}}
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nama Lengkap & Gelar</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" name="nama_lengkap" class="form-control bg-light border-start-0 text-dark fw-semibold fs-6" 
                                           value="{{ $user->nama_lengkap }}" required>
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="mb-5">
                                <label class="form-label small fw-bold text-muted text-uppercase">
                                    Password Baru <span class="fw-normal text-muted text-lowercase">(opsional)</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control bg-light border-start-0 fs-6" 
                                           placeholder="Kosongkan jika tidak ingin mengganti">
                                </div>
                                <div class="form-text small mt-1">
                                    Gunakan kombinasi huruf dan angka untuk keamanan.
                                </div>
                            </div>

                            {{-- Tombol Simpan --}}
                            <div class="d-flex justify-content-end pt-3 border-top">
                                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-save2-fill me-2"></i> Simpan Perubahan
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewFoto').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush