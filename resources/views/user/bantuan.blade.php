@extends('layouts.app')

@section('title', 'Pusat Bantuan')

@push('styles')
    <style>
        /* CSS Khusus Halaman Bantuan */
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

        /* Warna saat aktif/terbuka */
        .faq-btn.active {
            background-color: #eef2ff;
            color: #1e1e4f;
            border-color: #c7d2fe;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        /* Animasi Panah */
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

        /* Animasi Muncul */
        .faq-content.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="text-center mb-5 animate-fade-in-up">
        <h2 class="font-bold text-3xl text-gray-800">Pusat Bantuan</h2>
        <p class="text-gray-600 mt-2">Temukan jawaban atas pertanyaan Anda seputar E-Learning UKI Toraja.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="bg-white p-4 rounded-xl shadow-sm mb-4">
                <h5 class="font-bold text-gray-700 mb-4 ps-2 border-l-4 border-yellow-400">Pertanyaan Umum (FAQ)</h5>

                <div class="mb-3">
                    <button class="faq-btn active" onclick="toggleFaq('faq1', this)">
                        <span><i class="bi bi-question-circle-fill me-2 text-blue-900"></i> Bagaimana cara memulai
                            belajar?</span>
                        <i class="bi bi-chevron-down icon-chevron"></i>
                    </button>
                    <div id="faq1" class="faq-content show">
                        Masuk ke menu <strong>Jalur Belajar</strong> di sidebar kiri. Pilih Konsentrasi yang Anda minati
                        (misal: IOT), lalu pilih Mata Kuliah yang tersedia. Klik tombol "Masuk Kelas" untuk memulai.
                    </div>
                </div>

                <div class="mb-3">
                    <button class="faq-btn" onclick="toggleFaq('faq2', this)">
                        <span><i class="bi bi-lock-fill me-2 text-gray-500"></i> Kenapa materi selanjutnya terkunci?</span>
                        <i class="bi bi-chevron-down icon-chevron"></i>
                    </button>
                    <div id="faq2" class="faq-content">
                        Sistem ini menggunakan metode <strong>Structured Learning Path</strong>. Anda wajib menyelesaikan
                        materi secara berurutan. Pastikan Anda telah menonton video sampai selesai atau mengerjakan kuis
                        pada materi sebelumnya agar materi berikutnya terbuka.
                    </div>
                </div>

                <div class="mb-3">
                    <button class="faq-btn" onclick="toggleFaq('faq3', this)">
                        <span><i class="bi bi-github me-2 text-black"></i> Bagaimana cara mengumpulkan tugas?</span>
                        <i class="bi bi-chevron-down icon-chevron"></i>
                    </button>
                    <div id="faq3" class="faq-content">
                        Pada halaman materi bertipe Video, klik tab <strong>Tugas</strong> di bawah video player. Upload
                        kode tugas Anda ke repository GitHub pribadi, lalu salin link repository tersebut ke kolom yang
                        tersedia dan klik "Kirim Link".
                    </div>
                </div>

                <div class="mb-3">
                    <button class="faq-btn" onclick="toggleFaq('faq4', this)">
                        <span><i class="bi bi-chat-dots-fill me-2 text-green-600"></i> Bagaimana cara berdiskusi?</span>
                        <i class="bi bi-chevron-down icon-chevron"></i>
                    </button>
                    <div id="faq4" class="faq-content">
                        Gunakan menu <strong>Diskusi</strong> di sidebar. Anda akan masuk ke Grup Diskusi Angkatan di mana
                        Anda bisa bertanya kepada dosen maupun teman sekelas mengenai materi perkuliahan.
                    </div>
                </div>

            </div>

            <div
                class="bg-blue-900 text-white p-4 rounded-xl shadow-lg d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-center text-md-start">
                    <h5 class="font-bold mb-1">Masih butuh bantuan?</h5>
                    <p class="text-sm text-blue-200 mb-0">Tim IT Support UKI Toraja siap membantu Anda.</p>
                </div>
                <a href="https://wa.me/6282290435050" target="_blank"
                    class="btn btn-warning text-blue-900 font-bold rounded-pill px-4 shadow hover:bg-yellow-300 transition">
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

            // Tutup semua dulu (Accordion effect)
            var allContents = document.querySelectorAll('.faq-content');
            var allBtns = document.querySelectorAll('.faq-btn');

            // Hapus class show/active dari semua elemen
            allContents.forEach(function(el) {
                el.classList.remove('show');
            });
            allBtns.forEach(function(el) {
                el.classList.remove('active');
            });

            // Jika tadi tertutup, maka sekarang BUKA yang diklik
            if (!isOpen) {
                content.classList.add('show');
                btnElement.classList.add('active');
            }
        }
    </script>
@endpush
