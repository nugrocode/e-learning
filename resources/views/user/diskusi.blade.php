@extends('layouts.app')

@section('title', 'Asisten Virtual AI')

@push('styles')
<style>
    /* Sembunyikan Scrollbar tapi tetap bisa scroll */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@section('content')
<div class="row animate-fade-in-up">
    <div class="col-12">

        {{-- HEADER LAMA SUDAH DIHAPUS --}}

        {{-- AREA CHAT --}}
        {{-- Saya naikkan tingginya jadi 80vh biar lebih puas karena header sudah hilang --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden d-flex flex-column" style="height: 80vh;">
            
            {{-- LIST PESAN --}}
            <div id="chatContainer" class="flex-grow-1 p-4 overflow-y-auto no-scrollbar bg-gray-50">
                
                {{-- PESAN SAMBUTAN AI --}}
                <div class="d-flex gap-3 mb-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                            <i class="bi bi-stars text-lg"></i>
                        </div>
                    </div>
                    {{-- Bubble Chat AI --}}
                    <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 text-sm text-gray-700" style="max-width: 85%;">
                        <p class="mb-1 font-bold text-blue-600 text-xs">AI Assistant</p>
                        Halo, {{ session('nama') }}! 👋 <br>
                        Saya asisten virtual pintar. Ada yang bisa saya bantu hari ini?
                    </div>
                </div>

            </div>

            {{-- INPUT AREA --}}
            <div class="p-3 bg-white border-top">
                <form id="formChat" class="d-flex gap-2">
                    @csrf
                    <input type="text" id="inputPesan" class="form-control rounded-pill border-gray-300 px-4 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik pertanyaan..." autocomplete="off" required>
                    <button type="submit" class="btn btn-dark rounded-circle w-10 h-10 flex items-center justify-center shadow-md transition transform hover:scale-105">
                        <i class="bi bi-send-fill text-sm"></i>
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const chatContainer = document.getElementById('chatContainer');
    const formChat = document.getElementById('formChat');
    const inputPesan = document.getElementById('inputPesan');

    // URL Foto Profil User
    const userPhoto = "{{ session('foto') && session('foto') != 'default.png' ? asset('storage/profiles/' . session('foto')) : asset('images/logo_ukit.png') }}";

    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    formChat.addEventListener('submit', async function(e) {
        e.preventDefault();
        const pesan = inputPesan.value.trim();
        if(!pesan) return;

        // 1. Tampilkan Pesan User
        appendUserMessage(pesan);
        inputPesan.value = '';
        scrollToBottom();

        // 2. Tampilkan Loading
        const loadingId = appendLoadingAI();
        scrollToBottom();

        try {
            // 3. Request ke Server
            const response = await fetch("{{ url('/ask-ai') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: pesan })
            });
            const data = await response.json();

            // 4. Ganti Loading dengan Jawaban
            document.getElementById(loadingId).remove();
            appendAIMessage(data.reply);
        } catch (error) {
            document.getElementById(loadingId).remove();
            appendAIMessage("Maaf, terjadi kesalahan koneksi.");
        }
        scrollToBottom();
    });

    // RENDER PESAN USER
    function appendUserMessage(text) {
        const html = `
            <div class="d-flex gap-3 mb-4 flex-row-reverse animate-fade-in-up">
                <div class="flex-shrink-0">
                    <img src="${userPhoto}" 
                         class="rounded-full border-2 border-white shadow-sm bg-white"
                         style="width: 40px; height: 40px; object-fit: cover;"
                         onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                </div>
                <div class="bg-gray-800 text-white p-3 rounded-2xl rounded-tr-none shadow-md text-sm" style="max-width: 85%;">
                    <p class="mb-1 font-bold text-gray-300 text-xs text-end">Anda</p>
                    ${text}
                </div>
            </div>
        `;
        chatContainer.insertAdjacentHTML('beforeend', html);
    }

    // RENDER PESAN AI
    function appendAIMessage(text) {
        const formattedText = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>').replace(/\n/g, '<br>');
        
        const html = `
            <div class="d-flex gap-3 mb-4 animate-fade-in-up">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white shadow-md">
                        <i class="bi bi-stars text-lg"></i>
                    </div>
                </div>
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 text-sm text-gray-700" style="max-width: 85%;">
                    <p class="mb-1 font-bold text-blue-600 text-xs">AI Assistant</p>
                    ${formattedText}
                </div>
            </div>
        `;
        chatContainer.insertAdjacentHTML('beforeend', html);
    }

    // RENDER LOADING
    function appendLoadingAI() {
        const id = 'loading-' + Date.now();
        const html = `
            <div id="${id}" class="d-flex gap-3 mb-4 animate-pulse">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                        <i class="bi bi-three-dots text-gray-400"></i>
                    </div>
                </div>
                <div class="bg-gray-100 p-3 rounded-2xl rounded-tl-none text-xs text-gray-500 italic">
                    Sedang mengetik...
                </div>
            </div>
        `;
        chatContainer.insertAdjacentHTML('beforeend', html);
        return id;
    }
</script>
@endpush