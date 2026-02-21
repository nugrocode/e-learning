@extends('layouts.dosen')

@section('title', 'Bank Soal & Kuis')

@section('content')

{{-- HEADER & FILTER --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 p-4 bg-white rounded-2xl shadow-sm border border-gray-100 gap-3">
    <div>
        {{-- Ikon diganti menjadi kuning --}}
        <h4 class="font-bold text-gray-800 mb-0 text-lg md:text-xl"><i class="bi bi-patch-question-fill text-yellow-500 me-2"></i>Bank Soal & Kuis</h4>
        <p class="text-xs text-gray-400 mt-1 mb-0">Kelola pertanyaan untuk materi bertipe kuis.</p>
    </div>
    
    <form action="{{ url('/dosen/kuis') }}" method="GET" class="w-100 w-md-auto">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 rounded-s-xl"><i class="bi bi-filter text-gray-400"></i></span>
            <select name="course_id" class="form-select border-start-0 rounded-e-xl text-sm" onchange="this.form.submit()">
                <option value="">Semua Mata Kuliah</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_mk }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

{{-- GRID KUIS --}}
@if($quizzes->count() > 0)
    <div class="row g-4">
        @foreach($quizzes as $q)
        <div class="col-md-6 col-lg-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition h-100 d-flex flex-column">
                <div class="p-4 flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        {{-- Badge diganti kuning --}}
                        <span class="badge bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-lg px-2 py-1 text-[10px] uppercase font-bold">
                            {{ $q->course->nama_mk }}
                        </span>
                        <div class="dropdown">
                            <button class="btn btn-link text-gray-300 p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-xl overflow-hidden">
                                <li><a class="dropdown-item text-xs py-2" href="{{ url('/dosen/materi') }}"><i class="bi bi-pencil me-2"></i>Edit Materi</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <h5 class="font-bold text-gray-800 mb-2 line-clamp-2">{{ $q->judul_materi }}</h5>
                    <p class="text-xs text-gray-400 mb-4 line-clamp-2">{{ $q->deskripsi_materi }}</p>
                    
                    <div class="d-flex align-items-center gap-3 text-xs text-gray-500 bg-gray-50 p-2 rounded-xl">
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-collection text-blue-500"></i> 
                            <span class="fw-bold text-gray-700">{{ $q->questions_count }}</span> Soal
                        </div>
                        <div class="vr opacity-25"></div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-clock text-orange-500"></i> Auto-Grade
                        </div>
                    </div>
                </div>
                
                <div class="p-3 border-top bg-gray-50 rounded-b-2xl">
                    {{-- Tombol Kelola diganti Kuning menyesuaikan Navbar --}}
                    <button class="btn w-100 rounded-xl font-bold text-sm py-2 shadow-sm text-gray-800 hover:opacity-80 transition" 
                            style="background-color: #ffc107; border: none;" 
                            data-bs-toggle="modal" data-bs-target="#modalSoal{{ $q->id }}">
                        <i class="bi bi-gear-wide-connected me-1"></i> Kelola Soal
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5">
        <img src="https://img.freepik.com/free-vector/no-data-concept-illustration_114360-536.jpg" class="w-48 mx-auto opacity-50 grayscale" alt="Empty">
        <h6 class="text-gray-500 mt-3">Belum ada materi bertipe Kuis.</h6>
        <p class="text-xs text-gray-400">Buat materi baru dengan tipe "Kuis" di menu Susun Materi.</p>
        <a href="{{ url('/dosen/materi') }}" class="btn btn-outline-warning text-yellow-700 hover:text-gray-800 btn-sm rounded-xl mt-2 font-bold">Ke Susun Materi</a>
    </div>
@endif

{{-- MODAL KELOLA SOAL (SPLIT LAYOUT: KIRI LIST, KANAN FORM) --}}
@foreach($quizzes as $q)
<div class="modal fade" id="modalSoal{{ $q->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-2xl border-0 shadow-2xl h-[90vh]">
            
            {{-- HEADER MODAL --}}
            <div class="modal-header border-bottom bg-white p-4">
                <div>
                    {{-- Ikon & Teks diganti kuning --}}
                    <h5 class="modal-title font-bold text-gray-800"><i class="bi bi-collection-play me-2 text-yellow-500"></i>{{ $q->judul_materi }}</h5>
                    <p class="mb-0 text-xs text-gray-400">Total Soal: <strong class="text-yellow-600">{{ $q->questions_count }}</strong></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0 bg-gray-50">
                <div class="row g-0 h-100">
                    
                    {{-- KOLOM KIRI: DAFTAR SOAL (SCROLLABLE) --}}
                    <div class="col-lg-7 border-end h-100 overflow-auto no-scrollbar custom-scroll">
                        <div class="p-4">
                            <h6 class="text-xs font-bold text-gray-500 uppercase mb-3 sticky top-0 bg-gray-50 z-10 py-2">Daftar Pertanyaan</h6>
                            
                            @if($q->questions->count() > 0)
                                <div class="d-flex flex-column gap-3">
                                    @foreach($q->questions as $soal)
                                    <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm group relative">
                                        <div class="d-flex justify-content-between gap-2">
                                            <div class="flex-grow-1">
                                                {{-- Nomor diganti kuning --}}
                                                <p class="font-bold text-gray-800 text-sm mb-2"><span class="text-yellow-600 me-1">#{{ $loop->iteration }}</span> {{ $soal->pertanyaan }}</p>
                                                <div class="d-grid grid-cols-2 gap-2">
                                                    @foreach(['a','b','c','d'] as $opt)
                                                        <div class="text-[11px] px-2 py-1 rounded border {{ $soal->jawaban_benar == $opt ? 'bg-green-50 border-green-200 text-green-700 font-bold' : 'bg-gray-50 text-gray-500 border-transparent' }}">
                                                            <span class="uppercase me-1">{{ $opt }}.</span> {{ $soal->{'opsi_'.$opt} }}
                                                            @if($soal->jawaban_benar == $opt) <i class="bi bi-check-circle-fill ms-1"></i> @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div>
                                                <form action="{{ url('/dosen/soal/'.$soal->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-light btn-sm text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-clipboard-x text-gray-300 text-4xl mb-2"></i>
                                    <p class="text-xs text-gray-400">Belum ada soal. Tambahkan di panel kanan 👉</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- KOLOM KANAN: FORM INPUT (STICKY) --}}
                    <div class="col-lg-5 bg-white h-100 overflow-auto">
                        <div class="p-4">
                            {{-- Info Banner diganti Kuning --}}
                            <div class="bg-yellow-50 p-3 rounded-xl mb-4 border border-yellow-200">
                                <h6 class="font-bold text-yellow-700 text-sm mb-1"><i class="bi bi-plus-circle me-1"></i> Tambah Soal Baru</h6>
                                <p class="text-[10px] text-yellow-600 mb-0">Isi pertanyaan dan opsi jawaban di bawah ini.</p>
                            </div>

                            <form action="{{ url('/dosen/soal') }}" method="POST">
                                @csrf
                                <input type="hidden" name="material_id" value="{{ $q->id }}">
                                
                                <div class="mb-3">
                                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1">Pertanyaan</label>
                                    {{-- Ring focus diganti kuning --}}
                                    <textarea name="pertanyaan" class="form-control rounded-xl bg-gray-50 border-0 focus:bg-white focus:ring-2 ring-yellow-300 transition" rows="3" required placeholder="Tulis pertanyaan..."></textarea>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-12"><label class="text-[10px] font-bold text-gray-400 uppercase">Opsi Jawaban</label></div>
                                    
                                    @foreach(['a', 'b', 'c', 'd'] as $opt)
                                    <div class="col-12">
                                        <div class="input-group">
                                            <span class="input-group-text bg-gray-100 border-0 rounded-s-xl text-xs font-bold uppercase text-gray-500 w-10 justify-center">{{ $opt }}</span>
                                            <input type="text" name="opsi_{{ $opt }}" class="form-control bg-gray-50 border-0 focus:bg-white text-sm" required placeholder="Jawaban {{ strtoupper($opt) }}">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="mb-4">
                                    <label class="text-[10px] font-bold text-green-600 uppercase mb-1">Kunci Jawaban</label>
                                    <select name="jawaban_benar" class="form-select rounded-xl bg-green-50 border-green-100 text-green-700 font-bold" required>
                                        <option value="a">A</option>
                                        <option value="b">B</option>
                                        <option value="c">C</option>
                                        <option value="d">D</option>
                                    </select>
                                </div>

                                {{-- Tombol Simpan diganti Kuning menyesuaikan Navbar --}}
                                <button type="submit" class="btn w-100 py-2 rounded-xl font-bold shadow-lg hover:shadow-xl transition text-gray-800 hover:opacity-80" style="background-color: #ffc107; border: none;">
                                    <i class="bi bi-save me-1"></i> Simpan Soal
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    /* Custom Scrollbar untuk bagian daftar soal */
    .custom-scroll::-webkit-scrollbar { width: 4px; display: block; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>

@endsection