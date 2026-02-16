@extends('layouts.dosen')

@section('title', 'Susun Materi')

@section('content')
<div class="mb-5">
    <h2 class="font-bold text-2xl text-gray-800">Susun Materi</h2>
    <p class="text-gray-500">Pilih kelas yang ingin Anda kelola kurikulumnya.</p>
</div>

<div class="row g-4">
    @forelse($courses as $c)
        <div class="col-md-6 col-lg-4">
            {{-- Klik Card langsung ke halaman Susun Materi --}}
            <div class="card border-0 shadow-sm h-100 rounded-xl overflow-hidden hover:shadow-lg transition cursor-pointer" 
                 onclick="window.location='{{ url('/dosen/materi/'.$c->id) }}'">
                
                {{-- Gambar Kelas --}}
                <div class="h-40 overflow-hidden relative">
                    <img src="{{ $c->gambar ? asset('storage/thumbnails/'.$c->gambar) : asset('images/default_course.jpg') }}" 
                         class="w-full h-full object-cover transform hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-3 left-3 text-white">
                        <h5 class="font-bold text-lg mb-0 shadow-sm">{{ $c->nama_mk }}</h5>
                        <span class="text-xs opacity-90">{{ $c->materials_count }} Materi</span>
                    </div>
                </div>

                <div class="card-body d-flex justify-content-between align-items-center">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kelola Kurikulum</span>
                    <i class="bi bi-arrow-right-circle-fill text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-gray-300 mb-3"><i class="bi bi-journal-x text-5xl"></i></div>
            <p class="text-gray-500">Belum ada kelas ajar.</p>
        </div>
    @endforelse
</div>
@endsection