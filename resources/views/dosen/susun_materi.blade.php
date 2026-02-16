@extends('layouts.dosen')

@section('title', 'Susun: ' . $course->nama_mk)

@section('content')

{{-- HEADER: Tombol diperkecil (w-auto) --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 p-4 bg-white rounded-2xl shadow-sm border border-gray-100 gap-3">
    <div>
        <h4 class="font-bold text-gray-800 mb-0 text-lg md:text-xl"><i class="bi bi-layers-half text-blue-600 me-2"></i>{{ $course->nama_mk }}</h4>
        <p class="text-xs text-gray-400 mt-1 mb-0">Kelola urutan dan jenis materi secara dinamis.</p>
    </div>
    
    <div class="d-flex gap-2 w-100 w-md-auto">
        <form action="{{ url('/dosen/materi/ai-sort/'.$course->id) }}" method="POST" class="w-auto" onsubmit="return confirm('AI akan menyusun ulang TOTAL urutan. Lanjutkan?')">
            @csrf
            <button class="btn btn-outline-primary font-bold text-sm px-4 py-2 rounded-xl border-2 hover:bg-blue-50 transition">
                <i class="bi bi-stars"></i> Auto Sort AI
            </button>
        </form>
        
        <button class="btn btn-dark font-bold text-sm px-4 py-2 rounded-xl shadow-lg w-auto" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i> Tambah Materi
        </button>
    </div>
</div>

{{-- AREA PENDING: Tombol AI Smart Insert diperkecil --}}
@if($pending_materials->count() > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4 animate-fade-in-up">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3 gap-2">
            <div class="flex-grow-1">
                <h6 class="font-bold text-yellow-800 mb-1"><i class="bi bi-hourglass-split me-1"></i> Video Antrean AI</h6>
                <p class="text-xs text-yellow-600 mb-0">Video ini belum masuk urutan. Klik tombol untuk menyisipkannya.</p>
            </div>
            <form action="{{ url('/dosen/materi/ai-insert/'.$course->id) }}" method="POST" class="w-auto">
                @csrf
                <button class="btn btn-warning text-white font-bold text-sm shadow-sm rounded-xl px-4">
                    <i class="bi bi-magic me-1"></i> AI Smart Insert
                </button>
            </form>
        </div>
        <div class="d-flex gap-2 overflow-auto pb-2">
            @foreach($pending_materials as $pm)
                <span class="badge bg-white text-yellow-700 border border-yellow-200 p-2 rounded-lg text-[10px] whitespace-nowrap shadow-sm">{{ $pm->judul_materi }}</span>
            @endforeach
        </div>
    </div>
@endif

{{-- TABEL MATERI --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-gray-50 text-gray-400 text-[10px] uppercase font-bold tracking-widest">
                <tr>
                    <th class="ps-4" style="width: 60px;">Urutan</th>
                    <th>Judul & Deskripsi</th>
                    <th class="d-none d-md-table-cell">Tipe</th>
                    <th class="d-none d-md-table-cell">Info</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materials as $m)
                <tr>
                    <td class="ps-4">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 font-bold flex items-center justify-center text-sm border border-blue-100">
                            {{ $m->urutan }}
                        </div>
                    </td>
                    <td style="max-width: 200px;"> 
                        <h6 class="font-bold text-gray-800 mb-0 text-sm text-truncate">{{ $m->judul_materi }}</h6>
                        <div class="text-gray-400 text-xs mt-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $m->deskripsi_materi }}
                        </div>
                        <div class="d-md-none d-flex gap-2 mt-2 flex-wrap">
                            @if($m->kategori == 'quiz') <span class="badge bg-purple-50 text-purple-600 border border-purple-100 text-[9px] px-2">KUIS</span>
                            @else <span class="badge bg-blue-50 text-blue-600 border border-blue-100 text-[9px] px-2">VIDEO</span> @endif
                        </div>
                    </td>
                    <td class="d-none d-md-table-cell">
                        @if($m->kategori == 'quiz') <span class="badge bg-purple-50 text-purple-600 border border-purple-100 rounded-pill px-3">KUIS</span>
                        @else <span class="badge bg-blue-50 text-blue-600 border border-blue-100 rounded-pill px-3">VIDEO</span> @endif
                    </td>
                    <td class="d-none d-md-table-cell">
                        @if($m->kategori == 'quiz') <small class="text-purple-500 font-bold">Bank Soal</small>
                        @elseif($m->tipe_submission == 'file') <small class="text-green-600 font-bold"><i class="bi bi-google-drive"></i> G-Drive</small>
                        @elseif($m->tipe_submission == 'github') <small class="text-dark font-bold"><i class="bi bi-github"></i> GitHub</small>
                        @else <small class="text-gray-400">-</small> @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-light border text-blue-600 shadow-sm rounded-lg p-1 px-2" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $m->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ url('/dosen/materi/'.$m->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light border text-red-500 shadow-sm rounded-lg p-1 px-2"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL EDIT: LAYOUT KIRI (SETTINGS) - KANAN (DESKRIPSI) --}}
@foreach($materials as $m)
<div class="modal fade" id="modalEdit{{ $m->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered"> {{-- Pakai modal-xl biar lebar --}}
        <form action="{{ url('/dosen/materi/'.$m->id) }}" method="POST" enctype="multipart/form-data" class="modal-content rounded-2xl border-0 shadow-lg">
            @csrf @method('PUT')
            <div class="modal-header border-0 p-4 pb-2">
                <h5 class="font-bold">Edit Materi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-2">
                
                {{-- JUDUL & TIPE (FULL WIDTH) --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Judul Materi</label>
                        <input type="text" name="judul_materi" class="form-control fw-bold text-lg" value="{{ $m->judul_materi }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Tipe (Terkunci)</label>
                        <input type="text" class="form-control bg-gray-100 fw-bold uppercase" value="{{ $m->kategori }}" readonly>
                        <input type="hidden" name="kategori" value="{{ $m->kategori }}">
                    </div>
                </div>

                <div class="row g-4">
                    {{-- KOLOM KIRI: PENGATURAN TEKNIS --}}
                    <div class="col-md-7">
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 h-100">
                            @if($m->kategori == 'video')
                                <h6 class="text-xs font-bold text-gray-500 uppercase mb-3 border-bottom pb-2">Konfigurasi Video & Tugas</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase">Metode Pengumpulan</label>
                                        <select name="tipe_submission" class="form-select toggle-submission" data-id="{{ $m->id }}">
                                            <option value="none" {{ !$m->tipe_submission ? 'selected' : '' }}>Tanpa Tugas</option>
                                            <option value="file" {{ $m->tipe_submission == 'file' ? 'selected' : '' }}>Upload File (Drive)</option>
                                            <option value="github" {{ $m->tipe_submission == 'github' ? 'selected' : '' }}>Link GitHub</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-[10px] font-bold text-blue-600 uppercase">Link Folder G-Drive</label>
                                        <input type="url" name="link_drive" id="link-drive-{{ $m->id }}" 
                                               class="form-control {{ $m->tipe_submission == 'file' ? 'bg-white' : 'bg-gray-100' }}" 
                                               value="{{ $m->link_drive }}" 
                                               {{ $m->tipe_submission != 'file' ? 'disabled' : '' }}>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase">URL YouTube</label>
                                        <input type="url" name="video_url" class="form-control" value="{{ $m->video_url }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase">Update File Materi</label>
                                        <input type="file" name="file_lampiran" class="form-control">
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-patch-question-fill text-purple-600 text-4xl mb-2 d-block"></i>
                                    <h6 class="font-bold text-purple-800">Mode Kuis</h6>
                                    <p class="text-xs text-purple-600 mb-0 px-4">Pengaturan soal, jawaban, dan bobot nilai dilakukan secara terpisah di menu <strong>Bank Soal</strong>.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- KOLOM KANAN: DESKRIPSI (PANJANG) --}}
                    <div class="col-md-5">
                        <div class="d-flex flex-column h-100">
                            <label class="text-[10px] font-bold text-gray-400 uppercase mb-1">Deskripsi / Instruksi</label>
                            <textarea name="deskripsi_materi" class="form-control flex-grow-1 p-3" style="min-height: 250px; resize: none;">{{ $m->deskripsi_materi }}</textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-primary px-5 py-2 font-bold rounded-xl shadow-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- MODAL TAMBAH: LAYOUT KIRI (SETTINGS) - KANAN (DESKRIPSI) --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form action="{{ url('/dosen/materi') }}" method="POST" enctype="multipart/form-data" class="modal-content rounded-2xl border-0 shadow-lg">
            @csrf
            <input type="hidden" name="course_id" value="{{ $course->id }}">
            <div class="modal-header bg-blue-600 text-white border-0 p-4 pb-3">
                <h5 class="font-bold">Tambah Materi Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-3">
                
                {{-- JUDUL & TIPE --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Judul Materi</label>
                        <input type="text" name="judul_materi" class="form-control fw-bold text-lg" required placeholder="Contoh: Pengenalan Algoritma">
                    </div>
                    <div class="col-md-4">
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Tipe Konten</label>
                        <select name="kategori" class="form-select fw-bold" id="add-kategori">
                            <option value="video">Video Materi</option>
                            <option value="quiz">Kuis / Latihan</option>
                        </select>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- KOLOM KIRI: KONFIGURASI --}}
                    <div class="col-md-7">
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 h-100">
                            
                            {{-- FORM VIDEO (DEFAULT) --}}
                            <div id="add-video-fields">
                                <h6 class="text-xs font-bold text-blue-600 uppercase mb-3 border-bottom pb-2">Konfigurasi Video</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase">Tipe Tugas</label>
                                        <select name="tipe_submission" class="form-select" id="add-submission">
                                            <option value="none">Tanpa Tugas</option>
                                            <option value="file">Upload File (Drive)</option>
                                            <option value="github">Link GitHub</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-[10px] font-bold text-blue-600 uppercase">Link G-Drive Dosen</label>
                                        <input type="url" name="link_drive" id="add-link-drive" class="form-control bg-gray-100" placeholder="Link folder pengumpulan..." disabled>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase">URL YouTube</label>
                                        <input type="url" name="video_url" class="form-control" placeholder="https://youtube.com/...">
                                    </div>
                                    <div class="col-12">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase">File Materi</label>
                                        <input type="file" name="file_lampiran" class="form-control">
                                    </div>
                                </div>
                            </div>

                            {{-- FORM KUIS (HIDDEN) --}}
                            <div id="add-quiz-insert" class="d-none">
                                <h6 class="text-xs font-bold text-orange-600 uppercase mb-3 border-bottom pb-2">Konfigurasi Posisi Kuis</h6>
                                <div class="alert alert-warning border-0 d-flex align-items-center mb-3">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <span class="text-xs">Kuis disisipkan manual, Video disusun AI.</span>
                                </div>
                                <label class="text-[10px] font-bold text-orange-500 uppercase">Sisipkan Kuis Setelah...</label>
                                <select name="insert_after" class="form-select bg-orange-50 border-orange-200">
                                    <option value="">-- Paling Akhir --</option>
                                    <option value="start">-- Paling Awal (Urutan 1) --</option>
                                    @foreach($materials as $m)
                                        <option value="{{ $m->id }}">{{ $m->urutan }}. {{ $m->judul_materi }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>

                    {{-- KOLOM KANAN: DESKRIPSI (FULL HEIGHT) --}}
                    <div class="col-md-5">
                        <div class="d-flex flex-column h-100">
                            <label class="text-[10px] font-bold text-gray-400 uppercase mb-1">Deskripsi / Instruksi</label>
                            <textarea name="deskripsi_materi" class="form-control flex-grow-1 p-3" placeholder="Tulis deskripsi lengkap materi di sini..." style="min-height: 250px; resize: none;"></textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-primary px-5 py-2 font-bold rounded-xl shadow-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // LOGIKA TAMBAH
    $('#add-kategori').on('change', function() {
        if ($(this).val() === 'quiz') {
            $('#add-video-fields').addClass('d-none');
            $('#add-quiz-insert').removeClass('d-none');
        } else {
            $('#add-video-fields').removeClass('d-none');
            $('#add-quiz-insert').addClass('d-none');
        }
    });

    $('#add-submission').on('change', function() {
        let val = $(this).val();
        let input = $('#add-link-drive');
        if (val === 'file') input.prop('disabled', false).removeClass('bg-gray-100').addClass('bg-white');
        else input.prop('disabled', true).val('').addClass('bg-gray-100').removeClass('bg-white');
    });

    // LOGIKA EDIT
    $('.toggle-submission').on('change', function() {
        let id = $(this).data('id');
        let val = $(this).val();
        let input = $('#link-drive-' + id);
        if (val === 'file') input.prop('disabled', false).removeClass('bg-gray-100').addClass('bg-white');
        else input.prop('disabled', true).val('').addClass('bg-gray-100').removeClass('bg-white');
    });
});
</script>
@endpush