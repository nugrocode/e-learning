@extends('layouts.admin')

@section('title', 'Kelola Kurikulum')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="font-bold text-2xl text-gray-800">Program Studi / Konsentrasi</h2>
            <p class="text-gray-500 text-sm">Kelola data induk jurusan atau jalur belajar di sini.</p>
        </div>

        <button class="btn fw-bold text-sm px-4 shadow-sm w-full md:w-auto text-nowrap" 
                style="background-color: #FACC15; color: #2d3748; border: none;" 
                data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Konsentrasi
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-lg d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-xl"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-lg d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-xl"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        @forelse($konsentrasi as $item)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 d-flex flex-column hover:shadow-md transition-shadow">
                    
                    <div class="relative h-40 bg-gray-100 overflow-hidden">
                        @if($item->gambar)
                            <img src="{{ asset('storage/thumbnails/' . $item->gambar) }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                <i class="bi bi-diagram-3 text-5xl opacity-50"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>
                        <div class="absolute bottom-3 right-3">
                            <span class="bg-white/90 backdrop-blur-sm text-xs font-bold px-2 py-1 rounded shadow-sm text-gray-800">
                                {{ $item->total_mk }} Mata Kuliah
                            </span>
                        </div>
                    </div>

                    <div class="p-4 flex-grow-1 d-flex flex-column">
                        <h5 class="font-bold text-lg text-gray-800 mb-2">{{ $item->nama_konsentrasi }}</h5>
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4 leading-relaxed">
                            {{ $item->deskripsi ?? 'Kelola struktur pembelajaran untuk prodi ini.' }}
                        </p>

                        <div class="mt-auto pt-3 d-flex gap-2">
                            <button class="btn btn-outline-primary flex-grow-1 font-bold text-sm py-2 rounded-lg" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </button>
                            <form action="{{ url('/admin/konsentrasi/' . $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus konsentrasi ini? Semua data terkait akan ikut terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger font-bold text-sm py-2 rounded-lg px-3">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow">
                        <div class="modal-header py-3 border-bottom-0" style="background-color: #2d3748;">
                            <h6 class="modal-title fw-bold text-sm" style="color: #FACC15;">Edit Konsentrasi</h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ url('/admin/konsentrasi/' . $item->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label text-secondary text-[10px] text-uppercase fw-bold">Nama Konsentrasi</label>
                                    <input type="text" name="nama_konsentrasi" class="form-control text-sm rounded-lg" value="{{ $item->nama_konsentrasi }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-secondary text-[10px] text-uppercase fw-bold">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control text-sm rounded-lg" rows="3">{{ $item->deskripsi }}</textarea>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label text-secondary text-[10px] text-uppercase fw-bold">Gambar Thumbnail (Opsional)</label>
                                    <input type="file" name="gambar" class="form-control text-sm rounded-lg" accept="image/*">
                                </div>
                            </div>
                            <div class="modal-footer py-2 border-top-0">
                                <button type="submit" class="btn w-100 font-bold py-2 text-sm rounded-lg shadow-sm" 
                                        style="background-color: #FACC15; color: #2d3748; border: none;">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-exclamation-circle text-4xl text-gray-200 d-block mb-3"></i>
                <h5 class="text-gray-400 font-bold">Belum ada Prodi</h5>
            </div>
        @endforelse
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header py-3 border-bottom-0" style="background-color: #2d3748;">
                    <h6 class="modal-title fw-bold text-sm" style="color: #FACC15;"><i class="bi bi-plus-lg me-2"></i>Tambah Konsentrasi Baru</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ url('/admin/konsentrasi') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-secondary text-[10px] text-uppercase fw-bold">Nama Konsentrasi</label>
                            <input type="text" name="nama_konsentrasi" class="form-control text-sm rounded-lg" placeholder="Contoh: Software Development" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary text-[10px] text-uppercase fw-bold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control text-sm rounded-lg" rows="3" placeholder="Deskripsi singkat konsentrasi..."></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-secondary text-[10px] text-uppercase fw-bold">Gambar Thumbnail (Opsional)</label>
                            <input type="file" name="gambar" class="form-control text-sm rounded-lg" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer py-2 border-top-0">
                        <button type="submit" class="btn w-100 font-bold py-2 text-sm rounded-lg shadow-sm" 
                                style="background-color: #FACC15; color: #2d3748; border: none;">Simpan Konsentrasi Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection