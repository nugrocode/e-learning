@extends('layouts.dosen')

@section('title', 'Profil Saya')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-10 col-xl-8">

        <div class="d-flex align-items-center mb-4">
            <a href="{{ url('/dosen/dashboard') }}" class="text-decoration-none text-gray-500 hover:text-blue-900 transition me-3">
                <i class="bi bi-arrow-left text-2xl"></i>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 m-0">Profil Dosen</h2>
                <p class="text-sm text-gray-500 m-0">Kelola informasi akun dan foto profil Anda.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <form action="{{ url('/dosen/profil') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-0">
                    <div class="col-md-4 bg-gray-50 p-4 text-center border-end border-gray-100 d-flex flex-column justify-content-center align-items-center">
                        
                        <div class="position-relative mb-3 group d-inline-block" style="width: 160px; height: 160px;">
                            
                            <div class="w-100 h-100 rounded-circle overflow-hidden border-4 border-white shadow-md bg-white">
                                <img src="{{ $user->foto_profil && $user->foto_profil != 'default.png' ? asset('storage/profiles/' . $user->foto_profil) : asset('images/logo_ukit.png') }}"
                                     alt="Foto Profil"
                                     id="previewFoto"
                                     class="w-full h-full object-cover"
                                     onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                            </div>
                            
                            <div class="absolute bottom-1 right-1 bg-blue-600 text-white rounded-full w-10 h-10 d-flex align-items-center justify-content-center shadow-sm border-2 border-white cursor-pointer hover:bg-blue-700 transition" 
                                 onclick="document.getElementById('inputFoto').click()">
                                <i class="bi bi-camera-fill text-sm"></i>
                            </div>
                        </div>

                        <label class="btn btn-outline-primary btn-sm rounded-pill font-bold px-4 hover:bg-blue-600 hover:text-white transition cursor-pointer w-100 mt-2" style="max-width: 160px;">
                            <i class="bi bi-upload me-2"></i> Ganti Foto
                            <input type="file" name="file_foto" id="inputFoto" class="d-none" accept="image/*" onchange="previewImage(this)">
                        </label>
                        <p class="text-[10px] text-gray-400 mt-2 mb-0">Format: JPG, PNG (Max 2MB).</p>
                    </div>

                    <div class="col-md-8 p-4 p-md-5 bg-white">
                        
                        <div class="mb-4">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase tracking-wider">NIDN / Username</label>
                            <input type="text" class="form-control bg-gray-100 text-gray-500 cursor-not-allowed font-semibold border-0" value="{{ $user->nim_nidn }}" readonly>
                            <div class="form-text text-[10px] text-gray-400 mt-1">
                                <i class="bi bi-info-circle me-1"></i> Nomor Induk Dosen tidak dapat diubah sembarangan.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">Nama Lengkap & Gelar</label>
                            <input type="text" name="nama_lengkap" class="form-control font-semibold text-gray-800 border-gray-300 focus:ring-blue-500 focus:border-blue-500" value="{{ $user->nama_lengkap }}" required placeholder="Contoh: Dr. Budi Santoso, M.Kom">
                        </div>

                        <hr class="border-gray-100 my-4">

                        <div class="mb-4">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">
                                Password Baru <span class="text-gray-300 font-normal normal-case">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 border-gray-300 text-gray-400"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password_baru" class="form-control border-start-0 border-gray-300 ps-0 text-gray-800 focus:ring-0" placeholder="Kosongkan jika tidak ingin mengganti password">
                            </div>
                            <small class="text-[10px] text-gray-400 mt-1">Minimal 6 karakter jika ingin mengganti.</small>
                        </div>

                        <div class="d-flex justify-content-end pt-2">
                            <button type="submit" class="btn btn-dark border-0 px-4 py-2 rounded-lg font-bold shadow-md hover:bg-gray-800 transition">
                                <i class="bi bi-save2-fill me-2"></i> Simpan Perubahan
                            </button>
                        </div>

                    </div>
                </div>

            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-2xl mt-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="font-bold text-gray-800 mb-0">
                        <i class="bi bi-google text-blue-600 me-2"></i>Integrasi Google Drive
                    </h5>
                </div>
                
                @if(auth()->user()->google_token)
                    <div class="p-3 rounded-2xl bg-green-50 border border-green-100 d-flex align-items-center">
                        <div class="flex-shrink-0 bg-green-500 text-white rounded-full w-10 h-10 d-flex align-items-center justify-center me-3 shadow-sm">
                            <i class="bi bi-check-lg fs-4"></i>
                        </div>
                        <div>
                            <h6 class="font-bold text-green-800 mb-0">Drive Terhubung</h6>
                            <p class="text-[10px] text-green-600 mb-0">Tugas mahasiswa akan otomatis terupload ke akun Anda.</p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ url('/google/connect') }}" class="btn btn-sm btn-light border rounded-xl text-[10px] font-bold">
                                <i class="bi bi-arrow-repeat me-1"></i>Reconnect
                            </a>
                        </div>
                    </div>
                @else
                    <div class="p-3 rounded-2xl bg-orange-50 border border-orange-100 mb-3 d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-orange-500 fs-4 me-3"></i>
                        <div>
                            <h6 class="font-bold text-orange-800 mb-0 text-sm">Belum Terhubung</h6>
                            <p class="text-[10px] text-orange-600 mb-0">Hubungkan akun agar file tugas tidak membebani server lokal.</p>
                        </div>
                    </div>
                    
                    <a href="{{ url('/google/connect') }}" class="btn btn-white border shadow-sm w-100 py-3 rounded-xl d-flex align-items-center justify-center gap-3 hover:bg-gray-50 transition no-underline">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Google_Drive_icon_%282020%29.svg" width="24" alt="Drive">
                        <span class="font-bold text-gray-700">Hubungkan Akun Google Drive Sekarang</span>
                    </a>
                @endif
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