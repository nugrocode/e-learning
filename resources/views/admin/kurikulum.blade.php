@extends('layouts.admin')

@section('title', 'Kelola Kurikulum')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="font-bold text-2xl text-gray-800">Manajemen Kurikulum</h2>
            <p class="text-gray-500 text-sm">Pilih Konsentrasi untuk mengatur mata kuliah.</p>
        </div>

        {{-- TOMBOL SAKTI: AI SMART DISTRIBUTE (IKON DIPERBARUI) --}}
        <form action="{{ url('/admin/kurikulum/auto-distribute') }}" method="POST" onsubmit="return confirm('AI akan memindai SEMUA mata kuliah dan mendistribusikannya ke Konsentrasi yang relevan secara otomatis. Lanjutkan?')">
            @csrf
            <button type="submit" class="btn btn-warning text-yellow-900 font-bold shadow-sm hover:bg-yellow-400 transition px-4 py-2 d-flex align-items-center gap-2">
                {{-- GANTI IKON MENJADI MAGIC STARS --}}
                <i class="bi bi-stars"></i> AI Smart Distribute All
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-lg d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-xl"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        @forelse($konsentrasi as $item)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 d-flex flex-column hover:shadow-md transition-shadow">
                    
                    {{-- HEADER GAMBAR (FIX PATH STORAGE) --}}
                    <div class="relative h-40 bg-gray-100 overflow-hidden">
                        @if($item->gambar)
                            {{-- PERBAIKAN PATH: asset('storage/thumbnails/...') --}}
                            <img src="{{ asset('storage/thumbnails/' . $item->gambar) }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                <i class="bi bi-diagram-3 text-5xl opacity-50"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>
                        <div class="absolute bottom-3 right-3">
                            <span class="bg-white/90 backdrop-blur-sm text-xs font-bold px-2 py-1 rounded shadow-sm text-gray-800">
                                {{ $item->total_mk }} Mata Kuliah
                            </span>
                        </div>
                    </div>

                    {{-- KONTEN --}}
                    <div class="p-4 flex-grow-1 d-flex flex-column">
                        <h5 class="font-bold text-lg text-gray-800 mb-2">{{ $item->nama_konsentrasi }}</h5>
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4 leading-relaxed">
                            {{ $item->deskripsi ?? 'Kelola struktur pembelajaran untuk prodi ini.' }}
                        </p>

                        <div class="mt-auto border-t pt-3">
                            {{-- LINK TETAP SESUAI ASLINYA --}}
                            <a href="{{ url('/admin/mata-kuliah/' . $item->id) }}" class="btn btn-primary bg-blue-900 w-100 border-0 font-bold text-sm py-2 rounded-lg hover:bg-blue-800 transition">
                                <i class="bi bi-gear-wide-connected me-2"></i> Atur Kurikulum
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-exclamation-circle text-4xl text-gray-200 d-block mb-3"></i>
                <h5 class="text-gray-400 font-bold">Belum ada Prodi</h5>
            </div>
        @endforelse
    </div>
@endsection