@extends('layouts.app')

@section('title', 'Diskusi AI Assistant')

@push('styles')
    <style>
        /* --- AREA CHAT (Responsif Height) --- */
        .chat-container {
            height: 70vh; /* Default Laptop */
            overflow-y: auto;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 12px 12px 0 0;
            border: 1px solid #e2e8f0;
            scroll-behavior: smooth;
        }

        /* --- BUBBLE CHAT --- */
        .message {
            display: flex;
            margin-bottom: 20px;
            align-items: flex-start;
            animation: fadeIn 0.3s ease;
        }

        .message-content {
            max-width: 80%; /* Laptop */
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.95rem;
            line-height: 1.6;
            position: relative;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            word-wrap: break-word;
        }

        /* User Style */
        .message.user { flex-direction: row-reverse; }
        .message.user .message-content {
            background-color: #1e1e4f;
            color: white;
            border-top-right-radius: 0;
        }
        .message.user .avatar { margin-left: 10px; }

        /* AI Style */
        .message.ai { flex-direction: row; }
        .message.ai .message-content {
            background-color: white;
            color: #334155;
            border: 1px solid #e2e8f0;
            border-top-left-radius: 0;
        }
        .message.ai .avatar { margin-right: 10px; }

        /* Avatar */
        .avatar {
            width: 35px; height: 35px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }
        .avatar-ai { background: #10b981; color: white; }
        .avatar-user { background: #cbd5e1; color: #475569; }

        /* MARKDOWN (Code Block) */
        .markdown-body pre {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 10px;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 0.85em;
            margin: 10px 0;
        }
        .markdown-body p { margin-bottom: 0.5rem; }

        /* ANIMASI TYPING */
        .typing-indicator span {
            display: inline-block; width: 5px; height: 5px;
            background-color: #94a3b8; border-radius: 50%;
            animation: typing 1.4s infinite both; margin: 0 1px;
        }
        .typing-indicator span:nth-child(1) { animation-delay: 0s; }
        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing { 0%, 100% { opacity: 0.2; transform: scale(0.8); } 50% { opacity: 1; transform: scale(1); } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* --- RESPONSIVE MOBILE FIX --- */
        @media (max-width: 768px) {
            .chat-container {
                height: 65vh; /* Kurangi tinggi di HP biar keyboard aman */
                padding: 15px;
            }
            .message-content {
                max-width: 90%; /* Bubble lebih lebar di HP */
                font-size: 0.85rem;
                padding: 10px 14px;
            }
            .avatar {
                width: 30px; height: 30px; font-size: 0.8rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">

            {{-- Header Chat (Responsif) --}}
            <div class="d-flex align-items-center gap-3 mb-3 pt-2">
                <div class="bg-green-100 p-2 md:p-3 rounded-full text-green-700">
                    <i class="bi bi-robot text-xl md:text-2xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-xl md:text-2xl text-gray-800 m-0">AI Assistant (Gemini)</h2>
                    <p class="text-gray-500 text-xs md:text-sm m-0">Tanyakan apa saja seputar perkuliahan atau kodingan.</p>
                </div>
            </div>

            {{-- Chat Box --}}
            <div class="chat-container custom-scrollbar" id="chatBox">
                <div class="message ai">
                    <div class="avatar avatar-ai"><i class="bi bi-stars"></i></div>
                    <div class="message-content">
                        Halo, {{ session('nama') }}! 👋 <br>
                        Saya asisten virtual pintar. Ada yang bisa saya bantu?
                    </div>
                </div>
            </div>

            {{-- Input Area --}}
            <div class="input-area bg-white p-3 border border-t-0 rounded-b-xl shadow-sm">
                <form id="chatForm" class="d-flex gap-2">
                    @csrf
                    <input type="text" id="userVal"
                        class="form-control py-2 md:py-3 px-4 rounded-pill bg-gray-50 border-gray-200 text-sm md:text-base"
                        placeholder="Ketik pertanyaan..." autocomplete="off">

                    <button type="submit"
                        class="btn btn-dark rounded-circle w-10 h-10 md:w-12 md:h-12 flex items-center justify-center shadow-md hover:bg-gray-800 transition flex-shrink-0">
                        <i class="bi bi-send-fill text-sm md:text-base"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    {{-- Library Marked untuk format teks AI --}}
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
        const chatBox = document.getElementById('chatBox');
        const chatForm = document.getElementById('chatForm');
        const userInput = document.getElementById('userVal');

        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // Tambah Pesan ke Chat
        function appendMessage(role, text) {
            const isUser = role === 'user';
            const alignClass = isUser ? 'user' : 'ai';
            const avatarClass = isUser ? 'avatar-user' : 'avatar-ai';
            const icon = isUser ? '<i class="bi bi-person-fill"></i>' : '<i class="bi bi-stars"></i>';

            // Format teks (User: teks biasa, AI: Markdown)
            let contentHtml = isUser ? text : marked.parse(text);

            const html = `
            <div class="message ${alignClass}">
                <div class="avatar ${avatarClass}">${icon}</div>
                <div class="message-content markdown-body">${contentHtml}</div>
            </div>`;

            chatBox.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        }

        // Efek Loading (Ngetik...)
        function showTyping() {
            const html = `
            <div class="message ai" id="typingLoader">
                <div class="avatar avatar-ai"><i class="bi bi-stars"></i></div>
                <div class="message-content typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            </div>`;
            chatBox.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        }

        function removeTyping() {
            const loader = document.getElementById('typingLoader');
            if (loader) loader.remove();
        }

        // Kirim Pesan
        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = userInput.value.trim();
            if (!message) return;

            // 1. Tampilkan User Msg
            appendMessage('user', message);
            userInput.value = '';

            // 2. Loading...
            showTyping();

            try {
                // 3. Request ke Server
                const response = await fetch("{{ url('/ask-ai') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();

                // 4. Tampilkan Balasan
                removeTyping();
                if (data.reply) {
                    appendMessage('ai', data.reply);
                } else {
                    appendMessage('ai', "Maaf, tidak ada respon.");
                }

            } catch (error) {
                removeTyping();
                appendMessage('ai', "Maaf, terjadi kesalahan koneksi.");
            }
        });
    </script>
@endpush
