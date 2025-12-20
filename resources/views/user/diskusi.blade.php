@extends('layouts.app')

@section('title', 'Diskusi AI Assistant')

@push('styles')
    <style>
        /* --- 1. AREA UTAMA CHAT --- */
        .chat-container {
            height: 70vh;
            /* Tinggi area chat */
            overflow-y: auto;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 12px 12px 0 0;
            border: 1px solid #e2e8f0;
            scroll-behavior: smooth;
        }

        /* --- 2. BUBBLE CHAT --- */
        .message {
            display: flex;
            margin-bottom: 20px;
            align-items: flex-start;
            animation: fadeIn 0.3s ease;
        }

        .message-content {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.95rem;
            line-height: 1.6;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            word-wrap: break-word;
        }

        /* User (Posisi Kanan) */
        .message.user {
            flex-direction: row-reverse;
        }

        .message.user .message-content {
            background-color: #1e1e4f;
            color: white;
            border-top-right-radius: 0;
        }

        .message.user .avatar {
            margin-left: 10px;
        }

        /* AI (Posisi Kiri) */
        .message.ai {
            flex-direction: row;
        }

        .message.ai .message-content {
            background-color: white;
            color: #334155;
            border: 1px solid #e2e8f0;
            border-top-left-radius: 0;
        }

        .message.ai .avatar {
            margin-right: 10px;
        }

        /* Avatar Icon */
        .avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .avatar-ai {
            background: #10b981;
            color: white;
        }

        /* Hijau */
        .avatar-user {
            background: #cbd5e1;
            color: #475569;
        }

        /* --- 3. INPUT AREA --- */
        .input-area {
            background: white;
            padding: 15px;
            border: 1px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 12px 12px;
        }

        /* --- 4. FORMATTING MARKDOWN (BIAR KODINGAN RAPI) --- */
        .markdown-body p {
            margin-bottom: 10px;
        }

        .markdown-body pre {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-family: 'Courier New', Courier, monospace;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .markdown-body code {
            background: #e2e8f0;
            padding: 2px 5px;
            border-radius: 4px;
            font-family: monospace;
            color: #d63384;
            font-size: 0.9em;
        }

        /* Fix: Code block inside pre should not have pink background */
        .markdown-body pre code {
            background: transparent;
            color: inherit;
            padding: 0;
        }

        .markdown-body ul,
        .markdown-body ol {
            padding-left: 20px;
            margin-bottom: 10px;
        }

        .markdown-body li {
            margin-bottom: 5px;
        }

        .markdown-body strong {
            font-weight: 700;
            color: #1e1e4f;
        }

        /* --- 5. ANIMASI TYPING (LOADING) --- */
        .typing-indicator span {
            display: inline-block;
            width: 6px;
            height: 6px;
            background-color: #94a3b8;
            border-radius: 50%;
            animation: typing 1.4s infinite both;
            margin: 0 1px;
        }

        .typing-indicator span:nth-child(1) {
            animation-delay: 0s;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {

            0%,
            100% {
                opacity: 0.2;
                transform: scale(0.8);
            }

            50% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-green-100 p-3 rounded-full text-green-700">
                    <i class="bi bi-robot text-2xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 m-0">AI Assistant (Gemini)</h2>
                    <p class="text-gray-500 text-sm m-0">Tanyakan apa saja seputar perkuliahan atau kodingan.</p>
                </div>
            </div>

            <div class="chat-container custom-scrollbar" id="chatBox">
                <div class="message ai">
                    <div class="avatar avatar-ai"><i class="bi bi-stars"></i></div>
                    <div class="message-content">
                        Halo, {{ session('nama') }}! 👋 <br>
                        Saya adalah asisten virtual pintar. Ada yang bisa saya bantu? Coba minta saya buatkan contoh
                        kodingan!
                    </div>
                </div>
            </div>

            <div class="input-area shadow-sm">
                <form id="chatForm" class="d-flex gap-2">
                    @csrf
                    <input type="text" id="userVal"
                        class="form-control py-3 px-4 rounded-pill bg-gray-50 border-gray-200"
                        placeholder="Ketik pertanyaan Anda..." autocomplete="off">
                    <button type="submit"
                        class="btn btn-dark rounded-circle w-12 h-12 flex items-center justify-center shadow-md hover:bg-gray-800 transition">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
        const chatBox = document.getElementById('chatBox');
        const chatForm = document.getElementById('chatForm');
        const userInput = document.getElementById('userVal');

        // Fungsi Scroll ke Bawah Otomatis
        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // Fungsi Tambah Chat Bubble
        function appendMessage(role, text) {
            const isUser = role === 'user';
            const avatar = isUser ? '<i class="bi bi-person-fill"></i>' : '<i class="bi bi-stars"></i>';
            const alignClass = isUser ? 'user' : 'ai';
            const avatarClass = isUser ? 'avatar-user' : 'avatar-ai';

            // PENTING: Jika AI, format teksnya pakai library marked() biar bisa bold/code block
            // Jika User, teks biasa saja (biar aman dari XSS)
            let contentHtml = isUser ? text : marked.parse(text);

            const html = `
            <div class="message ${alignClass}">
                <div class="avatar ${avatarClass}">${avatar}</div>
                <div class="message-content markdown-body">${contentHtml}</div>
            </div>
        `;
            chatBox.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        }

        // Fungsi Tampilkan Loading (Titik tiga ...)
        function showTyping() {
            const html = `
            <div class="message ai" id="typingLoader">
                <div class="avatar avatar-ai"><i class="bi bi-stars"></i></div>
                <div class="message-content typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            </div>
        `;
            chatBox.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        }

        // Fungsi Hapus Loading
        function removeTyping() {
            const loader = document.getElementById('typingLoader');
            if (loader) loader.remove();
        }

        // EVENT LISTENER: SAAT TOMBOL KIRIM DIKLIK
        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = userInput.value.trim();
            if (!message) return;

            // 1. Tampilkan Pesan User
            appendMessage('user', message);
            userInput.value = '';

            // 2. Tampilkan Loading
            showTyping();

            try {
                // 3. Kirim ke Server (Laravel API)
                const response = await fetch("{{ url('/ask-ai') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        message: message
                    })
                });

                const data = await response.json();

                // 4. Hapus Loading & Tampilkan Balasan AI
                removeTyping();

                if (data.reply) {
                    appendMessage('ai', data.reply);
                } else {
                    appendMessage('ai', "Maaf, tidak ada respon.");
                }

            } catch (error) {
                removeTyping();
                appendMessage('ai', "Maaf, terjadi kesalahan koneksi. Silakan coba lagi.");
                console.error(error);
            }
        });
    </script>
@endpush
