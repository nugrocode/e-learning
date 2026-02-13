@extends('layouts.app')

@section('title', 'Edit Profil Saya')

@section('content')
<div class="row justify-content-center animate-fade-in-up">
    <div class="col-12 col-lg-10 col-xl-8">

        {{-- Header Halaman --}}
        <div class="d-flex align-items-center mb-4 animate-fade-in-up">
            <a href="{{ url('/dashboard') }}" class="text-decoration-none text-gray-500 hover:text-blue-900 transition me-3">
                <i class="bi bi-arrow-left text-2xl"></i>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 m-0">Profil Saya</h2>
                <p class="text-sm text-gray-500 m-0">Kelola informasi akun dan foto profil Anda.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <form action="{{ url('/profil/update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-0">
                    {{-- 1. KOLOM KIRI: FOTO PROFIL --}}
                    {{-- [PERBAIKAN] Ubah p-5 jadi p-4 agar tidak sempit --}}
                    <div class="col-md-4 bg-gray-50 p-4 text-center border-end border-gray-100 d-flex flex-column justify-content-center align-items-center">
                        
                        {{-- 
                             [SOLUSI FINAL: WRAPPER PENGUNCI UKURAN]
                             Kita bungkus gambar dengan DIV yang ukurannya DIKUNCI MATI (Fixed Width/Height).
                             Ini mencegah gambar tergencet oleh padding container.
                        --}}
                        <div class="position-relative mb-3 group d-inline-block" style="width: 160px; height: 160px;">
                            
                            {{-- GAMBAR UTAMA --}}
                            <div class="w-100 h-100 rounded-circle overflow-hidden border-4 border-white shadow-md bg-white">
                                <img src="{{ $user->foto_profil && $user->foto_profil != 'default.png' ? asset('storage/profiles/' . $user->foto_profil) : asset('images/logo_ukit.png') }}"
                                     alt="Foto Profil"
                                     id="previewFoto"
                                     style="width: 100%; height: 100%; object-fit: cover;" 
                                     onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                            </div>
                            
                            {{-- IKON KAMERA --}}
                            <div class="absolute bottom-1 right-1 bg-blue-600 text-white rounded-full w-10 h-10 d-flex align-items-center justify-content-center shadow-sm border-2 border-white cursor-pointer hover:bg-blue-700 transition" 
                                 onclick="document.getElementById('inputFoto').click()">
                                <i class="bi bi-camera-fill text-sm"></i>
                            </div>
                        </div>

                        {{-- LABEL TOMBOL UPLOAD --}}
                        <label class="btn btn-outline-primary btn-sm rounded-pill font-bold px-4 hover:bg-blue-600 hover:text-white transition cursor-pointer w-100 mt-2" style="max-width: 160px;">
                            <i class="bi bi-upload me-2"></i> Ganti Foto
                            <input type="file" name="foto" id="inputFoto" class="d-none" accept="image/*" onchange="previewImage(this)">
                        </label>
                        <p class="text-xs text-gray-400 mt-2 mb-0">Format: JPG, PNG (Max 2MB).</p>
                    </div>

                    {{-- 2. KOLOM KANAN: FORM DATA --}}
                    <div class="col-md-8 p-4 p-md-5 bg-white">
                        
                        <div class="mb-4">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase tracking-wider">NIM / NIDN</label>
                            <input type="text" class="form-control bg-gray-100 text-gray-500 cursor-not-allowed font-semibold" value="{{ $user->nim_nidn }}" readonly>
                            <div class="form-text text-xs text-gray-400">Nomor Induk tidak dapat diubah.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control font-semibold text-gray-800" value="{{ $user->nama_lengkap }}" required placeholder="Masukkan nama lengkap">
                        </div>

                        <hr class="border-gray-100 my-4">

                        <div class="mb-4">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">
                                Password Baru <span class="text-gray-300 font-normal normal-case">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-gray-400"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control border-start-0 ps-0 text-gray-800" placeholder="Kosongkan jika tidak ganti">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-3">
                            <button type="submit" class="btn btn-primary bg-blue-900 border-0 px-4 py-2 rounded-pill font-bold hover:bg-blue-800 transition">
                                <i class="bi bi-save2-fill me-2"></i> Simpan Perubahan
                            </button>
                        </div>

                    </div>
                </div>

            </form>
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