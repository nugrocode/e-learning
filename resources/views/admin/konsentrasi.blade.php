@extends('layouts.admin')

@section('title', 'Master Data Prodi')

@section('content')
    {{-- HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="font-bold text-2xl text-gray-800">Program Studi / Konsentrasi</h2>
            <p class="text-gray-500 text-sm">Kelola data induk jurusan atau jalur belajar di sini.</p>
        </div>
        
        <button class="btn btn-primary bg-blue-900 border-0 rounded-lg font-bold shadow-sm hover:bg-blue-800 transition w-full md:w-auto py-2 px-4 text-sm" 
            data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-2"></i> Tambah Prodi
        </button>
    </div>

    {{-- GRID KONSENTRASI --}}
    <div class="row g-4">
        @forelse($konsentrasi as $item)
            <div class="col-12 col-md-6 col-lg-4">
                {{-- CARD UTAMA --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 d-flex flex-column">
                    
                    {{-- GAMBAR THUMBNAIL (FIX PATH STORAGE) --}}
                    <div class="relative h-48 bg-gray-100 overflow-hidden group">
                        @if($item->gambar)
                            <img src="{{ asset('storage/thumbnails/' . $item->gambar) }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                 onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                <i class="bi bi-image text-5xl opacity-50"></i>
                            </div>
                        @endif
                        
                        {{-- Overlay Gradient --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>

                        {{-- Badge Total MK --}}
                        <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-gray-800 shadow-sm">
                            {{ $item->total_mk }} Mata Kuliah
                        </div>
                    </div>

                    {{-- KONTEN CARD --}}
                    <div class="p-4 flex-grow-1 d-flex flex-column">
                        <h5 class="font-bold text-lg text-gray-800 mb-2">{{ $item->nama_konsentrasi }}</h5>
                        <p class="text-sm text-gray-500 line-clamp-3 mb-4 flex-grow-1 leading-relaxed">
                            {{ $item->deskripsi ?? 'Belum ada deskripsi untuk program studi ini.' }}
                        </p>

                        {{-- TOMBOL AKSI (SIMETRIS & RAPI) --}}
                        <div class="d-flex gap-2 pt-3 border-t mt-auto">
                            <button class="btn btn-sm btn-light text-blue-600 border border-blue-100 flex-grow-1 font-bold rounded-lg hover:bg-blue-50 transition" 
                                data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </button>
                            <form action="{{ url('/admin/konsentrasi/'.$item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus prodi ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light text-red-600 border border-red-100 font-bold rounded-lg px-3 hover:bg-red-50 transition" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL EDIT --}}
            <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-xl border-0 shadow-lg">
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title font-bold text-gray-800">Edit Program Studi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ url('/admin/konsentrasi/'.$item->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label text-xs font-bold text-gray-500 uppercase">Nama Konsentrasi</label>
                                    <input type="text" name="nama_konsentrasi" class="form-control" value="{{ $item->nama_konsentrasi }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-xs font-bold text-gray-500 uppercase">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="3">{{ $item->deskripsi }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-xs font-bold text-gray-500 uppercase">Ganti Thumbnail (Opsional)</label>
                                    <input type="file" name="gambar" class="form-control" accept="image/*">
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 pt-0">
                                <button type="submit" class="btn btn-primary bg-blue-900 border-0 w-100 font-bold py-2 rounded-lg">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-diagram-3 text-5xl text-gray-200 d-block mb-3"></i>
                <h5 class="font-bold text-gray-400">Belum ada Program Studi</h5>
            </div>
        @endforelse
    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-xl border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title font-bold text-gray-800">Tambah Prodi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ url('/admin/konsentrasi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">Nama Konsentrasi</label>
                            <input type="text" name="nama_konsentrasi" class="form-control" placeholder="Contoh: Teknik Informatika" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Penjelasan singkat..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">Upload Thumbnail</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="submit" class="btn btn-primary bg-blue-900 border-0 w-100 font-bold py-2 rounded-lg">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection