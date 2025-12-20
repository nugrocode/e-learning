@extends('layouts.app')

@section('title', 'Jalur Belajar')

@section('content')
    <div class="mb-5 border-b border-gray-300 pb-3 animate-fade-in-up">
        <h2 class="font-bold text-2xl text-gray-800">Pilih Konsentrasi</h2>
        <p class="text-gray-600">Pilih jalur minatmu untuk melihat Mata Kuliah yang tersedia.</p>
    </div>

    <div class="row g-4">
        @forelse($concentrations as $item)
            <div class="col-12 col-md-6 col-lg-4">
                <a href="{{ url('/mata-kuliah/' . $item->id) }}" class="text-decoration-none">
                    <div class="card-hover p-4 shadow-sm">

                        <i class="bi bi-cpu-fill icon-bg text-blue-900"></i>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-blue-100 p-3 rounded-full text-blue-900">
                                <i class="bi bi-layers-fill text-2xl"></i>
                            </div>
                            <h5 class="font-bold text-xl text-gray-800 ms-3">
                                {{ $item->nama_konsentrasi }}
                            </h5>
                        </div>

                        <p class="text-sm text-gray-500 mb-4 flex-grow-1">
                            {{ $item->deskripsi ?? 'Pelajari materi dasar hingga lanjut secara terstruktur.' }}
                        </p>

                        <div class="mt-auto">
                            <span class="text-sm font-semibold text-blue-800 hover:text-blue-600 flex items-center gap-1">
                                Lihat Kurikulum <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="bg-white p-5 rounded-xl shadow-sm d-inline-block">
                    <i class="bi bi-inbox text-5xl text-gray-300"></i>
                    <h5 class="mt-3 text-gray-600 font-semibold">Belum Ada Konsentrasi</h5>
                    <p class="text-sm text-gray-400">Silakan hubungi admin untuk menambahkan data.</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection
