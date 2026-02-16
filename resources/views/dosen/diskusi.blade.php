@extends('layouts.dosen')

@section('title', 'Tanya Jawab Materi')

@section('content')

    {{-- 1. HEADER HALAMAN --}}
    <div class="row mb-4 animate-fade-in-up">
        <div class="col-12 col-md-8">
            <h2 class="font-bold text-2xl text-gray-800 mb-1">Ruang Diskusi & Tanya Jawab</h2>
            <p class="text-gray-500 text-sm m-0">Jawab pertanyaan mahasiswa terkait materi pembelajaran di sini.</p>
        </div>
        <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
             <div class="bg-white px-4 py-2 rounded-lg border shadow-sm d-inline-block text-start min-w-[150px]">
                <span class="d-block text-xl font-bold text-blue-600">{{ $discussions->count() }}</span>
                <span class="text-[10px] text-gray-400 uppercase font-bold">Total Utas</span>
            </div>
        </div>
    </div>

    {{-- 2. FILTER & PENCARIAN --}}
    <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 mb-4">
        <form action="" method="GET" class="d-flex flex-column flex-md-row gap-3 align-items-center">
            
            {{-- Filter Mata Kuliah --}}
            <div class="input-group w-full md:w-auto flex-grow-1">
                <span class="input-group-text bg-gray-50 border-end-0 text-gray-400"><i class="bi bi-funnel"></i></span>
                <select name="course_id" class="form-select border-start-0 bg-gray-50 text-sm focus:ring-0" onchange="this.form.submit()">
                    <option value="">Semua Mata Kuliah</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_mk }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Search Bar --}}
             <div class="input-group w-full md:w-auto flex-grow-1">
                <input type="text" name="q" class="form-control border-end-0 text-sm ps-3" placeholder="Cari isi pertanyaan atau nama mahasiswa..." value="{{ request('q') }}">
                <button class="btn btn-light border border-start-0 text-gray-400"><i class="bi bi-search"></i></button>
            </div>

            {{-- Reset --}}
            <a href="{{ url('/dosen/diskusi') }}" class="btn btn-light border text-gray-400" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
        </form>
    </div>

    {{-- 3. DAFTAR DISKUSI (FORUM STYLE) --}}
    <div class="d-flex flex-column gap-4">
        @forelse($discussions as $chat)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                
                {{-- A. Header Kartu: Info Materi & Waktu --}}
                <div class="bg-gray-50 px-4 py-2 border-bottom border-gray-100 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        {{-- Badge Mata Kuliah --}}
                        <span class="badge bg-white text-gray-600 border border-gray-200 rounded text-[10px] fw-bold shadow-sm">
                            {{ $chat->material->course->nama_mk ?? 'Umum' }}
                        </span>
                        <i class="bi bi-chevron-right text-gray-300 text-xs"></i>
                        {{-- Judul Materi --}}
                        <a href="{{ url('/dosen/materi?q='.$chat->material->judul_materi) }}" class="text-xs font-bold text-blue-600 text-decoration-none hover:underline truncate">
                            {{ $chat->material->judul_materi ?? 'Materi Dihapus' }}
                        </a>
                    </div>
                    <small class="text-gray-400 text-[10px] flex-shrink-0 ms-2">
                        <i class="bi bi-clock me-1"></i> {{ $chat->created_at->diffForHumans() }}
                    </small>
                </div>

                <div class="p-4">
                    {{-- B. Pertanyaan Mahasiswa --}}
                    <div class="d-flex gap-3 mb-4">
                        {{-- Foto Mahasiswa --}}
                         <img src="{{ $chat->user->foto_profil && $chat->user->foto_profil != 'default.png' ? asset('storage/profiles/' . $chat->user->foto_profil) : asset('images/logo_ukit.png') }}" 
                             class="w-10 h-10 rounded-full border border-gray-200 object-cover flex-shrink-0 shadow-sm"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($chat->user->nama_lengkap) }}'">
                        
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="font-bold text-gray-800 text-sm mb-1">
                                    {{ $chat->user->nama_lengkap }} 
                                    <span class="badge bg-gray-100 text-gray-500 border rounded-pill ms-1 text-[9px] font-normal">Mahasiswa</span>
                                </h6>
                            </div>
                            {{-- Bubble Chat Mahasiswa --}}
                            <div class="bg-gray-50 p-3 rounded-2xl rounded-tl-none border border-gray-100 text-sm text-gray-700 relative">
                                {{ $chat->isi }}
                            </div>
                        </div>
                    </div>

                    {{-- C. Balasan Dosen (Threaded) --}}
                    @foreach($chat->replies as $reply)
                         <div class="d-flex gap-3 mb-3 ms-5 ps-3 border-start border-2 border-gray-100">
                            <div class="flex-grow-1 text-end">
                                <h6 class="font-bold text-blue-600 text-sm mb-1">
                                    Anda <span class="badge bg-blue-100 text-blue-600 border border-blue-200 rounded-pill ms-1 text-[9px]">Dosen</span>
                                </h6>
                                {{-- Bubble Chat Dosen --}}
                                <div class="bg-blue-50 p-3 rounded-2xl rounded-tr-none border border-blue-100 text-sm text-gray-800 text-start d-inline-block shadow-sm">
                                    {{ $reply->isi }}
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1 me-1">{{ $reply->created_at->format('d M H:i') }}</div>
                            </div>
                            {{-- Foto Dosen --}}
                            <img src="{{ session('foto') && session('foto') != 'default.png' ? asset('storage/profiles/'.session('foto')) : asset('images/logo_ukit.png') }}" 
                                 class="w-8 h-8 rounded-full border border-blue-200 object-cover flex-shrink-0"
                                 onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                        </div>
                    @endforeach

                    {{-- D. Form Balas Cepat --}}
                    <div class="mt-4 pt-3 border-top border-gray-50 ms-0 ms-md-5">
                        <form action="{{ url('/dosen/proses-diskusi') }}" method="POST" class="d-flex gap-2 align-items-start">
                            @csrf
                            <input type="hidden" name="material_id" value="{{ $chat->material_id }}">
                            <input type="hidden" name="parent_id" value="{{ $chat->id }}">
                            
                            {{-- Foto Mini Dosen (Desktop Only) --}}
                            <img src="{{ session('foto') && session('foto') != 'default.png' ? asset('storage/profiles/'.session('foto')) : asset('images/logo_ukit.png') }}" 
                                 class="w-8 h-8 rounded-full border object-cover d-none d-md-block opacity-75"
                                 onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                            
                            <div class="flex-grow-1 relative">
                                <textarea name="isi" class="form-control text-sm bg-gray-50 border-gray-200 focus:bg-white focus:border-blue-300 focus:ring-4 focus:ring-blue-100 transition rounded-xl py-2" rows="1" placeholder="Tulis balasan untuk mahasiswa ini..." required style="min-height: 42px;"></textarea>
                                <button type="submit" class="btn btn-primary btn-sm absolute right-2 top-1.5 rounded-lg p-1 px-3 fw-bold text-xs shadow-sm hover:scale-105 transition">
                                    <i class="bi bi-send-fill"></i> Kirim
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        @empty
             {{-- EMPTY STATE --}}
             <div class="col-12 text-center py-5">
                <div class="d-inline-block p-5 rounded-full bg-gray-50 mb-3 border border-dashed border-gray-200">
                    <i class="bi bi-chat-square-text text-4xl text-gray-300"></i>
                </div>
                <h6 class="font-bold text-gray-600">Hening Sekali...</h6>
                <p class="text-gray-400 text-sm">Belum ada mahasiswa yang mengajukan pertanyaan pada materi Anda.</p>
            </div>
        @endforelse
    </div>
    
    {{-- Pagination (Optional) --}}
    <div class="mt-5 d-flex justify-content-center">
        {{-- $discussions->links() --}} 
    </div>

@endsection