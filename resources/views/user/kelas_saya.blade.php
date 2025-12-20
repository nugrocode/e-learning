@extends('layouts.app')

@section('title', 'Kelas Saya')

@section('content')
    <div class="mb-5 border-b border-gray-300 pb-3">
        <h2 class="font-bold text-2xl text-gray-800">Kelas Saya</h2>
        <p class="text-gray-600">Lanjutkan pembelajaran di kelas yang sedang Anda ikuti.</p>
    </div>

    <div class="row g-4">
        @forelse($courses as $mk)
            @php
                $progress_color = ($mk->persen == 100) ? 'bg-green-500' : 'bg-yellow-400';
            @endphp

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-hover p-4 h-100 shadow-sm border rounded-xl bg-white" style="border-left: 5px solid #2d3748;">

                    <div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-blue-100 text-blue-800 rounded-pill px-3 py-1 text-xs font-bold">Wajib</span>
                            <small class="text-gray-400 font-medium">{{ \App\Models\Material::where('course_id', $mk->id)->count() }} Materi</small>
                        </div>

                        <h5 class="font-bold text-lg text-gray-800 mb-2 leading-tight">{{ $mk->nama_mk }}</h5>

                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">
                            {{ $mk->deskripsi ?? 'Pelajari materi ini untuk meningkatkan keahlian Anda.' }}
                        </p>
                    </div>

                    <div class="mt-auto">
                        <div class="d-flex justify-content-between text-xs text-gray-500 mb-1">
                            <span>Progres Belajar</span>
                            <span class="font-bold text-gray-700">{{ $mk->persen }}%</span>
                        </div>

                        <div class="progress progress-height bg-gray-100 mb-4" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar {{ $progress_color }}" role="progressbar" style="width: {{ $mk->persen }}%"></div>
                        </div>

                        <a href="{{ url('/belajar/' . $mk->id . '/' . $mk->next_urutan) }}" class="btn btn-dark w-100 rounded-lg py-2.5 text-sm font-bold hover:bg-gray-800 transition shadow-sm">
                            @if($mk->persen == 100)
                                Ulangi Materi <i class="bi bi-arrow-repeat ms-1"></i>
                            @else
                                Lanjutkan Belajar <i class="bi bi-box-arrow-in-right ms-1"></i>
                            @endif
                        </a>
                    </div>

                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="bg-white p-5 rounded-xl shadow-sm d-inline-block max-w-md">
                    <i class="bi bi-journal-bookmark text-6xl text-gray-300 mb-3 block"></i>
                    <h5 class="font-bold text-gray-700">Belum Ada Kelas</h5>
                    <p class="text-gray-500 text-sm mb-4">Anda belum memulai pembelajaran apapun. Yuk pilih jalur belajar sekarang!</p>
                    <a href="{{ url('/jalur-belajar') }}" class="btn btn-primary bg-blue-900 text-white px-4 py-2 rounded-lg">
                        Mulai Belajar <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        @endforelse
    </div>
@endsection
