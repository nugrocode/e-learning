@extends('layouts.dosen')

@section('title', 'Kelola - ' . $course->nama_mk)

@section('content')
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom bg-white p-4 rounded shadow-sm">
        <div>
            <a href="{{ url('/dosen/kelas') }}" class="text-decoration-none text-muted small fw-bold"><i class="bi bi-arrow-left"></i> Kembali</a>
            <h4 class="mt-2 fw-bold text-dark">{{ $course->nama_mk }}</h4>
            <div class="d-flex gap-3 text-sm text-secondary">
                <span><i class="bi bi-layers"></i> {{ $materials->count() }} Materi</span>
                <span><i class="bi bi-people"></i> {{ $students->count() }} Mahasiswa</span>
            </div>
        </div>
        <button class="btn btn-dark fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahMateri">
            <i class="bi bi-plus-lg me-2"></i> Tambah Materi
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    {{-- NAVIGATION TABS --}}
    <ul class="nav nav-tabs nav-fill mb-4 bg-white rounded shadow-sm p-1" id="myTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold text-dark" id="materi-tab" data-bs-toggle="tab" data-bs-target="#materi">Materi & Kuis</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-dark" id="diskusi-tab" data-bs-toggle="tab" data-bs-target="#diskusi">Diskusi ({{ $discussions->count() }})</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-dark" id="mahasiswa-tab" data-bs-toggle="tab" data-bs-target="#mahasiswa">Mahasiswa</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-dark" id="nilai-tab" data-bs-toggle="tab" data-bs-target="#nilai">Penilaian</button>
        </li>
    </ul>

    <div class="tab-content">
        
        {{-- ================= TAB MATERI ================= --}}
        <div class="tab-pane fade show active" id="materi">
            
            {{-- AI Toolbar --}}
            @if($materials->count() > 1 || $new_materials->count() > 0)
                <div class="d-flex gap-2 mb-3">
                    @if($materials->count() > 1)
                        <form action="{{ url('/dosen/materi/reset/'.$course->id) }}" method="POST" onsubmit="return confirm('Susun ulang urutan?')">
                            @csrf <button class="btn btn-white border shadow-sm btn-sm fw-bold"><i class="bi bi-magic"></i> AI Auto-Sort</button>
                        </form>
                    @endif
                    @if($new_materials->count() > 0)
                        <form action="{{ url('/dosen/materi/update/'.$course->id) }}" method="POST">
                            @csrf <button class="btn btn-warning border shadow-sm btn-sm fw-bold"><i class="bi bi-stars"></i> Sisipkan {{ $new_materials->count() }} Materi Baru</button>
                        </form>
                    @endif
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3" width="5%">Urutan</th>
                            <th width="45%">Judul & Deskripsi</th>
                            <th>Konten</th>
                            <th class="text-end px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $m)
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $m->urutan }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $m->judul_materi }}</div>
                                    <div class="text-muted small text-truncate" style="max-width: 400px;">{{ $m->deskripsi_materi ?? '-' }}</div>
                                    
                                    {{-- Indikator Tipe --}}
                                    <div class="mt-1 d-flex gap-2">
                                        @if($m->kategori == 'quiz')
                                            <span class="badge bg-purple-100 text-purple-700">Kuis</span>
                                        @else
                                            <span class="badge bg-blue-100 text-blue-700">Video</span>
                                        @endif
                                        
                                        @if($m->file_lampiran)
                                            <span class="badge bg-green-100 text-green-700"><i class="bi bi-paperclip"></i> Ada File</span>
                                        @endif
                                        
                                        @if($m->tipe_submission != 'none')
                                            <span class="badge bg-orange-100 text-orange-700"><i class="bi bi-upload"></i> Tugas</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($m->kategori == 'quiz')
                                        <button class="btn btn-sm btn-outline-purple fw-bold" data-bs-toggle="modal" data-bs-target="#modalSoal{{ $m->id }}">
                                            <i class="bi bi-list-check"></i> Kelola Soal ({{ \App\Models\QuizQuestion::where('material_id', $m->id)->count() }})
                                        </button>
                                    @else
                                        <a href="{{ $m->video_url }}" target="_blank" class="text-decoration-none fw-bold small"><i class="bi bi-play-btn"></i> Cek Link</a>
                                    @endif
                                </td>
                                <td class="text-end px-4">
                                    <div class="btn-group">
                                        {{-- PREVIEW: Link ke Route Dosen Preview --}}
                                        <a href="{{ url('/dosen/preview/'.$course->id.'/'.$m->urutan) }}" target="_blank" class="btn btn-outline-secondary btn-sm" title="Preview">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        {{-- EDIT: Buka Modal Edit --}}
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $m->id }}" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        {{-- HAPUS --}}
                                        <form action="{{ url('/dosen/materi/'.$m->id) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- MODAL EDIT MATERI (REAL FUNCTION) --}}
                            <div class="modal fade" id="modalEdit{{ $m->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ url('/dosen/materi/'.$m->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Materi</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="fw-bold small">Judul Materi</label>
                                                <input type="text" name="judul_materi" class="form-control" value="{{ $m->judul_materi }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold small">Deskripsi</label>
                                                <textarea name="deskripsi_materi" class="form-control" rows="3">{{ $m->deskripsi_materi }}</textarea>
                                            </div>
                                            
                                            {{-- Edit Video URL (Hanya jika bukan quiz) --}}
                                            @if($m->kategori != 'quiz')
                                            <div class="mb-3">
                                                <label class="fw-bold small">Link Video (Embed)</label>
                                                <input type="text" name="video_url" class="form-control" value="{{ $m->video_url }}">
                                            </div>
                                            @endif

                                            {{-- Update File --}}
                                            <div class="mb-3 p-2 bg-light border rounded">
                                                <label class="fw-bold small">File Lampiran (PDF/PPT/Excel)</label>
                                                @if($m->file_lampiran)
                                                    <div class="small text-success mb-1"><i class="bi bi-check-circle"></i> File saat ini: {{ $m->file_lampiran }}</div>
                                                @endif
                                                <input type="file" name="file_lampiran" class="form-control form-control-sm">
                                                <div class="form-text small">Upload baru untuk mengganti file lama.</div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="fw-bold small">Jenis Tugas</label>
                                                <select name="tipe_submission" class="form-select">
                                                    <option value="none" {{ $m->tipe_submission == 'none' ? 'selected' : '' }}>Tidak Ada</option>
                                                    <option value="file" {{ $m->tipe_submission == 'file' ? 'selected' : '' }}>Upload File</option>
                                                    <option value="github" {{ $m->tipe_submission == 'github' ? 'selected' : '' }}>Link (Git/Drive)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-primary w-100 fw-bold">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- MODAL KELOLA SOAL (SAMA SEPERTI SEBELUMNYA TAPI RAPIH) --}}
                            @if($m->kategori == 'quiz')
                                <div class="modal fade" id="modalSoal{{ $m->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header bg-light">
                                                <h6 class="modal-title fw-bold">Kelola Soal: {{ $m->judul_materi }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                {{-- Form Input Soal --}}
                                                <form action="{{ url('/dosen/soal') }}" method="POST" class="card card-body shadow-sm mb-4 border-primary">
                                                    @csrf <input type="hidden" name="material_id" value="{{ $m->id }}">
                                                    <h6 class="fw-bold small text-primary mb-3"><i class="bi bi-plus-circle"></i> Tambah Soal Baru</h6>
                                                    <div class="mb-2">
                                                        <input type="text" name="pertanyaan" class="form-control" placeholder="Tulis pertanyaan..." required>
                                                    </div>
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-6"><input type="text" name="opsi_a" class="form-control form-control-sm" placeholder="Opsi A" required></div>
                                                        <div class="col-6"><input type="text" name="opsi_b" class="form-control form-control-sm" placeholder="Opsi B" required></div>
                                                        <div class="col-6"><input type="text" name="opsi_c" class="form-control form-control-sm" placeholder="Opsi C" required></div>
                                                        <div class="col-6"><input type="text" name="opsi_d" class="form-control form-control-sm" placeholder="Opsi D" required></div>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <select name="jawaban_benar" class="form-select form-select-sm w-auto" required>
                                                            <option value="">Kunci Jawaban</option>
                                                            <option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option>
                                                        </select>
                                                        <button class="btn btn-primary btn-sm flex-grow-1">Simpan</button>
                                                    </div>
                                                </form>

                                                {{-- List Soal --}}
                                                <hr>
                                                <h6 class="fw-bold small mb-3">Daftar Soal Aktif</h6>
                                                @foreach(\App\Models\QuizQuestion::where('material_id', $m->id)->get() as $soal)
                                                    <div class="border rounded p-3 mb-2 bg-light position-relative">
                                                        <p class="fw-bold mb-1 small">{{ $loop->iteration }}. {{ $soal->pertanyaan }}</p>
                                                        <div class="small text-muted">
                                                            <span class="{{ $soal->jawaban_benar=='a'?'text-success fw-bold':'' }}">A. {{ $soal->opsi_a }}</span> | 
                                                            <span class="{{ $soal->jawaban_benar=='b'?'text-success fw-bold':'' }}">B. {{ $soal->opsi_b }}</span> | 
                                                            <span class="{{ $soal->jawaban_benar=='c'?'text-success fw-bold':'' }}">C. {{ $soal->opsi_c }}</span> | 
                                                            <span class="{{ $soal->jawaban_benar=='d'?'text-success fw-bold':'' }}">D. {{ $soal->opsi_d }}</span>
                                                        </div>
                                                        <form action="{{ url('/dosen/soal/'.$soal->id) }}" method="POST" class="position-absolute top-0 end-0 mt-2 me-2">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm text-danger p-0"><i class="bi bi-trash-fill"></i></button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada konten materi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= TAB DISKUSI (REVISED) ================= --}}
        <div class="tab-pane fade" id="diskusi">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    @if($discussions->isEmpty())
                        {{-- EMPTY STATE DISKUSI --}}
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                                <i class="bi bi-chat-square-text text-secondary display-4"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Belum Ada Pertanyaan</h5>
                            <p class="text-muted">Jika mahasiswa bertanya pada materi apapun, pertanyaan akan muncul di sini.</p>
                        </div>
                    @else
                        {{-- LIST DISKUSI --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark">Daftar Pertanyaan Mahasiswa</h6>
                            <span class="badge bg-secondary">{{ $discussions->count() }} Utas</span>
                        </div>

                        @foreach($discussions as $chat)
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-file-play me-1"></i> {{ $chat->material->judul_materi ?? 'Materi Dihapus' }}
                                    </span>
                                    <small class="text-muted">{{ $chat->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="d-flex gap-3">
                                        <img src="{{ asset('images/'.($chat->user->foto_profil ?? 'default.png')) }}" class="rounded-circle border" style="width:40px;height:40px;">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">{{ $chat->user->nama_lengkap }}</div>
                                            <p class="mb-2 text-dark">{{ $chat->isi }}</p>
                                            
                                            {{-- Form Balas --}}
                                            <form action="{{ url('/dosen/proses-diskusi') }}" method="POST" class="mt-3 p-3 bg-light rounded border">
                                                @csrf
                                                <input type="hidden" name="material_id" value="{{ $chat->material_id }}">
                                                <input type="hidden" name="parent_id" value="{{ $chat->id }}">
                                                <label class="small fw-bold text-primary mb-1">Balas Pertanyaan Ini:</label>
                                                <div class="input-group">
                                                    <input type="text" name="isi" class="form-control" placeholder="Tulis jawaban..." required>
                                                    <button class="btn btn-primary"><i class="bi bi-send-fill"></i></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>

        {{-- ================= TAB MAHASISWA & NILAI (Simpel Saja) ================= --}}
        <div class="tab-pane fade" id="mahasiswa">
            {{-- Tabel Mahasiswa (Copy dari versi sebelumnya sudah oke, cukup rapihkan class) --}}
            <div class="card border-0 shadow-sm">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light"><tr><th class="px-4">Mahasiswa</th><th>Progress</th><th class="text-end px-4">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($students as $s)
                        @php $prog = \App\Models\Progress::where('user_id',$s->id)->whereIn('material_id',$materials->pluck('id'))->count(); $pct = $materials->count()>0?round(($prog/$materials->count())*100):0; @endphp
                        <tr>
                            <td class="px-4 fw-bold">{{ $s->nama_lengkap }}</td>
                            <td><div class="progress" style="height:6px;width:100px;"><div class="progress-bar bg-success" style="width:{{$pct}}%"></div></div><small>{{$pct}}%</small></td>
                            <td class="text-end px-4"><form action="{{ url('/dosen/kick-student') }}" method="POST">@csrf <input type="hidden" name="user_id" value="{{$s->id}}"><input type="hidden" name="course_id" value="{{$course->id}}"><button class="btn btn-sm btn-outline-danger">Reset</button></form></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="nilai">
            {{-- Tabel Nilai (Copy dari versi sebelumnya) --}}
            <div class="card border-0 shadow-sm">
                 <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light"><tr><th class="px-4">Mahasiswa</th><th>Tugas</th><th>File</th><th>Nilai</th><th class="text-end px-4">Aksi</th></tr></thead>
                    <tbody>
                         @php $subs = \App\Models\Submission::whereIn('material_id', $materials->pluck('id'))->with(['user','material'])->latest()->get(); @endphp
                         @foreach($subs as $sub)
                         <tr>
                             <td class="px-4 fw-bold">{{ $sub->user->nama_lengkap }}</td>
                             <td>{{ $sub->material->judul_materi }}</td>
                             <td><a href="{{ asset('uploads/submissions/'.$sub->file_path) }}" target="_blank" class="fw-bold text-decoration-none">Buka File</a></td>
                             <td>{{ $sub->nilai ?? '-' }}</td>
                             <td class="text-end px-4"><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#nilai{{$sub->id}}">Nilai</button></td>
                         </tr>
                         {{-- Modal Nilai (Inline) --}}
                         <div class="modal fade" id="nilai{{$sub->id}}" tabindex="-1"><div class="modal-dialog modal-sm"><form action="{{ url('/dosen/nilai/'.$sub->id) }}" method="POST" class="modal-content"><div class="modal-body">@csrf @method('PUT') <input type="number" name="nilai" class="form-control mb-2" value="{{$sub->nilai}}" placeholder="0-100" required><button class="btn btn-dark w-100">Simpan</button></div></form></div></div>
                         @endforeach
                    </tbody>
                 </table>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH (Global) --}}
    <div class="modal fade" id="modalTambahMateri" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ url('/dosen/materi') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf <input type="hidden" name="course_id" value="{{ $course->id }}">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Konten</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="small fw-bold">Judul</label><input type="text" name="judul_materi" class="form-control" required></div>
                    <div class="mb-3"><label class="small fw-bold">Deskripsi</label><textarea name="deskripsi_materi" class="form-control" rows="2"></textarea></div>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><label class="small fw-bold">Tipe</label><select name="kategori" class="form-select"><option value="video">Video</option><option value="quiz">Kuis</option></select></div>
                        <div class="col-6"><label class="small fw-bold">Tugas</label><select name="tipe_submission" class="form-select"><option value="none">Tidak Ada</option><option value="file">Upload File</option><option value="github">Link</option></select></div>
                    </div>
                    <div class="mb-3"><label class="small fw-bold">Link Video (Embed)</label><input type="text" name="video_url" class="form-control" placeholder="URL Youtube Embed"></div>
                    <div class="mb-3 p-2 bg-light border rounded"><label class="small fw-bold">Upload Materi (PDF/Excel) - Opsional</label><input type="file" name="file_lampiran" class="form-control"></div>
                </div>
                <div class="modal-footer"><button class="btn btn-dark w-100">Simpan</button></div>
            </form>
        </div>
    </div>
@endsection