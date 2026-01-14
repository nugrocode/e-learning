@extends('layouts.app')

@section('title', 'Edit Profil Saya')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex align-items-center mb-4">
            <a href="{{ url('/dashboard') }}" class="text-decoration-none text-gray-600 me-3">
                <i class="bi bi-arrow-left text-xl"></i>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 m-0">Profil Saya</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-5 rounded-xl shadow-sm border">

            <form action="{{ url('/profil/update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-4 text-center border-end border-gray-100">
                        <div class="mb-3 position-relative d-inline-block">
                            <img src="{{ asset('images/' . ($user->foto_profil ?? 'default.png')) }}"
                                 class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 shadow-sm"
                                 id="previewFoto"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}'">

                            <label for="inputFoto" class="position-absolute bottom-0 end-0 bg-blue-900 text-white rounded-circle w-8 h-8 flex items-center justify-center cursor-pointer hover:bg-blue-700 transition border border-white" title="Ganti Foto">
                                <i class="bi bi-camera-fill text-sm"></i>
                            </label>
                            <input type="file" name="foto" id="inputFoto" class="d-none" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG (Max 2MB)</p>
                        <div class="mt-3">
                            <span class="badge bg-blue-100 text-blue-800 px-3 py-1 rounded-pill">{{ ucfirst($user->role) }}</span>
                        </div>
                    </div>

                    <div class="col-md-8 ps-md-4">

                        <div class="mb-3">
                            <label class="form-label font-bold text-gray-700 text-sm">NIM / NIDN</label>
                            <input type="text" class="form-control bg-gray-100 text-gray-500" value="{{ $user->nim_nidn }}" readonly>
                            <small class="text-xs text-gray-400">*NIM tidak dapat diubah.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-bold text-gray-700 text-sm">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ $user->nama_lengkap }}" required>
                        </div>

                        <hr class="my-4 border-gray-200">

                        <div class="mb-3">
                            <label class="form-label font-bold text-gray-700 text-sm">Ganti Password <span class="fw-normal text-muted">(Opsional)</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti password" autocomplete="new-password">
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary bg-blue-900 px-4 py-2 rounded-lg font-bold shadow-md hover:bg-blue-800 transition">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    // Script Preview Gambar sebelum upload
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
@endsection
