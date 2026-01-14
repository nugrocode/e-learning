@extends('layouts.app')

@section('title', 'Pusat Bantuan')

@push('styles')
    <style>
        /* Styling Tombol FAQ */
        .faq-btn {
            width: 100%;
            text-align: left;
            padding: 15px 20px;
            background-color: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            color: #374151;
            transition: all 0.2s;
            cursor: pointer;
        }

        .faq-btn:hover {
            background-color: #f9fafb;
        }

        /* State Aktif */
        .faq-btn.active {
            background-color: #eef2ff;
            color: #1e1e4f;
            border-color: #c7d2fe;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        /* Ikon Panah */
        .faq-btn .icon-chevron {
            transition: transform 0.3s;
        }

        .faq-btn.active .icon-chevron {
            transform: rotate(180deg);
        }

        /* Konten Jawaban */
        .faq-content {
            display: none;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            color: #4b5563;
            line-height: 1.6;
        }

        .faq-content.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* RESPONSIF MOBILE */
        @media (max-width: 768px) {
            .faq-btn { padding: 12px 15px; font-size: 0.9rem; }
            .faq-content { padding: 15px; font-size: 0.85rem; }
        }
    </style>
@endpush

@section('content')
    {{-- Header Bantuan (Responsif) --}}
    <div class="text-center mb-5 animate-fade-in-up pt-2">
        <h2 class="font-bold text-2xl md:text-3xl text-gray-800">Pusat Bantuan</h2>
        <p class="text-sm md:text-base text-gray-600 mt-2 px-4">
            Temukan jawaban atas pertanyaan Anda seputar E-Learning UKI Toraja.
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">

            {{-- Container FAQ --}}
            <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm mb-4">
                <h5 class="font-bold text-gray-700 mb-4 ps-2 border-l-4 border-yellow-400 text-sm md:text-lg">
                    Pertanyaan Umum (FAQ)
                </h5>

                {{-- FAQ 1 --}}
                <div class="mb-3">
                    <button class="faq-btn active" onclick="toggleFaq('faq1', this)">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-question-circle-fill text-blue-900"></i> Cara memulai belajar?
                        </span>
                        <i class="bi bi-chevron-down icon-chevron"></i>
                    </button>
                    <div id="faq1" class="faq-content show">
                        Masuk ke menu <strong>Jalur Belajar</strong> di sidebar kiri. Pilih Konsentrasi yang Anda minati (misal: IoT), lalu pilih Mata Kuliah yang tersedia. Klik tombol "Masuk Kelas" untuk memulai.
                    </div>
                </div>

                {{-- FAQ 2 --}}
                <div class="mb-3">
                    <button class="faq-btn" onclick="toggleFaq('faq2', this)">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-lock-fill text-gray-500"></i> Kenapa materi terkunci?
                        </span>
                        <i class="bi bi-chevron-down icon-chevron"></i>
                    </button>
                    <div id="faq2" class="faq-content">
                        Sistem ini menggunakan metode <strong>Structured Learning Path</strong>. Anda wajib menyelesaikan materi secara berurutan. Pastikan Anda telah menonton video sampai selesai atau mengerjakan kuis pada materi sebelumnya.
                    </div>
                </div>

                {{-- FAQ 3 --}}
                <div class="mb-3">
                    <button class="faq-btn" onclick="toggleFaq('faq3', this)">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-github text-black"></i> Cara mengumpulkan tugas?
                        </span>
                        <i class="bi bi-chevron-down icon-chevron"></i>
                    </button>
                    <div id="faq3" class="faq-content">
                        Pada halaman materi bertipe Video, klik tab <strong>Tugas</strong> di bawah video player. Upload file tugas atau salin link repository GitHub Anda ke kolom yang tersedia.
                    </div>
                </div>

                {{-- FAQ 4 --}}
                <div class="mb-3">
                    <button class="faq-btn" onclick="toggleFaq('faq4', this)">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-chat-dots-fill text-green-600"></i> Cara berdiskusi?
                        </span>
                        <i class="bi bi-chevron-down icon-chevron"></i>
                    </button>
                    <div id="faq4" class="faq-content">
                        Gunakan menu <strong>Diskusi AI</strong> di sidebar untuk bertanya pada asisten cerdas, atau gunakan fitur komentar di bawah setiap materi pelajaran untuk berdiskusi dengan dosen dan teman.
                    </div>
                </div>

            </div>

            {{-- Box Kontak Admin (Responsif) --}}
            <div class="bg-blue-900 text-white p-4 rounded-xl shadow-lg d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 text-center md:text-start">
                <div>
                    <h5 class="font-bold mb-1 text-sm md:text-lg">Masih butuh bantuan?</h5>
                    <p class="text-xs md:text-sm text-blue-200 mb-0">Tim IT Support UKI Toraja siap membantu Anda.</p>
                </div>
                <a href="https://wa.me/6282290435050" target="_blank"
                    class="btn btn-warning text-blue-900 font-bold rounded-pill px-4 py-2 text-sm shadow hover:bg-yellow-300 transition w-full md:w-auto">
                    <i class="bi bi-whatsapp me-1"></i> Hubungi Admin
                </a>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleFaq(id, btnElement) {
            var content = document.getElementById(id);
            var isOpen = content.classList.contains('show');

            // Tutup semua accordion lain
            var allContents = document.querySelectorAll('.faq-content');
            var allBtns = document.querySelectorAll('.faq-btn');

            allContents.forEach(el => el.classList.remove('show'));
            allBtns.forEach(el => el.classList.remove('active'));

            // Buka yang diklik
            if (!isOpen) {
                content.classList.add('show');
                btnElement.classList.add('active');
            }
        }
    </script>
@endpush
