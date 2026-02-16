@extends('layouts.dosen')

@section('title', 'Penugasan & Nilai')

@section('content')

{{-- HEADER & STATS --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 p-4 bg-white rounded-2xl shadow-sm border border-gray-100 gap-3">
    <div>
        <h4 class="font-bold text-gray-800 mb-0 text-lg md:text-xl"><i class="bi bi-journal-check text-blue-600 me-2"></i>Penugasan</h4>
        <p class="text-xs text-gray-400 mt-1 mb-0">Kelola tugas mahasiswa, cek file masuk, dan beri nilai.</p>
    </div>
    
    {{-- STATISTIK MINI --}}
    <div class="d-flex gap-3 w-100 w-md-auto">
        <div class="bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 flex-grow-1 flex-md-grow-0 text-center text-md-start">
            <span class="d-block text-[10px] text-blue-500 uppercase font-bold">Total Tugas</span>
            <span class="fs-5 fw-bold text-blue-700">{{ $total_submissions }}</span>
        </div>
        <div class="bg-orange-50 px-4 py-2 rounded-xl border border-orange-100 flex-grow-1 flex-md-grow-0 text-center text-md-start">
            <span class="d-block text-[10px] text-orange-500 uppercase font-bold">Perlu Dinilai</span>
            <span class="fs-5 fw-bold text-orange-700">{{ $pending_grading }}</span>
        </div>
    </div>
</div>

{{-- FILTER COURSE --}}
<div class="mb-4">
    <form action="{{ url('/dosen/tugas') }}" method="GET">
        <div class="input-group shadow-sm rounded-xl overflow-hidden border-0">
            <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-filter text-gray-400"></i></span>
            <select name="course_id" class="form-select border-0 text-sm py-3" onchange="this.form.submit()">
                <option value="">Semua Mata Kuliah</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_mk }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

{{-- GRID TUGAS --}}
@if($assignments->count() > 0)
    <div class="row g-4">
        @foreach($assignments as $m)
        <div class="col-md-6 col-lg-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition h-100 d-flex flex-column relative overflow-hidden group">
                
                {{-- Badge Tipe --}}
                <div class="absolute top-0 right-0 p-3">
                    @if($m->tipe_submission == 'github')
                        <span class="badge bg-dark text-white rounded-lg shadow-sm"><i class="bi bi-github me-1"></i> GitHub</span>
                    @else
                        <span class="badge bg-green-500 text-white rounded-lg shadow-sm"><i class="bi bi-google-drive me-1"></i> File</span>
                    @endif
                </div>

                <div class="p-4 flex-grow-1">
                    <div class="mb-2">
                        <span class="text-[10px] font-bold text-blue-500 uppercase tracking-wider">{{ $m->course->nama_mk }}</span>
                    </div>
                    
                    <h5 class="font-bold text-gray-800 mb-2 line-clamp-2">{{ $m->judul_materi }}</h5>
                    <p class="text-xs text-gray-400 mb-4 line-clamp-2">{{ $m->deskripsi_materi }}</p>
                    
                    {{-- Progress Bar --}}
                    @php
                        $total_sub = $m->submissions->count();
                        $graded = $m->submissions->whereNotNull('nilai')->count();
                        $percent = $total_sub > 0 ? ($graded / $total_sub) * 100 : 0;
                    @endphp
                    
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <div class="d-flex justify-content-between text-[10px] fw-bold text-gray-500 mb-1">
                            <span>Progress Penilaian</span>
                            <span>{{ $graded }} / {{ $total_sub }} Mahasiswa</span>
                        </div>
                        <div class="progress h-1.5 rounded-full bg-gray-200">
                            <div class="progress-bar bg-blue-500 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="p-3 border-top bg-gray-50">
                    <button class="btn btn-primary w-100 rounded-xl font-bold text-sm py-2 shadow-sm" 
                            data-bs-toggle="modal" data-bs-target="#modalNilai{{ $m->id }}">
                        <i class="bi bi-pen me-1"></i> Periksa & Nilai
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5">
        <img src="https://img.freepik.com/free-vector/check-list-concept-illustration_114360-475.jpg" class="w-48 mx-auto opacity-50 grayscale" alt="Empty">
        <h6 class="text-gray-500 mt-3">Tidak ada tugas aktif.</h6>
        <p class="text-xs text-gray-400">Buat materi dengan tipe "Upload File" atau "GitHub" di menu Susun Materi.</p>
        <a href="{{ url('/dosen/materi') }}" class="btn btn-outline-primary btn-sm rounded-xl mt-2">Buat Tugas Baru</a>
    </div>
@endif

{{-- MODAL PENILAIAN (SPLIT LAYOUT) --}}
@foreach($assignments as $m)
<div class="modal fade" id="modalNilai{{ $m->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-2xl border-0 shadow-2xl h-[90vh]">
            
            <div class="modal-header border-bottom bg-white p-4">
                <div>
                    <h5 class="modal-title font-bold text-gray-800"><i class="bi bi-journal-check me-2 text-blue-600"></i>{{ $m->judul_materi }}</h5>
                    <p class="mb-0 text-xs text-gray-400">Total Pengumpulan: <strong class="text-blue-600">{{ $m->submissions->count() }}</strong></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0 bg-gray-50">
                <div class="row g-0 h-100">
                    
                    {{-- KIRI: DAFTAR MAHASISWA & FILE --}}
                    <div class="col-lg-8 border-end h-100 overflow-auto no-scrollbar custom-scroll">
                        <div class="p-4">
                            @if($m->submissions->count() > 0)
                                <div class="table-responsive bg-white rounded-xl shadow-sm border border-gray-100">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-gray-50 text-[10px] text-gray-400 uppercase font-bold">
                                            <tr>
                                                <th class="ps-4 py-3">Mahasiswa</th>
                                                <th>File / Link</th>
                                                <th>Nilai</th>
                                                <th class="text-end pe-4">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($m->submissions as $sub)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="{{ $sub->user->foto_profil ? asset('storage/profiles/'.$sub->user->foto_profil) : asset('images/default.png') }}" 
                                                             class="w-8 h-8 rounded-full border" onerror="this.src='https://ui-avatars.com/api/?name={{ $sub->user->nama_lengkap }}'">
                                                        <div>
                                                            <h6 class="text-xs font-bold text-gray-800 mb-0">{{ $sub->user->nama_lengkap }}</h6>
                                                            <small class="text-[9px] text-gray-400">{{ $sub->created_at->format('d M H:i') }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($m->tipe_submission == 'github')
                                                        <a href="{{ $sub->file_path }}" target="_blank" class="btn btn-dark btn-sm rounded-lg text-[10px] px-3">
                                                            <i class="bi bi-github me-1"></i> Repo
                                                        </a>
                                                    @else
                                                        <a href="{{ asset('storage/submissions/'.$sub->file_path) }}" target="_blank" class="btn btn-success btn-sm rounded-lg text-[10px] px-3">
                                                            <i class="bi bi-download me-1"></i> Unduh
                                                        </a>
                                                    @endif
                                                </td>
                                                <td style="width: 100px;">
                                                    {{-- FORM INPUT NILAI LANGSUNG DI TABEL --}}
                                                    <form action="{{ url('/dosen/nilai/'.$sub->id) }}" method="POST" id="form-nilai-{{ $sub->id }}">
                                                        @csrf @method('PUT')
                                                        <input type="number" name="nilai" class="form-control form-control-sm text-center fw-bold {{ $sub->nilai ? 'bg-green-50 border-green-200 text-green-700' : 'bg-gray-50' }}" 
                                                               value="{{ $sub->nilai }}" placeholder="0-100" min="0" max="100">
                                                    </form>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <button type="submit" form="form-nilai-{{ $sub->id }}" class="btn btn-light border btn-sm rounded-lg text-blue-600 shadow-sm" title="Simpan Nilai">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox text-gray-300 text-4xl mb-2"></i>
                                    <p class="text-xs text-gray-400">Belum ada mahasiswa yang mengumpulkan tugas ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- KANAN: INSTRUKSI & INFO --}}
                    <div class="col-lg-4 bg-white h-100 overflow-auto border-start">
                        <div class="p-4">
                            <div class="bg-blue-50 p-3 rounded-xl border border-blue-100 mb-4">
                                <h6 class="font-bold text-blue-700 text-sm mb-1">Informasi Tugas</h6>
                                <p class="text-xs text-blue-600 mb-0 line-clamp-3">{{ $m->deskripsi_materi }}</p>
                            </div>

                            @if($m->link_drive)
                            <div class="mb-4">
                                <label class="text-[10px] font-bold text-gray-400 uppercase mb-1">Folder Drive Dosen</label>
                                <a href="{{ $m->link_drive }}" target="_blank" class="d-flex align-items-center gap-2 p-2 rounded-xl border hover:bg-gray-50 transition text-decoration-none">
                                    <i class="bi bi-folder-fill text-yellow-400 fs-4"></i>
                                    <div>
                                        <span class="d-block text-xs font-bold text-gray-700">Buka Folder Penampungan</span>
                                        <span class="d-block text-[9px] text-gray-400">Google Drive</span>
                                    </div>
                                    <i class="bi bi-box-arrow-up-right ms-auto text-gray-400 text-xs"></i>
                                </a>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="text-[10px] font-bold text-gray-400 uppercase mb-2">Panduan Penilaian</label>
                                <ul class="list-unstyled text-xs text-gray-500 d-flex flex-column gap-2">
                                    <li class="d-flex align-items-start gap-2">
                                        <i class="bi bi-1-circle text-blue-500"></i>
                                        <span>Unduh file atau buka link GitHub mahasiswa.</span>
                                    </li>
                                    <li class="d-flex align-items-start gap-2">
                                        <i class="bi bi-2-circle text-blue-500"></i>
                                        <span>Periksa kelengkapan dan kesesuaian tugas.</span>
                                    </li>
                                    <li class="d-flex align-items-start gap-2">
                                        <i class="bi bi-3-circle text-blue-500"></i>
                                        <span>Input nilai (0-100) pada kolom nilai dan klik tombol centang (<i class="bi bi-check-lg"></i>) untuk menyimpan.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    .custom-scroll::-webkit-scrollbar { width: 4px; display: block; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>

@endsection