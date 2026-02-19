@extends('layouts.dosen')

@section('title', 'Penilaian')

@section('content')
<div class="container-fluid px-0">
    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-bold text-xl text-slate-800 mb-0">Manajemen Penilaian</h4>
            <p class="text-slate-500 text-xs mb-0">Klik pada materi untuk melihat kiriman mahasiswa. <span class="fw-bold">Kuis disembunyikan.</span></p>
        </div>
        
        <div class="bg-white border border-slate-200 rounded-lg d-flex align-items-center px-2 shadow-sm">
            <i class="bi bi-funnel text-slate-400 text-xs me-1"></i>
            <form action="{{ url('/dosen/tugas') }}" method="GET" class="m-0">
                <select name="course_id" class="form-select border-0 shadow-none text-xs font-bold text-slate-600 bg-transparent py-1" onchange="this.form.submit()" style="min-width: 180px; cursor: pointer;">
                    <option value="">Semua Mata Kuliah</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_mk }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- LIST PENUGASAN --}}
    <div class="d-flex flex-column gap-3">
        @forelse($assignments as $assign)
            @if($assign->kategori == 'quiz') @continue @endif

            @php
                $totalSub = $assign->submissions->count();
                $gradedSub = $assign->submissions->whereNotNull('nilai')->count();
                $progressPercent = $totalSub > 0 ? ($gradedSub / $totalSub) * 100 : 0;
            @endphp

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                {{-- MINIMALIST HEADER - Default Tertutup --}}
                <div class="px-3 py-2 bg-slate-50 border-bottom d-flex justify-content-between align-items-center cursor-pointer hover:bg-slate-100 transition-colors" 
                     onclick="toggleCard('{{ $assign->id }}')">
                    
                    <div class="d-flex align-items-center gap-3">
                        <div class="w-8 h-8 bg-slate-800 text-white rounded-lg d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-earmark-text text-sm"></i>
                        </div>
                        <div>
                            <h6 class="font-bold text-sm text-slate-800 mb-0">{{ $assign->judul_materi }}</h6>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $assign->course->nama_mk }}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-4">
                        <div class="d-none d-md-block" style="width: 100px;">
                            <div class="progress h-1 bg-slate-200 rounded-full">
                                <div class="progress-bar bg-indigo-500" style="width: {{ $progressPercent }}%"></div>
                            </div>
                        </div>
                        <div class="text-end" style="min-width: 80px;">
                            <span class="d-block text-[10px] font-bold text-slate-400 uppercase leading-none mb-1">Submisi</span>
                            <span class="text-xs font-bold text-slate-700">{{ $totalSub }} <small class="text-slate-400">Mhs</small></span>
                        </div>
                        {{-- Icon dirotasi -90deg sebagai indikator tertutup --}}
                        <i class="bi bi-chevron-down text-slate-300 text-xs transition-transform duration-200" id="icon-{{ $assign->id }}" style="transform: rotate(-90deg);"></i>
                    </div>
                </div>

                {{-- COMPACT TABLE - Style Display None agar tertutup saat halaman dimuat --}}
                <div id="content-{{ $assign->id }}" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="bg-slate-50/50">
                                <tr class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">
                                    <th class="ps-4 py-2" width="35%">Mahasiswa</th>
                                    <th class="py-2 text-center" width="20%">Karya</th>
                                    <th class="py-2 text-center" width="20%">Waktu</th>
                                    <th class="pe-4 py-2 text-end" width="25%">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($assign->submissions as $sub)
                                    <tr>
                                        <td class="ps-4 py-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ asset('images/' . ($sub->user->foto_profil ?? 'default.png')) }}" 
                                                     class="rounded-full border border-slate-100" width="28" height="28"
                                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($sub->user->nama_lengkap) }}&background=f1f5f9&color=64748b'">
                                                <div class="lh-1">
                                                    <div class="font-bold text-slate-700 text-xs">{{ $sub->user->nama_lengkap }}</div>
                                                    <small class="text-[9px] text-slate-400 font-mono">{{ $sub->user->nim_nidn }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-2">
                                            @if($sub->file_path)
                                                {{-- [FIX] Menggunakan route download via Controller --}}
                                                <a href="{{ url('/dosen/tugas/download/'.$sub->id) }}" target="_blank" 
                                                   class="btn btn-link p-0 text-indigo-600 text-[11px] font-bold text-decoration-none hover:text-indigo-800 transition-colors">
                                                    @if($assign->tipe_submission == 'github' || $assign->tipe_submission == 'link')
                                                        <i class="bi bi-github me-1"></i>Buka Link
                                                    @else
                                                        <i class="bi bi-file-earmark-arrow-down me-1"></i>Unduh File
                                                    @endif
                                                </a>
                                            @else
                                                <span class="text-[10px] text-slate-300 italic">Kosong</span>
                                            @endif
                                        </td>
                                        <td class="text-center py-2 text-[10px] text-slate-500">
                                            {{ \Carbon\Carbon::parse($sub->created_at)->format('d/m/y H:i') }}
                                        </td>
                                        <td class="pe-4 py-2 text-end">
                                            <form action="{{ url('/dosen/tugas/nilai') }}" method="POST" class="d-flex justify-content-end align-items-center gap-1 m-0">
                                                @csrf
                                                <input type="hidden" name="submission_id" value="{{ $sub->id }}">
                                                <input type="number" name="nilai" value="{{ $sub->nilai }}" 
                                                       class="form-control form-control-sm text-center font-bold text-xs py-1 px-1 {{ $sub->nilai ? 'bg-green-50 text-green-700 border-green-200' : 'bg-white' }}" 
                                                       style="width: 50px; height: 28px;" placeholder="-" min="0" max="100" required>
                                                <button type="submit" class="btn btn-slate-800 text-white btn-sm p-0 rounded shadow-sm hover:bg-black transition-all" style="width: 28px; height: 28px;">
                                                    <i class="bi bi-check text-sm"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-slate-400 text-xs italic">Belum ada kiriman tugas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                <i class="bi bi-clipboard-x text-3xl text-slate-300 mb-2 d-block"></i>
                <h6 class="font-bold text-slate-500 text-sm">Data Kosong</h6>
                <p class="text-slate-400 text-[11px]">Belum ada tugas bertipe berkas/link yang aktif.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    .table > :not(caption) > * > * { padding: 0.4rem 0.4rem; }
    .form-control:focus { box-shadow: none; border-color: #6366f1; }
    .btn-slate-800 { background-color: #1e293b; }
    .btn-slate-800:hover { background-color: #0f172a; }
</style>

@endsection

@push('scripts')
<script>
    /** * Logika Toggle dengan Status Tertutup secara Default */
    function toggleCard(id) {
        const content = document.getElementById('content-' + id);
        const icon = document.getElementById('icon-' + id);
        
        if (content.style.display === "none") {
            content.style.display = "block";
            icon.style.transform = "rotate(0deg)";
        } else {
            content.style.display = "none";
            icon.style.transform = "rotate(-90deg)";
        }
    }
</script>
@endpush