@extends('layouts.dosen')

@section('title', 'Data Mahasiswa')

@section('content')

    {{-- 1. HEADER --}}
    <div class="mb-4">
        <h2 class="font-bold text-xl text-gray-800">Data Mahasiswa</h2>
        <p class="text-gray-500 text-sm">Pantau perkembangan dan data mahasiswa yang Anda ampu.</p>
    </div>

    {{-- 2. FILTER & SEARCH --}}
    <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 mb-4">
        <form action="" method="GET" class="d-flex flex-column flex-md-row gap-3">
            
            {{-- Filter Kelas --}}
            <div class="input-group w-full md:w-auto" style="min-width: 200px;">
                <span class="input-group-text bg-gray-50 border-end-0 text-gray-400"><i class="bi bi-funnel"></i></span>
                <select name="kelas_id" class="form-select border-start-0 bg-gray-50 text-sm focus:ring-0" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('kelas_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_mk }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Search Bar --}}
             <div class="input-group flex-grow-1">
                <span class="input-group-text bg-gray-50 border-end-0 text-gray-400"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control border-start-0 bg-gray-50 text-sm" placeholder="Cari nama atau NIM mahasiswa..." value="{{ request('q') }}">
                <button class="btn btn-primary text-sm font-bold px-4">Cari</button>
            </div>

            {{-- Reset --}}
            <a href="{{ url('/dosen/mahasiswa') }}" class="btn btn-light border text-gray-500 font-bold text-sm d-flex align-items-center gap-2">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </form>
    </div>

    {{-- 3. LIST MAHASISWA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-gray-50 text-gray-500 text-[10px] uppercase font-bold tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Identitas Mahasiswa</th>
                        <th>Kelas / Mata Kuliah</th>
                        <th>Status</th>
                        <th class="text-end px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $s)
                        <tr>
                            {{-- IDENTITAS (Foto, Nama, NIM) --}}
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $s->foto_profil && $s->foto_profil != 'default.png' ? asset('storage/profiles/' . $s->foto_profil) : asset('images/logo_ukit.png') }}" 
                                         class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm"
                                         onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                                    <div>
                                        <h6 class="font-bold text-gray-800 text-sm mb-0">{{ $s->nama_lengkap }}</h6>
                                        {{-- PERBAIKAN: Gunakan nim_nidn --}}
                                        <span class="text-xs text-gray-400 font-mono">{{ $s->nim_nidn ?? 'Belum ada NIM' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- KELAS YANG DIIKUTI --}}
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($s->courses_taken as $mk)
                                        <span class="badge bg-blue-50 text-blue-700 border border-blue-100 rounded-pill text-[10px] px-2 py-1">
                                            {{ $mk->nama_mk }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td>
                                <span class="badge bg-green-50 text-green-600 border border-green-100 rounded-pill text-[10px] px-2 py-1 flex items-center w-fit gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                </span>
                            </td>

                            {{-- AKSI --}}
                            <td class="text-end px-4">
                                <div class="d-flex justify-content-end gap-2">
                                    {{-- Tombol Detail --}}
                                    <button class="btn btn-sm btn-light text-gray-500 border hover:bg-gray-50" 
                                            data-bs-toggle="modal" data-bs-target="#modalMhs{{ $s->id }}" title="Lihat Detail">
                                        <i class="bi bi-person-vcard"></i>
                                    </button>
                                    
                                    {{-- Tombol Kick (Opsional) --}}
                                    @if(request('kelas_id'))
                                        <form action="{{ url('/dosen/kick-student') }}" method="POST" onsubmit="return confirm('Keluarkan mahasiswa ini dari kelas? Progress akan dihapus.')">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $s->id }}">
                                            <input type="hidden" name="course_id" value="{{ request('kelas_id') }}">
                                            <button class="btn btn-sm btn-light text-red-500 border-red-100 hover:bg-red-50" title="Keluarkan dari Kelas">
                                                <i class="bi bi-person-x"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL DETAIL MAHASISWA --}}
                        <div class="modal fade" id="modalMhs{{ $s->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content border-0 shadow-lg rounded-xl overflow-hidden">
                                    {{-- Header Warna --}}
                                    <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-600 relative">
                                        <button type="button" class="btn-close btn-close-white absolute top-3 right-3" data-bs-dismiss="modal"></button>
                                    </div>
                                    
                                    {{-- Foto Profil (Overlap) --}}
                                    <div class="text-center -mt-12 relative px-4">
                                        <img src="{{ $s->foto_profil && $s->foto_profil != 'default.png' ? asset('storage/profiles/' . $s->foto_profil) : asset('images/logo_ukit.png') }}" 
                                             class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover mx-auto bg-white"
                                             onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                                        
                                        <h5 class="font-bold text-gray-800 mt-2 mb-0">{{ $s->nama_lengkap }}</h5>
                                        {{-- PERBAIKAN: NIM Muncul Disini --}}
                                        <p class="text-indigo-600 font-bold font-mono text-sm bg-indigo-50 rounded-pill px-3 py-1 d-inline-block mt-1 mb-0">
                                            {{ $s->nim_nidn ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="p-4 pt-2">
                                        <hr class="border-gray-100 my-3">
                                        
                                        {{-- List Kelas (Pengganti Email/Telp) --}}
                                        <div class="text-start">
                                            <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 d-block">Kelas yang Diikuti</label>
                                            @if($s->courses_taken->count() > 0)
                                                <div class="d-flex flex-column gap-2">
                                                    @foreach($s->courses_taken as $mk)
                                                        <div class="d-flex align-items-center gap-2 p-2 rounded bg-gray-50 border border-gray-100">
                                                            <div class="w-8 h-8 rounded bg-white flex items-center justify-center text-blue-600 border shadow-sm">
                                                                <i class="bi bi-journal-bookmark-fill"></i>
                                                            </div>
                                                            <span class="text-xs font-bold text-gray-700">{{ $mk->nama_mk }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-xs text-gray-400 italic text-center py-2">Belum ada data kelas.</p>
                                            @endif
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-light w-100 text-sm font-bold text-gray-600" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="d-inline-block p-4 rounded-full bg-gray-50 mb-3">
                                    <i class="bi bi-people text-3xl text-gray-300"></i>
                                </div>
                                <h6 class="text-gray-600 font-bold text-sm">Belum Ada Mahasiswa</h6>
                                <p class="text-gray-400 text-xs">Coba pilih kelas lain atau tunggu mahasiswa mendaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="p-3 border-top bg-gray-50">
            {{ $students->links() }}
        </div>
    </div>

@endsection