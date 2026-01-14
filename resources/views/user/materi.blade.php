@extends('layouts.app')

@section('title', $materi->judul_materi)

@section('content')
    {{-- NAVIGASI KEMBALI --}}
    <div class="mb-3 animate-fade-in-up">
        <a href="{{ url('/mata-kuliah/' . $materi->course->concentration_id) }}"
           class="text-decoration-none text-gray-600 hover:text-blue-900 text-xs md:text-sm flex items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali ke Kurikulum
        </a>
    </div>

    {{-- ALERT SYSTEM --}}
    @if(session('error'))
        <div class="alert alert-danger mb-3 p-2 text-xs md:text-sm shadow-sm border-0 d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success mb-3 p-2 text-xs md:text-sm shadow-sm border-0 d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-3 md:g-4">
        {{-- KOLOM KIRI: KONTEN UTAMA --}}
        <div class="col-lg-8">
            <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm h-100">

                {{-- HEADER MATERI --}}
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3 border-b pb-3">
                    <h1 class="text-lg md:text-2xl font-bold text-gray-800 mb-0 leading-tight flex-grow-1">
                        {{ $materi->judul_materi }}
                    </h1>
                    <span class="badge {{ $materi->kategori == 'quiz' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-400 text-black' }} px-2 py-1 rounded-pill text-[10px] md:text-xs whitespace-nowrap flex-shrink-0">
                        {{ $materi->kategori == 'quiz' ? 'Mini Quiz' : 'Video Materi' }} #{{ $materi->urutan }}
                    </span>
                </div>

                {{-- KONTEN (QUIZ / VIDEO) --}}
                @if($materi->kategori == 'quiz')

                    @if($data_nilai && request('mode') != 'retake')
                        {{-- Tampilan Skor --}}
                        <div class="text-center py-4 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <i class="bi bi-trophy-fill text-4xl md:text-5xl text-yellow-500 mb-2 block"></i>
                            <h1 class="text-3xl md:text-4xl font-bold text-blue-900 mb-1">{{ $data_nilai->skor }}</h1>
                            <p class="text-gray-600 font-semibold text-xs md:text-sm">Nilai Terakhir Anda</p>
                            <div class="mt-4 d-flex flex-column flex-sm-row justify-content-center gap-2 px-3">
                                <a href="{{ url()->current() }}?mode=retake" class="btn btn-outline-primary btn-sm rounded-lg font-bold">
                                    <i class="bi bi-arrow-repeat"></i> Ulangi Kuis
                                </a>
                                <form action="{{ url('/proses-progress') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="material_id" value="{{ $materi->id }}">
                                    <input type="hidden" name="course_id" value="{{ $course_id }}">
                                    <input type="hidden" name="urutan" value="{{ $urutan }}">
                                    <button type="submit" class="btn btn-dark btn-sm rounded-lg font-bold w-100">
                                        Lanjut Materi <i class="bi bi-arrow-right ms-1"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- Form Kuis --}}
                        <form action="{{ url('/proses-kuis') }}" method="POST">
                            @csrf
                            <input type="hidden" name="material_id" value="{{ $materi->id }}">
                            <input type="hidden" name="course_id" value="{{ $course_id }}">
                            <input type="hidden" name="urutan" value="{{ $urutan }}">
                            @forelse($soal_kuis as $index => $soal)
                                <div class="mb-3 p-3 border rounded-lg bg-gray-50 hover:bg-white transition">
                                    <p class="font-semibold text-sm md:text-base mb-2">{{ $index + 1 }}. {{ $soal->pertanyaan }}</p>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach(['a','b','c','d'] as $opt)
                                            <label class="d-flex align-items-center gap-2 p-2 border rounded cursor-pointer hover:bg-gray-100 bg-white">
                                                <input class="form-check-input mt-0" type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $opt }}" required>
                                                <span class="text-xs md:text-sm">{{ $soal->{'opsi_'.$opt} }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-warning text-sm">Belum ada soal untuk kuis ini.</div>
                            @endforelse
                            @if(count($soal_kuis) > 0)
                                <button type="submit" class="btn btn-primary w-100 py-2 font-bold text-sm md:text-base rounded-lg shadow-md hover:bg-blue-700 transition">
                                    Kirim Jawaban <i class="bi bi-send-fill ms-2"></i>
                                </button>
                            @endif
                        </form>
                    @endif

                @else
                    {{-- VIDEO PLAYER --}}
                    <div class="ratio ratio-16x9 mb-3 rounded-xl overflow-hidden bg-black shadow-lg">
                        <iframe src="{{ $materi->video_url }}" allowfullscreen></iframe>
                    </div>

                    {{-- TABS --}}
                    <ul class="nav nav-tabs mb-3 text-xs md:text-sm" id="materiTab" role="tablist">
                        <li class="nav-item"><button class="nav-link active fw-bold py-2 px-3" id="deskripsi-tab" data-bs-toggle="tab" data-bs-target="#deskripsi" type="button">Deskripsi</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold py-2 px-3" id="tugas-tab" data-bs-toggle="tab" data-bs-target="#tugas" type="button">Tugas</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold py-2 px-3" id="diskusi-tab" data-bs-toggle="tab" data-bs-target="#diskusi" type="button">Diskusi</button></li>
                    </ul>

                    <div class="tab-content">
                        {{-- Tab Deskripsi --}}
                        <div class="tab-pane fade show active p-3 bg-gray-50 rounded border text-sm md:text-base" id="deskripsi">
                            <p class="text-gray-700 leading-relaxed mb-3">{{ $materi->deskripsi_materi ?? 'Tidak ada deskripsi.' }}</p>
                            @if($materi->file_lampiran)
                                <div class="mt-3 pt-3 border-t">
                                    <p class="mb-2 text-xs font-bold text-gray-500 uppercase">File Pendukung</p>
                                    <a href="{{ asset('uploads/materials/' . $materi->file_lampiran) }}" target="_blank" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2 shadow-sm">
                                        <i class="bi bi-file-earmark-arrow-down-fill"></i> Download Materi (PDF/PPT)
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Tab Tugas --}}
                        <div class="tab-pane fade p-3 bg-gray-50 rounded border" id="tugas">
                            @if ($data_tugas)
                                <div class="alert alert-success d-flex align-items-center border-0 shadow-sm text-xs md:text-sm">
                                    <i class="bi bi-check-circle-fill text-lg me-2 text-green-600"></i>
                                    <div>
                                        <strong>Tugas Terkirim!</strong><br>
                                        <span class="text-[10px] md:text-xs">Dikirim: {{ \Carbon\Carbon::parse($data_tugas->tanggal_kumpul)->format('d M, H:i') }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="bg-white p-3 border rounded shadow-sm">
                                    <h6 class="font-bold mb-2 text-gray-800 text-sm">Upload Tugas</h6>
                                    <form action="{{ url('/proses-tugas') }}" method="POST" enctype="multipart/form-data">
                                        @csrf <input type="hidden" name="material_id" value="{{ $materi->id }}">
                                        @if($materi->tipe_submission == 'github')
                                            <div class="mb-2">
                                                <label class="form-label text-[10px] font-bold text-gray-500 uppercase">Link Repository</label>
                                                <input type="url" name="file_tugas" class="form-control form-control-sm" placeholder="https://github.com/..." required>
                                            </div>
                                        @else
                                            <div class="mb-2">
                                                <label class="form-label text-[10px] font-bold text-gray-500 uppercase">File (PDF/ZIP)</label>
                                                <input type="file" name="file_tugas" class="form-control form-control-sm" required>
                                            </div>
                                        @endif
                                        <button class="btn btn-dark w-100 btn-sm font-bold mt-2" type="submit">
                                            <i class="bi bi-send-fill me-1"></i> Kirim
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        {{-- Tab Diskusi --}}
                        <div class="tab-pane fade" id="diskusi">
                            <style>
                                .forum-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 10px; }
                                .forum-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #e5e7eb; }
                                .reply-wrapper { display: none; margin-top: 10px; }
                                .reply-container { margin-left: 10px; border-left: 2px solid #e5e7eb; padding-left: 10px; }
                                /* Tombol Link Custom untuk Lihat Balasan */
                                .btn-link-custom { font-size: 0.75rem; color: #6b7280; cursor: pointer; text-decoration: none; font-weight: 600; background: none; border: none; padding: 0; }
                                .btn-link-custom:hover { color: #1e1e4f; }

                                .badge-role { font-size: 0.6rem; padding: 2px 5px; border-radius: 4px; margin-left: 4px; text-transform: uppercase; font-weight: bold; }
                                .badge-dosen { background: #1e1e4f; color: white; }
                                .badge-mhs { background: #f3f4f6; color: #666; border: 1px solid #ddd; }
                            </style>

                            <div class="p-1">
                                {{-- Form Diskusi Baru --}}
                                <div class="bg-gray-50 p-3 rounded-xl border mb-3">
                                    <form class="formDiskusi" data-parent="0">
                                        @csrf <input type="hidden" name="course_id" value="{{ $course_id }}"> <input type="hidden" name="material_id" value="{{ $materi->id }}">
                                        <textarea name="message" class="form-control form-control-sm text-sm" rows="2" placeholder="Tanya sesuatu..." required></textarea>
                                        <div class="text-end mt-2">
                                            <button type="submit" class="btn btn-primary btn-sm px-3 rounded-pill font-bold shadow-sm text-xs">Kirim</button>
                                        </div>
                                    </form>
                                </div>

                                {{-- List Diskusi --}}
                                <div id="forumList" style="max-height: 500px; overflow-y: auto;" class="custom-scrollbar">
                                    @forelse($diskusi as $chat)
                                        <div class="forum-card animate-fade-in-up" id="chat-{{ $chat->id }}">
                                            <div class="d-flex gap-2">
                                                <img src="{{ asset('images/' . ($chat->user->foto_profil ?? 'default.png')) }}" class="forum-avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($chat->user->nama_lengkap) }}'">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="fw-bold text-xs md:text-sm text-dark">{{ $chat->user->nama_lengkap }}</span>
                                                        <span class="badge-role {{ $chat->user->role == 'dosen' ? 'badge-dosen' : 'badge-mhs' }}">{{ $chat->user->role }}</span>
                                                        <small class="text-muted ms-auto text-[10px]">
                                                            {{ \Carbon\Carbon::parse($chat->created_at)->diffForHumans() }}
                                                            @if($chat->user_id == session('user_id'))
                                                                <span class="text-red-500 cursor-pointer ms-2" onclick="deleteComment({{ $chat->id }}, 'parent')"><i class="bi bi-trash"></i></span>
                                                            @endif
                                                        </small>
                                                    </div>
                                                    <p class="text-xs md:text-sm text-gray-700 mb-2">{{ $chat->message }}</p>

                                                    {{-- TOMBOL AKSI (BALAS & LIHAT BALASAN) --}}
                                                    <div class="d-flex align-items-center gap-3 text-xs action-buttons">
                                                        <span class="cursor-pointer text-blue-900 font-bold" onclick="toggleForm('{{ $chat->id }}')">Balas</span>

                                                        {{-- Tombol Lihat Balasan (Server Side Check) --}}
                                                        @if($chat->replies->count() > 0)
                                                            <button type="button" class="btn-link-custom" id="btn-toggle-{{ $chat->id }}" onclick="toggleReplies(event, '{{ $chat->id }}')">
                                                                <i class="bi bi-chevron-down"></i> Lihat {{ $chat->replies->count() }} Balasan
                                                            </button>
                                                        @endif
                                                    </div>

                                                    {{-- Balasan --}}
                                                    <div class="reply-wrapper" id="wrapper-{{ $chat->id }}">
                                                        <div class="reply-container" id="replies-{{ $chat->id }}">
                                                            @foreach($chat->replies as $reply)
                                                                <div class="bg-gray-50 p-2 rounded mb-2 border" id="reply-{{ $reply->id }}">
                                                                    <div class="d-flex gap-2">
                                                                        <img src="{{ asset('images/' . ($reply->user->foto_profil ?? 'default.png')) }}" class="forum-avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($reply->user->nama_lengkap) }}'">

                                                                        <div class="flex-grow-1">
                                                                            <div class="d-flex align-items-center mb-1">
                                                                                <strong class="text-xs">{{ $reply->user->nama_lengkap }}</strong>
                                                                                <span class="badge-role {{ $reply->user->role == 'dosen' ? 'badge-dosen' : 'badge-mhs' }}">{{ $reply->user->role }}</span>
                                                                                @if($reply->user_id == session('user_id'))
                                                                                    <span class="text-red-500 cursor-pointer ms-auto text-[10px]" onclick="deleteComment({{ $reply->id }}, 'reply')"><i class="bi bi-trash"></i></span>
                                                                                @endif
                                                                            </div>
                                                                            <p class="mb-0 text-xs text-gray-600">{{ $reply->message }}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    {{-- Form Balas --}}
                                                    <div class="mt-2" id="replyForm-{{ $chat->id }}" style="display: none;">
                                                        <form class="formDiskusi" data-parent="{{ $chat->id }}">
                                                            @csrf <input type="hidden" name="course_id" value="{{ $course_id }}"> <input type="hidden" name="material_id" value="{{ $materi->id }}"> <input type="hidden" name="parent_id" value="{{ $chat->id }}">
                                                            <div class="d-flex gap-2">
                                                                <textarea name="message" class="form-control form-control-sm text-xs" rows="1" placeholder="Balas..." required></textarea>
                                                                <button type="submit" class="btn btn-dark btn-sm rounded-circle w-8 h-8 flex items-center justify-center"><i class="bi bi-send-fill text-[10px]"></i></button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-xs text-muted">Belum ada diskusi.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Selesai --}}
                    <div class="mt-4 text-end">
                        <form action="{{ url('/proses-progress') }}" method="POST">
                            @csrf <input type="hidden" name="material_id" value="{{ $materi->id }}"> <input type="hidden" name="course_id" value="{{ $course_id }}"> <input type="hidden" name="urutan" value="{{ $urutan }}">
                            <button class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm font-bold">
                                Selesai & Lanjut <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- KOLOM KANAN: PLAYLIST --}}
        <div class="col-lg-4">
            <div class="d-block">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky-top" style="top: 90px; z-index: 10;">
                    <div class="p-3 bg-gray-800 text-white font-bold border-b d-flex justify-content-between align-items-center">
                        <span class="text-xs md:text-sm"><i class="bi bi-collection-play me-2"></i> Daftar Materi</span>
                        <span class="badge bg-gray-600 rounded-pill text-[10px]">{{ $daftar_materi->count() }} Item</span>
                    </div>

                    <div class="overflow-y-auto custom-scrollbar" style="max-height: 400px;">
                        @foreach($daftar_materi as $m)
                            @php $is_active = $m->urutan == $urutan; @endphp
                            <a href="{{ url('/belajar/' . $course_id . '/' . $m->urutan) }}" class="text-decoration-none">
                                <div class="p-3 border-b d-flex align-items-center gap-3 transition {{ $is_active ? 'bg-blue-50 border-l-4 border-blue-900' : 'hover:bg-gray-50' }}">
                                    <div class="flex-shrink-0">
                                        <i class="bi {{ $m->kategori == 'quiz' ? 'bi-puzzle-fill text-purple-600' : 'bi-play-fill text-blue-600' }} text-xs"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="mb-0 text-xs font-semibold text-gray-800 text-truncate {{ $is_active ? 'text-blue-900' : '' }}">
                                            {{ $m->judul_materi }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const imgBaseUrl = "{{ asset('images') }}/";

        function toggleForm(id) {
            const form = document.getElementById('replyForm-' + id);
            form.style.display = (form.style.display === 'block') ? 'none' : 'block';
            if(form.style.display === 'block') form.querySelector('textarea').focus();
        }

        function toggleReplies(e, id) {
            e.preventDefault();
            const wrapper = document.getElementById('wrapper-' + id);
            const btn = document.getElementById('btn-toggle-' + id);

            if (wrapper.style.display === 'block') {
                wrapper.style.display = 'none';
                btn.innerHTML = '<i class="bi bi-chevron-down"></i> Lihat Balasan';
            } else {
                wrapper.style.display = 'block';
                btn.innerHTML = '<i class="bi bi-chevron-up"></i> Sembunyikan';
            }
        }

        async function deleteComment(id, type) {
            if(!confirm('Hapus komentar ini?')) return;
            try {
                const response = await fetch("{{ url('/diskusi') }}/" + id, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const result = await response.json();
                if(result.status === 'success') {
                    if(type === 'parent') document.getElementById('chat-' + id).remove();
                    else document.getElementById('reply-' + id).remove();
                } else alert(result.message);
            } catch (error) { alert('Gagal menghapus.'); }
        }

        // AJAX SUBMIT KOMENTAR
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('submit', async function(e) {
                if (e.target.classList.contains('formDiskusi')) {
                    e.preventDefault();
                    const form = e.target;
                    const btn = form.querySelector('button');
                    const input = form.querySelector('textarea');
                    const msg = input.value.trim();
                    const parentId = form.getAttribute('data-parent');

                    if(!msg) return;

                    btn.disabled = true;
                    const formData = new FormData(form);

                    try {
                        const response = await fetch("{{ url('/proses-diskusi') }}", {
                            method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const res = await response.json();

                        if(res.status === 'success') {
                            const d = res.data;
                            const roleBadge = d.role == 'dosen' ? 'badge-dosen' : 'badge-mhs';
                            const fotoSrc = d.foto ? imgBaseUrl + d.foto : `https://ui-avatars.com/api/?name=${encodeURIComponent(d.nama)}`;

                            if (parentId == 0) {
                                // Logic Tambah Komentar Utama (Parent)
                                const html = `
                                    <div class="forum-card animate-fade-in-up" id="chat-${d.id}">
                                        <div class="d-flex gap-2">
                                            <img src="${fotoSrc}" class="forum-avatar">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="fw-bold text-xs md:text-sm text-dark">${d.nama}</span>
                                                    <span class="badge-role ${roleBadge}">${d.role}</span>
                                                    <small class="text-muted ms-auto text-[10px]">Baru saja <span class="text-red-500 cursor-pointer ms-2" onclick="deleteComment(${d.id}, 'parent')"><i class="bi bi-trash"></i></span></small>
                                                </div>
                                                <p class="text-xs md:text-sm text-gray-700 mb-2">${d.isi}</p>
                                                <div class="d-flex align-items-center gap-3 text-xs action-buttons">
                                                    <span class="cursor-pointer text-blue-900 font-bold" onclick="toggleForm('${d.id}')">Balas</span>
                                                </div>
                                                <div class="reply-wrapper" id="wrapper-${d.id}">
                                                    <div class="reply-container" id="replies-${d.id}"></div>
                                                </div>
                                                <div class="mt-2" id="replyForm-${d.id}" style="display: none;">
                                                    <form class="formDiskusi" data-parent="${d.id}">
                                                        @csrf <input type="hidden" name="course_id" value="{{ $course_id }}"> <input type="hidden" name="material_id" value="{{ $materi->id }}"> <input type="hidden" name="parent_id" value="${d.id}">
                                                        <div class="d-flex gap-2">
                                                            <textarea name="message" class="form-control form-control-sm text-xs" rows="1" placeholder="Balas..." required></textarea>
                                                            <button type="submit" class="btn btn-dark btn-sm rounded-circle w-8 h-8 flex items-center justify-center"><i class="bi bi-send-fill text-[10px]"></i></button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                                document.getElementById('forumList').insertAdjacentHTML('afterbegin', html);
                            } else {
                                // Logic Tambah Balasan (Reply)
                                const html = `
                                    <div class="bg-gray-50 p-2 rounded mb-2 border" id="reply-${d.id}">
                                        <div class="d-flex gap-2">
                                            <img src="${fotoSrc}" class="forum-avatar">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <strong class="text-xs">${d.nama}</strong>
                                                    <span class="badge-role ${roleBadge}">${d.role}</span>
                                                    <span class="text-red-500 cursor-pointer ms-auto text-[10px]" onclick="deleteComment(${d.id}, 'reply')"><i class="bi bi-trash"></i></span>
                                                </div>
                                                <p class="mb-0 text-xs text-gray-600">${d.isi}</p>
                                            </div>
                                        </div>
                                    </div>`;

                                // 1. Tambahkan HTML balasan
                                document.getElementById('replies-' + parentId).insertAdjacentHTML('beforeend', html);
                                document.getElementById('replyForm-' + parentId).style.display = 'none';

                                // 2. Tampilkan Wrapper Balasan (Paksa Buka)
                                const wrapper = document.getElementById('wrapper-' + parentId);
                                wrapper.style.display = 'block';

                                // 3. Cek apakah tombol "Lihat Balasan" sudah ada? Jika belum, BUAT BARU.
                                const parentChat = document.getElementById('chat-' + parentId);
                                const actionDiv = parentChat.querySelector('.action-buttons');
                                let toggleBtn = document.getElementById('btn-toggle-' + parentId);

                                if (!toggleBtn) {
                                    const newBtn = `<button type="button" class="btn-link-custom ms-2" id="btn-toggle-${parentId}" onclick="toggleReplies(event, '${parentId}')"><i class="bi bi-chevron-up"></i> Sembunyikan</button>`;
                                    actionDiv.insertAdjacentHTML('beforeend', newBtn);
                                } else {
                                    // Jika tombol sudah ada, ubah teks jadi "Sembunyikan" karena kita buka wrappernya
                                    toggleBtn.innerHTML = '<i class="bi bi-chevron-up"></i> Sembunyikan';
                                }
                            }
                            input.value = '';
                        }
                    } catch (err) { console.error(err); }
                    finally { btn.disabled = false; }
                }
            });
        });
    </script>
@endpush
