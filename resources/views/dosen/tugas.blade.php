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
                {{-- HEADER KARTU TUGAS --}}
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
                        <i class="bi bi-chevron-down text-slate-300 text-xs transition-transform duration-200" id="icon-{{ $assign->id }}" style="transform: rotate(-90deg);"></i>
                    </div>
                </div>

                {{-- TABEL PENILAIAN --}}
                <div id="content-{{ $assign->id }}" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="bg-slate-50/50">
                                <tr class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">
                                    <th class="ps-4 py-2" width="30%">Mahasiswa</th>
                                    <th class="py-2 text-center" width="25%">Karya / File</th>
                                    <th class="py-2 text-center" width="20%">Waktu Kumpul</th>
                                    <th class="pe-4 py-2 text-end" width="25%">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($assign->submissions as $sub)
                                    <tr>
                                        <td class="ps-4 py-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $sub->user->foto_profil && $sub->user->foto_profil != 'default.png' ? asset('storage/profiles/' . $sub->user->foto_profil) : asset('images/logo_ukit.png') }}" 
                                                     class="rounded-full border border-slate-200 object-cover" style="width: 32px; height: 32px;"
                                                     onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                                                <div class="lh-1">
                                                    <div class="font-bold text-slate-700 text-xs">{{ $sub->user->nama_lengkap }}</div>
                                                    <small class="text-[9px] text-slate-400 font-mono">{{ $sub->user->nim_nidn ?? 'NIM TDK ADA' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-2">
                                            @if($sub->file_path)
                                                @if($assign->tipe_submission == 'github' || $assign->tipe_submission == 'link' || filter_var($sub->file_path, FILTER_VALIDATE_URL))
                                                    @php
                                                        $url = preg_match("~^(?:f|ht)tps?://~i", $sub->file_path) ? $sub->file_path : "https://" . $sub->file_path;
                                                    @endphp
                                                    <div class="d-flex flex-column align-items-center">
                                                        <a href="{{ $url }}" target="_blank" 
                                                           class="btn btn-dark btn-sm py-1 px-2 text-[10px] font-bold rounded shadow-sm d-flex align-items-center gap-1 w-fit">
                                                            <i class="bi bi-link-45deg"></i> Buka Tautan
                                                        </a>
                                                        <a href="{{ $url }}" target="_blank" 
                                                           class="text-[9px] text-blue-500 mt-1 d-inline-block text-truncate" 
                                                           style="max-width: 140px;" title="{{ $sub->file_path }}">
                                                            {{ $sub->file_path }}
                                                        </a>
                                                    </div>
                                                @else
                                                    <a href="{{ url('/dosen/tugas/download/'.$sub->id) }}" target="_blank" 
                                                       class="btn btn-outline-primary btn-sm py-1 px-2 text-[10px] font-bold rounded shadow-sm d-flex align-items-center gap-1 w-fit mx-auto">
                                                        <i class="bi bi-file-earmark-arrow-down"></i> Unduh Berkas
                                                    </a>
                                                @endif
                                            @else
                                                <span class="badge bg-slate-100 text-slate-400 border border-slate-200 text-[9px]">Belum Kumpul</span>
                                            @endif
                                        </td>
                                        <td class="text-center py-2 text-[10px] text-slate-500 font-medium">
                                            {{ \Carbon\Carbon::parse($sub->created_at)->format('d/m/y - H:i') }}
                                        </td>
                                        <td class="pe-4 py-2 text-end">
                                            
                                            {{-- [UPDATE] Penambahan class form-nilai-ajax --}}
                                            <form action="{{ url('/dosen/tugas/nilai/'.$sub->id) }}" method="POST" class="d-flex justify-content-end align-items-center gap-1 m-0 form-nilai-ajax">
                                                @csrf
                                                <input type="number" name="nilai" value="{{ $sub->nilai }}" 
                                                       class="form-control form-control-sm text-center font-bold text-xs py-1 px-1 shadow-sm bg-white text-slate-800 {{ $sub->nilai ? 'border-green-400' : 'border-slate-300' }}" 
                                                       style="width: 55px; height: 30px; border-radius: 6px;" placeholder="0" min="0" max="100" required>
                                                
                                                <button type="submit" class="btn btn-dark btn-sm p-0 rounded shadow-sm d-flex align-items-center justify-content-center transition-all btn-submit-nilai" style="width: 30px; height: 30px;" title="Simpan Nilai">
                                                    <i class="bi bi-check-lg text-white text-sm"></i>
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-slate-400 text-xs italic">Belum ada mahasiswa yang mengirimkan tugas ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50 mt-2">
                <i class="bi bi-clipboard-x text-3xl text-slate-300 mb-2 d-block"></i>
                <h6 class="font-bold text-slate-500 text-sm">Data Kosong</h6>
                <p class="text-slate-400 text-[11px]">Belum ada tugas bertipe berkas/link yang aktif.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    .table > :not(caption) > * > * { padding: 0.6rem 0.4rem; }
    .form-control:focus { box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25); border-color: #6366f1; }
    .btn-dark:hover { background-color: #1e293b; border-color: #1e293b; }
</style>

@endsection

@push('scripts')
<script>
    // 1. Logika Buka-Tutup Tabel
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

    // 2. Logika AJAX Tanpa Reload (Silent Save)
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.form-nilai-ajax').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault(); // Mencegah reload halaman
                
                const btn = this.querySelector('.btn-submit-nilai');
                const input = this.querySelector('input[name="nilai"]');
                const originalIcon = '<i class="bi bi-check-lg text-white text-sm"></i>';
                
                // Ubah tombol jadi loading
                btn.innerHTML = '<span class="spinner-border spinner-border-sm text-white" style="width: 14px; height: 14px; border-width: 2px;"></span>';
                btn.disabled = true;

                try {
                    const formData = new FormData(this);
                    
                    // Mengirim data nilai ke controller secara rahasia
                    const response = await fetch(this.action, {
                        method: this.method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.ok) {
                        // SKSES: Beri efek visual hijau pada tombol
                        btn.innerHTML = '<i class="bi bi-check2-all text-white text-sm"></i>';
                        btn.classList.remove('btn-dark');
                        btn.classList.add('btn-success', 'bg-green-600', 'border-green-600');
                        
                        // Ubah border input jadi hijau agar menandakan sudah dinilai
                        input.classList.remove('border-slate-300');
                        input.classList.add('border-green-400');

                        // Kembalikan tombol ke warna aslinya setelah 2 detik
                        setTimeout(() => {
                            btn.classList.remove('btn-success', 'bg-green-600', 'border-green-600');
                            btn.classList.add('btn-dark');
                            btn.innerHTML = originalIcon;
                            btn.disabled = false;
                        }, 2000);
                        
                    } else {
                        alert('Terjadi kesalahan saat menyimpan nilai.');
                        btn.innerHTML = originalIcon;
                        btn.disabled = false;
                    }
                } catch (error) {
                    alert('Gagal terhubung ke server. Periksa koneksi Anda.');
                    btn.innerHTML = originalIcon;
                    btn.disabled = false;
                }
            });
        });
    });
</script>
@endpush