@extends('layouts.dosen')

@section('title', 'Forum Diskusi')

@section('content')

<style>
    /* Styling Avatar responsif */
    .comment-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0; }
    .reply-avatar { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0; }
    
    @media (min-width: 768px) {
        .comment-avatar { width: 40px; height: 40px; }
        .reply-avatar { width: 32px; height: 32px; }
    }
    
    .btn-action { 
        font-size: 0.75rem; 
        font-weight: 600; 
        color: #64748b; 
        background: none; 
        border: none; 
        padding: 0; 
        transition: all 0.2s ease; 
        display: flex;
        align-items: center;
        gap: 4px;
    }
    @media (min-width: 768px) { .btn-action { font-size: 0.8rem; } }
    .btn-action:hover { color: #2563eb; }
    .btn-action.text-danger:hover { color: #dc2626; }
    
    .reply-thread { 
        margin-left: 12px; 
        padding-left: 12px; 
        margin-top: 12px; 
        border-left: 2px solid #f1f5f9;
    }
    @media (min-width: 768px) {
        .reply-thread { margin-left: 20px; padding-left: 16px; }
    }
    
    /* Layout Input Reply: Stack di HP, Row di Desktop */
    .input-reply-wrapper {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 12px;
        background: #f8fafc;
        padding: 10px;
        border-radius: 8px;
    }
    @media (min-width: 768px) {
        .input-reply-wrapper {
            flex-direction: row;
            align-items: flex-start;
            background: transparent;
            padding: 0;
            gap: 12px;
        }
    }

    .input-reply { 
        background-color: transparent; 
        border: none; 
        border-bottom: 1px solid #cbd5e1; 
        border-radius: 0; 
        box-shadow: none !important; 
        font-size: 0.85rem; 
        padding: 6px 0; 
        transition: border-color 0.2s; 
        resize: none;
        overflow: hidden;
        min-height: 32px;
        width: 100%;
    }
    @media (min-width: 768px) { .input-reply { font-size: 0.9rem; } }
    .input-reply:focus { border-bottom-color: #2563eb; background: transparent; }
    
    .comment-card {
        transition: background-color 0.2s ease;
        padding: 12px 8px;
        border-radius: 12px;
    }
    @media (min-width: 768px) { .comment-card { padding: 16px; } }
    .comment-card:hover { background-color: #f8fafc; }
    
    .badge-role {
        font-size: 0.55rem;
        padding: 2px 5px;
        border-radius: 4px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    @media (min-width: 768px) { .badge-role { font-size: 0.6rem; padding: 2px 6px; } }
</style>

<div class="container-fluid px-0">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2 md:gap-3">
        <div>
            <h4 class="fw-bold text-gray-800 mb-1 text-lg md:text-xl">Forum Diskusi</h4>
            <p class="text-gray-500 text-xs md:text-sm m-0">Pusat interaksi tanya jawab dengan mahasiswa.</p>
        </div>
        <div>
            <span class="badge bg-white text-gray-700 border shadow-sm py-2 px-3 rounded-pill text-xs">
                <i class="bi bi-chat-dots-fill text-blue-600 me-2"></i> {{ $discussions->count() }} Pertanyaan
            </span>
        </div>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-xl mb-4 p-2">
        <form action="" method="GET" class="row g-2 align-items-center m-0">
            <div class="col-12 col-md-4 border-end-md border-gray-200">
                <select name="course_id" class="form-select border-0 shadow-none py-2 text-gray-600 text-xs md:text-sm font-bold bg-transparent" onchange="this.form.submit()">
                    <option value="">Semua Mata Kuliah</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_mk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-gray-400 ps-2 md:ps-3"><i class="bi bi-search text-xs md:text-base"></i></span>
                    <input type="text" name="q" class="form-control border-0 shadow-none text-xs md:text-sm bg-transparent" placeholder="Cari pertanyaan mahasiswa..." value="{{ request('q') }}">
                    <button class="btn btn-primary rounded-pill px-3 md:px-4 ms-2 font-bold text-xs md:text-sm my-1 shadow-sm">Cari</button>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-2 md:p-5">
        @forelse($discussions as $chat)
            <div class="comment-card {{ !$loop->last ? 'border-bottom border-gray-100 mb-2 pb-3 md:pb-4' : '' }}" id="chat-card-{{ $chat->id }}">
                <div class="d-flex gap-2 md:gap-3">
                    
                    <img src="{{ $chat->user->foto_profil && $chat->user->foto_profil != 'default.png' ? asset('storage/profiles/'.$chat->user->foto_profil) : asset('images/logo_ukit.png') }}" 
                         class="comment-avatar flex-shrink-0" 
                         onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                    
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-start flex-column md:flex-row md:align-items-center gap-1 md:gap-2 mb-1">
                            <div class="d-flex align-items-center flex-wrap gap-1 md:gap-2">
                                <span class="font-bold text-gray-900 text-xs md:text-sm">
                                    {{ $chat->user->nama_lengkap }}
                                </span>
                                <span class="badge-role bg-gray-100 text-gray-600 border border-gray-200">MHS</span>

                                @if($chat->replies->count() > 0)
                                    <span class="badge-role bg-green-50 text-green-600 border border-green-200"><i class="bi bi-check-all"></i> DIBALAS</span>
                                @else
                                    <span class="badge-role bg-red-50 text-red-500 border border-red-200"><i class="bi bi-exclamation-circle"></i> BLUM DIBALAS</span>
                                @endif
                            </div>
                            <span class="text-[9px] md:text-[11px] text-gray-400 ms-0 md:ms-auto"><i class="bi bi-clock me-1"></i>{{ $chat->created_at->diffForHumans() }}</span>
                        </div>

                        <div class="d-flex align-items-center flex-wrap gap-1 mb-2 bg-gray-50 p-1 md:p-2 rounded border border-gray-100 w-fit mt-1">
                            <span class="text-[9px] md:text-[10px] font-bold text-blue-600 uppercase tracking-wider">
                                {{ Str::limit($chat->material->course->nama_mk ?? 'Umum', 15) }}
                            </span>
                            <i class="bi bi-chevron-right text-[9px] md:text-[10px] text-gray-400"></i>
                            <a href="{{ url('/dosen/materi/'.$chat->material->course_id) }}" class="text-[10px] md:text-[11px] text-gray-600 hover:text-blue-600 text-decoration-none text-truncate" style="max-width: 180px;">
                                {{ $chat->material->judul_materi }}
                            </a>
                        </div>

                        <div class="text-xs md:text-sm text-gray-800 mb-2 leading-relaxed" style="word-break: break-word;">
                            {!! nl2br(e($chat->message)) !!}
                        </div>

                        <div class="d-flex align-items-center gap-3 md:gap-4 mb-2">
                            <button type="button" class="btn-action" onclick="toggleForm('{{ $chat->id }}')">
                                <i class="bi bi-reply"></i> Balas
                            </button>
                            
                            @if($chat->replies->count() > 0)
                                <button type="button" class="btn-action text-blue-600 bg-blue-50 px-2 py-1 md:px-3 md:py-1 rounded-pill" onclick="toggleReplies('{{ $chat->id }}')">
                                    <i class="bi bi-caret-down-fill text-[9px] md:text-[10px]" id="icon-{{ $chat->id }}"></i> 
                                    <span id="text-{{ $chat->id }}">{{ $chat->replies->count() }} balasan</span>
                                </button>
                            @endif
                        </div>

                        {{-- Form Balasan Responsif --}}
                        <div id="replyForm-{{ $chat->id }}" style="display: none;" class="input-reply-wrapper">
                            <div class="d-none d-md-block">
                                <img src="{{ session('foto') && session('foto') != 'default.png' ? asset('storage/profiles/'.session('foto')) : asset('images/logo_ukit.png') }}" 
                                     class="reply-avatar flex-shrink-0" onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                            </div>
                            <form action="{{ url('/dosen/proses-diskusi') }}" method="POST" class="flex-grow-1 w-100 form-ajax" data-chat-id="{{ $chat->id }}">
                                @csrf
                                <input type="hidden" name="material_id" value="{{ $chat->material_id }}">
                                <input type="hidden" name="parent_id" value="{{ $chat->id }}">
                                <input type="hidden" name="course_id" value="{{ $chat->material->course_id }}">
                                
                                <textarea name="message" class="form-control input-reply" rows="1" placeholder="Ketik balasan Anda..." required oninput="autoResize(this)"></textarea>
                                <div class="d-flex gap-2 justify-content-end mt-2">
                                    <button type="button" class="btn btn-light btn-sm rounded-pill font-bold px-3 text-gray-600 hover:bg-gray-200 text-xs md:text-sm" onclick="toggleForm('{{ $chat->id }}')">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill font-bold px-4 shadow-sm text-xs md:text-sm d-flex align-items-center"><i class="bi bi-send-fill me-1 d-md-none"></i> Kirim</button>
                                </div>
                            </form>
                        </div>

                        {{-- Balasan / Thread --}}
                        @if($chat->replies->count() > 0)
                            <div id="replies-{{ $chat->id }}" class="reply-thread" style="display: none;">
                                @foreach($chat->replies as $reply)
                                    <div class="d-flex gap-2 md:gap-3 mb-3 group">
                                        <img src="{{ session('foto') && session('foto') != 'default.png' ? asset('storage/profiles/'.session('foto')) : asset('images/logo_ukit.png') }}" 
                                             class="reply-avatar flex-shrink-0" 
                                             onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                                        
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center flex-wrap gap-1 md:gap-2 mb-1">
                                                <span class="font-bold text-gray-900 text-xs md:text-sm">Anda</span>
                                                <span class="badge-role bg-blue-100 text-blue-700 border border-blue-200">DOSEN</span>
                                                <span class="text-[9px] md:text-[11px] text-gray-400 ms-auto md:ms-0">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="text-xs md:text-sm text-gray-800 mb-1 leading-relaxed bg-white rounded-lg p-0" style="word-break: break-word;">
                                                {!! nl2br(e($reply->message)) !!}
                                            </div>
                                            
                                            <form action="{{ url('/dosen/diskusi/'.$reply->id) }}" method="POST" class="d-inline opacity-0 group-hover:opacity-100 transition-opacity form-ajax" data-chat-id="{{ $chat->id }}" data-confirm="Hapus balasan ini?">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-action text-red-500 mt-1"><i class="bi bi-trash"></i> Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-chat-left-text text-gray-300 text-3xl md:text-4xl"></i>
                </div>
                <h6 class="font-bold text-gray-600 text-sm md:text-base">Belum Ada Diskusi</h6>
                <p class="text-xs md:text-sm text-gray-400">Belum ada mahasiswa yang bertanya saat ini.</p>
            </div>
        @endforelse
    </div>

</div>

<script>
    function toggleForm(id) {
        const form = document.getElementById('replyForm-' + id);
        if (form.style.display === 'none') {
            form.style.display = 'flex';
            form.querySelector('textarea').focus();
        } else {
            form.style.display = 'none';
        }
    }

    function toggleReplies(id) {
        const replies = document.getElementById('replies-' + id);
        const icon = document.getElementById('icon-' + id);
        
        if (replies.style.display === 'none') {
            replies.style.display = 'block';
            if(icon) { icon.classList.remove('bi-caret-down-fill'); icon.classList.add('bi-caret-up-fill'); }
        } else {
            replies.style.display = 'none';
            if(icon) { icon.classList.remove('bi-caret-up-fill'); icon.classList.add('bi-caret-down-fill'); }
        }
    }
    
    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('submit', async function(e) {
            let form = e.target.closest('.form-ajax');
            if (form) {
                e.preventDefault();
                
                if (form.hasAttribute('data-confirm')) {
                    if (!confirm(form.getAttribute('data-confirm'))) return;
                }

                const chatId = form.getAttribute('data-chat-id');
                const btn = form.querySelector('button[type="submit"]');
                const originalContent = btn.innerHTML;
                
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width: 1rem; height: 1rem;"></span>';

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: form.method,
                        body: formData
                    });

                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newCard = doc.getElementById('chat-card-' + chatId);
                    const oldCard = document.getElementById('chat-card-' + chatId);
                    
                    if (newCard && oldCard) {
                        oldCard.innerHTML = newCard.innerHTML;
                        
                        // Memaksa thread balasan terbuka otomatis
                        const newReplies = oldCard.querySelector('.reply-thread');
                        const newIcon = oldCard.querySelector('[id^="icon-"]');
                        if (newReplies) {
                            newReplies.style.display = 'block';
                            if (newIcon) {
                                newIcon.classList.remove('bi-caret-down-fill'); 
                                newIcon.classList.add('bi-caret-up-fill');
                            }
                        }
                    } else {
                        window.location.reload(); 
                    }
                } catch (err) {
                    window.location.reload(); 
                }
            }
        });
    });
</script>

@endsection