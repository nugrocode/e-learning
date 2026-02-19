@extends('layouts.dosen')

@section('title', 'Forum Diskusi')

@section('content')

<style>
    /* --- CUSTOM CSS FOR CHAT UI --- */
    .chat-container { display: flex; gap: 12px; margin-bottom: 24px; }
    
    /* Avatar Styling */
    .chat-avatar { 
        width: 48px; height: 48px; 
        border-radius: 50%; object-fit: cover; 
        flex-shrink: 0; 
        border: 2px solid #fff; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
    }

    /* Bubble Styles */
    .chat-bubble {
        position: relative;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 0.95rem;
        line-height: 1.5;
        max-width: 85%;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    /* Student Bubble (Left) */
    .chat-bubble-student {
        background-color: #f8f9fa;
        border: 1px solid #edf2f7;
        border-top-left-radius: 0;
        color: #2d3748;
    }

    /* Dosen Bubble (Right) */
    .chat-dosen-wrapper { 
        display: flex; 
        justify-content: flex-end; 
        margin-top: 8px;
        margin-left: auto;
        gap: 10px;
    }
    .chat-bubble-dosen {
        background-color: #ebf8ff; /* Light Blue Fixed */
        border: 1px solid #bee3f8;
        border-top-right-radius: 0;
        color: #2c5282;
        text-align: left;
    }

    /* Typography */
    .sender-name { 
        font-size: 0.85rem; 
        font-weight: 700; 
        margin-bottom: 4px; 
        display: flex; /* Fix alignment */
        align-items: center;
        gap: 6px;
        color: #4a5568;
    }
    .time-stamp {
        font-size: 0.7rem;
        color: #a0aec0;
        margin-top: 4px;
        display: block;
        text-align: right;
    }

    /* FIX BADGE COLORS MANUAL (Agar tidak tertimpa) */
    .badge-mk-fix {
        background-color: #ebf8ff !important; /* Biru Muda */
        color: #3182ce !important; /* Biru Tua */
        border: 1px solid #bee3f8;
    }
    .badge-role-fix {
        background-color: #edf2f7 !important; /* Abu Muda */
        color: #718096 !important; /* Abu Tua */
        border: 1px solid #e2e8f0;
        font-size: 0.65rem;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
        text-transform: uppercase;
    }

    /* Mobile Adjustments */
    @media (max-width: 576px) {
        .chat-avatar { width: 36px; height: 36px; }
        .chat-bubble { font-size: 0.85rem; padding: 10px 14px; }
        .sender-name { font-size: 0.75rem; }
    }
</style>

<div class="container-fluid px-0">
    
    {{-- 1. HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">Forum Diskusi</h4>
            <p class="text-muted small m-0">Interaksi tanya jawab materi pembelajaran.</p>
        </div>
        
        <div class="d-none d-md-block">
            <span class="badge bg-white text-dark border shadow-sm py-2 px-3 rounded-pill">
                <i class="bi bi-chat-dots-fill text-primary me-2"></i> {{ $discussions->count() }} Pertanyaan
            </span>
        </div>
    </div>

    {{-- 2. FILTER SEARCH --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-2">
            <form action="" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-4 border-end-md">
                    <select name="course_id" class="form-select border-0 shadow-none py-2 text-secondary fw-bold" style="font-size: 0.9rem;" onchange="this.form.submit()">
                        <option value="">Semua Mata Kuliah</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_mk }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control border-0 shadow-none text-dark" placeholder="Cari pertanyaan mahasiswa..." value="{{ request('q') }}">
                        <button class="btn btn-primary rounded-pill px-4 ms-2 fw-bold shadow-sm btn-sm my-1">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. LIST DISKUSI --}}
    <div class="d-flex flex-column gap-4">
        @forelse($discussions as $chat)
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                
                {{-- HEADER KONTEKS (FIX WARNA) --}}
                <div class="px-4 py-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        {{-- Badge Mata Kuliah dengan Class Fix --}}
                        <span class="badge badge-mk-fix rounded-pill px-3 py-1 fw-bold text-uppercase" style="font-size: 0.7rem;">
                            {{ $chat->material->course->nama_mk ?? 'Umum' }}
                        </span>
                        <i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;"></i>
                        <a href="{{ url('/dosen/materi/'.$chat->material->course_id) }}" class="text-dark fw-bold text-decoration-none text-truncate small" style="max-width: 200px;">
                            {{ $chat->material->judul_materi }}
                        </a>
                    </div>
                    <small class="text-muted fw-medium" style="font-size: 0.75rem;">
                        {{ $chat->created_at->diffForHumans() }}
                    </small>
                </div>

                <div class="card-body p-3 p-md-4">
                    
                    {{-- A. PERTANYAAN MAHASISWA (KIRI) --}}
                    <div class="chat-container">
                        <img src="{{ $chat->user->foto_profil ? asset('storage/profiles/'.$chat->user->foto_profil) : asset('images/default.png') }}" 
                             class="chat-avatar" 
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($chat->user->nama_lengkap) }}&background=random'">
                        
                        <div class="flex-grow-1">
                            <div class="sender-name">
                                {{ $chat->user->nama_lengkap }} 
                                {{-- Badge Role dengan Class Fix --}}
                                <span class="badge-role-fix">Mahasiswa</span>
                            </div>
                            <div class="chat-bubble chat-bubble-student">
                                {!! nl2br(e($chat->message)) !!}
                            </div>
                        </div>
                    </div>

                    {{-- B. BALASAN DOSEN (KANAN) --}}
                    @foreach($chat->replies as $reply)
                        <div class="chat-dosen-wrapper w-100 w-md-75">
                            <div class="d-flex flex-column align-items-end flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    {{-- Tombol Hapus --}}
                                    <form action="{{ url('/dosen/diskusi/'.$reply->id) }}" method="POST" onsubmit="return confirm('Hapus balasan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link p-0 text-danger opacity-25 hover:opacity-100 transition" style="font-size: 0.8rem;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <span class="sender-name text-primary mb-0">Anda</span>
                                </div>
                                
                                <div class="chat-bubble chat-bubble-dosen">
                                    {!! nl2br(e($reply->message)) !!}
                                    <span class="time-stamp">{{ $reply->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                            
                            <img src="{{ session('foto') ? asset('storage/profiles/'.session('foto')) : asset('images/default.png') }}" 
                                 class="chat-avatar ms-1" style="width: 38px; height: 38px;"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(session('nama', 'Dosen')) }}'">
                        </div>
                    @endforeach

                </div>

                {{-- C. FORM BALASAN --}}
                <div class="card-footer bg-white p-3 border-top">
                    <form action="{{ url('/dosen/proses-diskusi') }}" method="POST" class="d-flex align-items-end gap-2">
                        @csrf
                        <input type="hidden" name="material_id" value="{{ $chat->material_id }}">
                        <input type="hidden" name="parent_id" value="{{ $chat->id }}">
                        <input type="hidden" name="course_id" value="{{ $chat->material->course_id }}">
                        
                        <div class="flex-grow-1">
                            <textarea name="message" class="form-control bg-light border-0 px-3 py-2 rounded-3" 
                                      rows="1" placeholder="Tulis balasan..." 
                                      style="resize: none; font-size: 0.9rem; min-height: 42px;" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                            <i class="bi bi-send-fill fs-6"></i>
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-chat-square-quote text-secondary fs-1 opacity-25"></i>
                </div>
                <h6 class="fw-bold text-secondary">Belum Ada Diskusi</h6>
                <p class="text-muted small">Mahasiswa belum mengajukan pertanyaan apapun.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection