@extends('layouts.app')

@section('title', 'Dashboard - E-Learning')

@section('content')
    {{-- HEADER SAMBUTAN (Simpel) --}}
    <div class="text-center mb-5">
        <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" class="mx-auto w-16 md:w-20 mb-3 drop-shadow-sm">
        <h2 class="font-bold text-xl md:text-2xl text-gray-800 uppercase tracking-wide">
            Selamat Datang Di E-Learning
        </h2>
        <h3 class="font-medium text-sm md:text-lg text-gray-500 mt-1">
            Universitas Kristen Indonesia Toraja
        </h3>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">

            {{-- CARD INFORMASI TERBARU (Database Dynamic) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Header Card --}}
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 d-flex justify-content-between align-items-center">
                    <h5 class="font-bold text-gray-700 m-0 text-sm md:text-base flex items-center gap-2">
                        <i class="bi bi-megaphone-fill text-yellow-500"></i> Papan Informasi
                    </h5>
                    <span class="text-[10px] text-gray-400">Terupdate</span>
                </div>

                {{-- List Pengumuman --}}
                <div class="p-0">
                    @forelse($announcements as $info)
                        {{-- Logika Warna Berdasarkan Tipe di Database --}}
                        @php
                            $borderClass = 'border-l-4';
                            $bgIcon = '';
                            $icon = '';

                            if($info->tipe == 'penting') {
                                // Tipe Penting: Warna Kuning Emas (Sesuai Tema)
                                $borderColor = 'border-yellow-400';
                                $iconColor = 'text-yellow-600';
                                $icon = 'bi-star-fill';
                            } elseif($info->tipe == 'libur') {
                                // Tipe Libur/Danger: Warna Merah
                                $borderColor = 'border-red-500';
                                $iconColor = 'text-red-600';
                                $icon = 'bi-exclamation-circle-fill';
                            } else {
                                // Tipe Info Biasa: Warna Biru
                                $borderColor = 'border-blue-500';
                                $iconColor = 'text-blue-600';
                                $icon = 'bi-info-circle-fill';
                            }
                        @endphp

                        <div class="p-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors {{ $borderClass }} {{ $borderColor }}">
                            <div class="d-flex align-items-start gap-3">
                                {{-- Ikon --}}
                                <div class="mt-1">
                                    <i class="bi {{ $icon }} {{ $iconColor }} text-lg"></i>
                                </div>

                                {{-- Konten Teks --}}
                                <div class="flex-grow-1">
                                    <h6 class="font-bold text-gray-800 text-sm md:text-base mb-1">
                                        {{ $info->judul }}
                                    </h6>
                                    <p class="text-xs md:text-sm text-gray-600 leading-relaxed mb-0">
                                        {{ $info->isi }}
                                    </p>
                                    <small class="text-[10px] text-gray-400 mt-2 d-block">
                                        Diposting: {{ \Carbon\Carbon::parse($info->created_at)->isoFormat('D MMMM Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- Jika Database Kosong --}}
                        <div class="p-5 text-center">
                            <i class="bi bi-clipboard-x text-gray-300 text-4xl mb-2 d-block"></i>
                            <span class="text-sm text-gray-400">Tidak ada informasi terbaru.</span>
                        </div>
                    @endforelse
                </div>

            </div>

        </div>
    </div>
@endsection
