@extends('layouts.dosen')

@section('title', $course->nama_mk)

@section('content')

    {{-- 1. HEADER KELAS (Ringkas) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4 relative overflow-hidden">
        <div class="d-flex justify-content-between align-items-center relative z-10">
            <div>
                <a href="{{ url('/dosen/kelas') }}" class="text-gray-400 text-sm mb-1 d-inline-block hover:text-blue-600">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kelas
                </a>
                <h1 class="font-bold text-2xl text-gray-800 mb-1">{{ $course->nama_mk }}</h1>
                
                {{-- Statistik Ringkas --}}
                <div class="d-flex gap-4 text-xs font-bold text-gray-500 mt-2 uppercase tracking-wide">
                    <span><i class="bi bi-collection-play me-1 text-blue-500"></i> {{ $total_materi }} Materi</span>
                    <span class="border-start ps-4"><i class="bi bi-people me-1 text-green-500"></i> {{ $total_mahasiswa }} Mahasiswa</span>
                    <span class="border-start ps-4"><i class="bi bi-chat-dots me-1 text-purple-500"></i> {{ $total_diskusi }} Diskusi</span>
                </div>
            </div>
            
            {{-- Tombol Utama --}}
            <button class="btn btn-dark rounded-lg font-bold shadow-md px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahMateri">
                <i class="bi bi-plus-lg me-1"></i> Tambah Materi
            </button>
        </div>
        {{-- Dekorasi Background Halus --}}
        <div class="absolute right-0 top-0 w-64 h-64 bg-gray-50 rounded-full mix-blend-multiply filter blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
    </div>

    {{-- 2. LIST MATERI (LANGSUNG TAMPIL TANPA TAB) --}}
    @if($materials->isEmpty())
        <div class="text-center py-5 bg-white rounded-xl border border-dashed border-gray-300">
            <div class="mb-3 bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto text-gray-300">
                <i class="bi bi-folder2-open text-3xl"></i>
            </div>
            <h6 class="text-gray-600 font-bold mb-1">Belum ada materi</h6>
            <p class="text-xs text-gray-400">Silakan tambahkan materi pertemuan pertama Anda.</p>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($materials as $m)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition group">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex gap-3">
                            {{-- Icon Tipe Konten --}}
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 {{ $m->kategori == 'quiz' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' }}">
                                <i class="bi {{ $m->kategori == 'quiz' ? 'bi-puzzle-fill' : 'bi-play-fill' }} text-2xl"></i>
                            </div>
                            
                            {{-- Detail Materi --}}
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-gray-100 text-gray-500 text-[10px] border border-gray-200">#{{ $m->urutan }}</span>
                                    <h6 class="font-bold text-gray-800 mb-0">{{ $m->judul_materi }}</h6>
                                </div>
                                
                                <p class="text-xs text-gray-500 line-clamp-1 mb-2">{{ $m->deskripsi_materi }}</p>
                                
                                {{-- Badges Info --}}
                                <div class="d-flex gap-2">
                                    @if($m->video_url)
                                        <a href="{{ $m->video_url }}" target="_blank" class="badge bg-red-50 text-red-600 border border-red-100 text-[10px] no-underline hover:bg-red-100">
                                            <i class="bi bi-youtube me-1"></i> Video
                                        </a>
                                    @endif
                                    
                                    @if($m->tipe_submission == 'file')
                                        <span class="badge bg-orange-50 text-orange-600 border border-orange-100 text-[10px]">
                                            <i class="bi bi-upload me-1"></i> Tugas File
                                        </span>
                                        @if($m->link_drive)
                                            <a href="{{ $m->link_drive }}" target="_blank" class="badge bg-blue-50 text-blue-600 border border-blue-100 text-[10px] no-underline hover:bg-blue-100">
                                                <i class="bi bi-google-drive me-1"></i> Drive
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        {{-- Dropdown Aksi --}}
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 text-sm rounded-lg p-1">
                                <li>
                                    <a class="dropdown-item rounded px-3 py-2 font-medium text-gray-600 hover:bg-gray-50" href="#" data-bs-toggle="modal" data-bs-target="#modalEditMateri{{ $m->id }}">
                                        <i class="bi bi-pencil me-2 text-blue-500"></i> Edit Materi
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ url('/dosen/materi/'.$m->id) }}" method="POST" onsubmit="return confirm('Yakin hapus materi ini?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item rounded px-3 py-2 font-medium text-red-500 hover:bg-red-50">
                                            <i class="bi bi-trash me-2"></i> Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                @include('dosen.modal_edit_materi', ['m' => $m])
            @endforeach
        </div>
    @endif

   
    <div class="modal fade" id="modalTambahMateri" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ url('/dosen/materi') }}" method="POST" enctype="multipart/form-data" class="modal-content rounded-xl border-0 shadow-lg">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-bold">Tambah Materi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-xs font-bold uppercase text-gray-500">Judul Materi</label>
                        <input type="text" name="judul_materi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs font-bold uppercase text-gray-500">Deskripsi</label>
                        <textarea name="deskripsi_materi" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-xs font-bold uppercase text-gray-500">Tipe Konten</label>
                            <select name="kategori" class="form-select">
                                <option value="video">Video Materi</option>
                                <option value="quiz">Kuis / Latihan</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-xs font-bold uppercase text-gray-500">Tugas (Submission)</label>
                            <select name="tipe_submission" id="tipe_submission_add" class="form-select" onchange="toggleDriveInput('add')">
                                <option value="none">Tidak Ada</option>
                                <option value="file">Upload File</option>
                            </select>
                        </div>
                    </div>

                    
                    <div class="mb-3" id="drive_input_add" style="display: none;">
                        <label class="form-label text-xs font-bold uppercase text-blue-600">
                            <i class="bi bi-google-drive"></i> Link Folder G-Drive (Pengumpulan)
                        </label>
                        <input type="url" name="link_drive" class="form-control bg-blue-50 border-blue-200" placeholder="Paste link folder Google Drive disini...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs font-bold uppercase text-gray-500">Link Video (YouTube)</label>
                        <input type="url" name="video_url" class="form-control" placeholder="https://youtube.com/...">
                    </div>
                     <div class="mb-3">
                        <label class="form-label text-xs font-bold uppercase text-gray-500">File Lampiran</label>
                        <input type="file" name="file_lampiran" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-dark w-100 font-bold">Simpan Materi</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function toggleDriveInput(mode) {
        let selectId = mode === 'add' ? 'tipe_submission_add' : 'tipe_submission_edit';
        let divId = mode === 'add' ? 'drive_input_add' : 'drive_input_edit';
        
        let tipe = document.getElementById(selectId).value;
        let container = document.getElementById(divId);
        
        if (container) {
            container.style.display = (tipe === 'file') ? 'block' : 'none';
        }
    }
</script>
@endpush