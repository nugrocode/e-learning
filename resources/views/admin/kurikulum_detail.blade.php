@extends('layouts.admin')

@section('title', 'Kurikulum - ' . $konsentrasi->nama_konsentrasi)

@section('content')
    <a href="{{ url('/admin/kurikulum') }}" class="text-gray-500 hover:text-gray-800 text-sm font-bold mb-4 d-inline-block transition">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Konsentrasi
    </a>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                <span style="color: #2d3748;">{{ $konsentrasi->nama_konsentrasi }}</span>
            </h2>
            <p class="text-gray-500 text-sm">Atur mata kuliah untuk konsentrasi ini.</p>
        </div>
        
        <button class="btn rounded-lg font-bold py-2 px-4 shadow-sm transition" 
            style="background-color: #FACC15; color: #2d3748; border: none;"
            data-bs-toggle="modal" data-bs-target="#modalTambahMK">
            <i class="bi bi-plus-lg me-2"></i> Tambah Mata Kuliah
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-lg d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-xl"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-lg">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- BLOK PENDING: Tempat MK baru yang belum memiliki urutan --}}
    @if($new_courses->count() > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-5 animate-fade-in-up">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="font-bold text-yellow-800 m-0 flex items-center gap-2">
                    <i class="bi bi-exclamation-circle-fill"></i> Pending (Belum Diurutkan)
                </h5>
                
                {{-- Logika Tombol: Jika kurikulum masih kosong gunakan Re-Sort, jika sudah ada isinya gunakan Smart Insert (Update) --}}
                @if($courses->count() == 0)
                    <form action="{{ url('/admin/kurikulum/reset/' . $konsentrasi->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn font-bold shadow-sm transition text-sm px-3 py-2 rounded-lg" 
                                style="background-color: #FACC15; color: #2d3748; border: none;">
                            <i class="bi bi-stars"></i> AI Auto-Sort
                        </button>
                    </form>
                @else
                    <form action="{{ url('/admin/kurikulum/update/' . $konsentrasi->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn font-bold shadow-sm transition text-sm px-3 py-2 rounded-lg" 
                                style="background-color: #FACC15; color: #2d3748; border: none;">
                            <i class="bi bi-stars"></i> AI Smart Insert
                        </button>
                    </form>
                @endif
            </div>
            <div class="row g-3">
                @foreach($new_courses as $mk)
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white p-3 rounded-lg border border-yellow-100 shadow-sm d-flex align-items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center font-bold">?</div>
                            <div>
                                <h6 class="font-bold text-gray-800 m-0 text-sm">{{ $mk->nama_mk }}</h6>
                                <span class="text-[10px] text-gray-400">Menunggu AI...</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 d-flex justify-content-between align-items-center">
            <h5 class="font-bold text-gray-700 m-0 text-sm md:text-base">Struktur Kurikulum</h5>
            @if($courses->count() > 1)
                <form action="{{ url('/admin/kurikulum/reset/' . $konsentrasi->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary font-bold text-xs border-gray-300">
                        <i class="bi bi-arrow-repeat me-1"></i> AI Re-Sort
                    </button>
                </form>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Mata Kuliah</th>
                        <th style="min-width: 180px;">Dosen Pengampu</th>
                        <th>Deskripsi</th>
                        <th class="text-end px-4 text-nowrap" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($courses as $mk)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    @if($mk->gambar)
                                        <img src="{{ asset('storage/thumbnails/' . $mk->gambar) }}" class="w-10 h-10 rounded object-cover border">
                                    @else
                                        <div class="w-10 h-10 rounded bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xs border">
                                            {{ substr($mk->nama_mk, 0, 2) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong class="text-gray-800 d-block">{{ $mk->nama_mk }}</strong>
                                        {{-- <span class="text-xs text-gray-400">ID: {{ $mk->id }}</span> --}}
                                    </div>
                                </div>
                            </td>
                            
                            <td>
                                @if($mk->dosen)
                                    <div class="d-flex align-items-center gap-2 p-1 bg-gray-50 rounded border border-gray-100 w-fit">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px]" style="background-color: #fef08a; color: #854d0e;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <span class="text-gray-700 font-medium text-xs">{{ $mk->dosen->nama_lengkap }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">Belum diset</span>
                                @endif
                            </td>

                            <td class="text-gray-500">
                                <p class="line-clamp-2 m-0 text-xs leading-relaxed">{{ $mk->deskripsi }}</p>
                            </td>

                            <td class="text-end px-4 text-nowrap">
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="btn btn-sm btn-light border rounded hover:bg-blue-50" 
                                        style="color: #0d6efd;" 
                                        data-bs-toggle="modal" data-bs-target="#modalEdit{{ $mk->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    
                                    <form action="{{ url('/admin/mata-kuliah/'.$mk->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="concentration_id" value="{{ $konsentrasi->id }}">
                                        <button class="btn btn-sm btn-light text-red-600 border rounded hover:bg-red-50">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL EDIT MASTER MK --}}
                        <div class="modal fade" id="modalEdit{{ $mk->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-xl border-0 shadow-lg">
                                    <div class="modal-header py-3 border-bottom-0" style="background-color: #2d3748;">
                                        <h5 class="modal-title font-bold text-sm" style="color: #FACC15;">Edit Master MK</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ url('/admin/mata-kuliah/'.$mk->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="alert alert-warning text-xs border-0 bg-yellow-50 text-yellow-800 mb-3">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                Perubahan data di sini akan berlaku untuk <strong>semua konsentrasi</strong>.
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs font-bold uppercase">Nama MK</label>
                                                <input type="text" name="nama_mk" class="form-control rounded-lg" value="{{ $mk->nama_mk }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs font-bold uppercase">Dosen Pengampu</label>
                                                <select name="dosen_id" class="form-select rounded-lg">
                                                    @foreach($dosens as $dosen)
                                                        <option value="{{ $dosen->id }}" {{ $mk->dosen_id == $dosen->id ? 'selected' : '' }}>{{ $dosen->nama_lengkap }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs font-bold uppercase">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control rounded-lg" rows="3">{{ $mk->deskripsi }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="submit" class="btn w-100 font-bold py-2 rounded-lg" 
                                                    style="background-color: #FACC15; color: #2d3748; border: none;">Simpan Perubahan Master</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-gray-400">Belum ada kurikulum yang diurutkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH / AMBIL MK --}}
    <div class="modal fade" id="modalTambahMK" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-xl border-0 shadow-lg">
                <div class="modal-header py-3 border-bottom-0" style="background-color: #2d3748;">
                    <h5 class="modal-title font-bold text-sm" style="color: #FACC15;">Tambah / Ambil Mata Kuliah</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ url('/admin/mata-kuliah') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="concentration_id" value="{{ $konsentrasi->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">Nama Mata Kuliah</label>
                            <input type="text" name="nama_mk" class="form-control rounded-lg" placeholder="Contoh: Algoritma" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">Dosen (Wajib jika MK Baru)</label>
                            <select name="dosen_id" class="form-select rounded-lg">
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control rounded-lg" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">Gambar (Opsional)</label>
                            <input type="file" name="gambar" class="form-control rounded-lg" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light font-bold rounded-lg" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn font-bold rounded-lg px-4" 
                                style="background-color: #FACC15; color: #2d3748; border: none;">Simpan</button>
                    </div>
                </form>
            </div>
        </div> 
    </div>
@endsection