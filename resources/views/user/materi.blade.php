@extends('layouts.app')

@section('title', $materi->judul_materi)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ url('/mata-kuliah/' . $materi->course->concentration_id) }}" class="text-decoration-none text-gray-600 hover:text-blue-900">
            <i class="bi bi-arrow-left"></i> Kembali ke Kurikulum
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger mb-4 shadow-sm border-0">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success mb-4 shadow-sm border-0">{{ session('success') }}</div>
    @endif

    <div class="row g-4">

        <div class="col-lg-8">
            <div class="bg-white p-4 rounded-xl shadow-sm h-100">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-800 mb-0">{{ $materi->judul_materi }}</h1>
                        <span class="badge {{ $materi->kategori == 'quiz' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-400 text-black' }} px-3 py-1 rounded-pill">
                            {{ $materi->kategori == 'quiz' ? 'Mini Quiz' : 'Video Materi' }} #{{ $materi->urutan }}
                        </span>
                    </div>
                </div>

                @if($materi->kategori == 'quiz')

                    @if($data_nilai && request('mode') != 'retake')
                        <div class="text-center py-5 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 animate-fade-in-up">
                            <i class="bi bi-trophy-fill text-6xl text-yellow-500 mb-3 block"></i>
                            <h1 class="text-5xl font-bold text-blue-900 mb-2">{{ $data_nilai->skor }}</h1>
                            <p class="text-gray-600 font-semibold">Nilai Terakhir Anda</p>

                            <div class="mt-5 d-flex justify-content-center gap-3">
                                <a href="{{ url()->current() }}?mode=retake" class="btn btn-outline-primary px-4 py-2 rounded-lg">
                                    <i class="bi bi-arrow-repeat"></i> Ulangi Kuis
                                </a>
                                <form action="{{ url('/proses-progress') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="material_id" value="{{ $materi->id }}">
                                    <input type="hidden" name="course_id" value="{{ $course_id }}">
                                    <input type="hidden" name="urutan" value="{{ $urutan }}">
                                    <button type="submit" class="btn btn-dark px-4 py-2 rounded-lg">
                                        Lanjut Materi Berikutnya <i class="bi bi-arrow-right ms-1"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <form action="{{ url('/proses-kuis') }}" method="POST">
                            @csrf
                            <input type="hidden" name="material_id" value="{{ $materi->id }}">
                            <input type="hidden" name="course_id" value="{{ $course_id }}">
                            <input type="hidden" name="urutan" value="{{ $urutan }}">

                            @forelse($soal_kuis as $index => $soal)
                                <div class="mb-4 p-4 border rounded-lg bg-gray-50 hover:bg-white transition shadow-sm">
                                    <p class="font-semibold text-lg mb-3">{{ $index + 1 }}. {{ $soal->pertanyaan }}</p>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach(['a','b','c','d'] as $opt)
                                            <label class="d-flex align-items-center gap-2 p-2 border rounded cursor-pointer hover:bg-gray-100">
                                                <input class="form-check-input mt-0" type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $opt }}" required>
                                                <span>{{ $soal->{'opsi_'.$opt} }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i> Belum ada soal untuk kuis ini.
                                </div>
                            @endforelse

                            @if(count($soal_kuis) > 0)
                                <button type="submit" class="btn btn-primary w-100 py-3 font-bold text-lg rounded-lg shadow-md hover:bg-blue-700 transition">
                                    Kirim Jawaban <i class="bi bi-send-fill ms-2"></i>
                                </button>
                            @endif
                        </form>
                    @endif

                @else
                    <div class="ratio ratio-16x9 mb-4 rounded-xl overflow-hidden bg-black shadow-lg">
                        <iframe src="{{ $materi->video_url }}" allowfullscreen></iframe>
                    </div>

                    <ul class="nav nav-tabs mb-3" id="materiTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold" id="deskripsi-tab" data-bs-toggle="tab" data-bs-target="#deskripsi" type="button">Deskripsi</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" id="tugas-tab" data-bs-toggle="tab" data-bs-target="#tugas" type="button">Tugas</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="materiTabContent">
                        <div class="tab-pane fade show active p-3 bg-gray-50 rounded border" id="deskripsi">
                            <p class="text-gray-700 leading-relaxed mb-0">{{ $materi->deskripsi_materi ?? 'Tidak ada deskripsi.' }}</p>
                        </div>

                        <div class="tab-pane fade p-3 bg-gray-50 rounded border" id="tugas">
                            @if ($data_tugas)
                                <div class="alert alert-success d-flex align-items-center border-0 shadow-sm">
                                    <i class="bi bi-check-circle-fill text-2xl me-3 text-green-600"></i>
                                    <div>
                                        <strong>Tugas Sudah Dikumpulkan!</strong><br>
                                        <a href="{{ $data_tugas->file_path }}" target="_blank" class="text-green-800 text-decoration-underline font-semibold">
                                            Lihat Repository Anda <i class="bi bi-box-arrow-up-right text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="bg-white p-4 border rounded shadow-sm">
                                    <h6 class="font-bold mb-3 text-gray-800">Kumpulkan Tugas</h6>
                                    <form action="{{ url('/proses-tugas') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="material_id" value="{{ $materi->id }}">
                                        <div class="mb-3">
                                            <label class="form-label text-sm font-medium text-gray-600">Link Repository GitHub</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-gray-100"><i class="bi bi-github"></i></span>
                                                <input type="url" name="link_github" class="form-control" placeholder="https://github.com/username/repo" required>
                                            </div>
                                            <small class="text-muted text-xs">Pastikan repositori bersifat publik.</small>
                                        </div>
                                        <button class="btn btn-dark w-100 fw-bold" type="submit">Kirim Tugas</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5 text-end border-t pt-4">
                        <form action="{{ url('/proses-progress') }}" method="POST">
                            @csrf
                            <input type="hidden" name="material_id" value="{{ $materi->id }}">
                            <input type="hidden" name="course_id" value="{{ $course_id }}">
                            <input type="hidden" name="urutan" value="{{ $urutan }}">
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill font-bold shadow-md hover:bg-blue-700 transition transform hover:-translate-y-1">
                                Selesai & Lanjut <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="d-block" id="playlistCollapse">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky-top" style="top: 20px; z-index: 1;">
                    <div class="p-3 bg-gray-800 text-white font-bold border-b d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-collection-play me-2"></i> Daftar Materi</span>
                        <span class="badge bg-gray-600 rounded-pill">{{ $daftar_materi->count() }} Item</span>
                    </div>

                    <div class="overflow-y-auto custom-scrollbar" style="max-height: 500px;">
                        @foreach($daftar_materi as $m)
                            @php
                                $is_active = $m->urutan == $urutan;
                                $link = url('/belajar/' . $course_id . '/' . $m->urutan);
                            @endphp

                            <a href="{{ $link }}" class="text-decoration-none">
                                <div class="p-3 border-b d-flex align-items-center gap-3 transition {{ $is_active ? 'bg-blue-50 border-l-4 border-blue-900' : 'hover:bg-gray-50' }}">
                                    <div class="flex-shrink-0">
                                        @if($m->kategori == 'quiz')
                                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 d-flex align-items-center justify-content-center">
                                                <i class="bi bi-puzzle-fill"></i>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 d-flex align-items-center justify-content-center">
                                                <i class="bi bi-play-fill"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="mb-0 text-sm font-semibold text-gray-800 text-truncate {{ $is_active ? 'text-blue-900' : '' }}">
                                            {{ $m->judul_materi }}
                                        </p>
                                        <small class="text-xs text-gray-500 uppercase font-bold tracking-wider">
                                            {{ $m->kategori == 'quiz' ? 'Mini Kuis' : 'Video' }}
                                        </small>
                                    </div>
                                    @if($is_active)
                                        <i class="bi bi-bar-chart-fill text-blue-900 animate-pulse"></i>
                                    @endif
                                </div>
                            </a>
                        @endforeach

                        @if($daftar_materi->isEmpty())
                            <div class="p-3 text-center text-gray-500">
                                <small>Tidak ada materi ditemukan.</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
