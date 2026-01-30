@extends('layouts.app')

@section('title', 'Dashboard - E-Learning')

@section('content')
    {{-- HEADER SAMBUTAN --}}
    <div class="text-center mb-5 animate-fade-in-up">
        <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" class="mx-auto w-20 md:w-24 mb-4 drop-shadow-md">
        <h2 class="font-extrabold text-2xl md:text-3xl text-gray-800 uppercase tracking-wide mb-2">
            Selamat Datang Di E-Learning
        </h2>
        <h3 class="font-medium text-base md:text-lg text-gray-500">
            Universitas Kristen Indonesia Toraja
        </h3>
    </div>

    {{-- CONTENT AREA --}}
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">

            {{-- CARD INFORMASI TERBARU --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Header Card --}}
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 d-flex justify-content-between align-items-center">
                    <h5 class="font-bold text-gray-800 m-0 text-base flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center text-sm shadow-sm">
                            <i class="bi bi-megaphone-fill"></i>
                        </span>
                        Papan Informasi
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Terupdate</span>
                    </div>
                </div>

                {{-- List Pengumuman --}}
                <div class="divide-y divide-gray-100">
                    @forelse($announcements as $info)
                        @php
                            // Styling Icon & Badge
                            $bgIcon = 'bg-blue-50';
                            $textIcon = 'text-blue-600';
                            $icon = 'bi-info-circle-fill';
                            $badgeLabel = 'Info';
                            $badgeClass = 'bg-blue-100 text-blue-700';

                            if($info->tipe == 'penting') {
                                $bgIcon = 'bg-yellow-50';
                                $textIcon = 'text-yellow-600';
                                $icon = 'bi-star-fill';
                                $badgeLabel = 'Penting';
                                $badgeClass = 'bg-yellow-100 text-yellow-700';
                            } elseif($info->tipe == 'libur') {
                                $bgIcon = 'bg-red-50';
                                $textIcon = 'text-red-600';
                                $icon = 'bi-exclamation-triangle-fill';
                                $badgeLabel = 'Perhatian';
                                $badgeClass = 'bg-red-100 text-red-700';
                            }
                        @endphp

                        {{-- Item Pengumuman (Compact) --}}
                        <div class="p-3 md:p-4 bg-white hover:bg-gray-50 transition duration-200">
                            <div class="d-flex align-items-start gap-3">
                                
                                {{-- Ikon Box (Lebih Kecil) --}}
                                <div class="shrink-0 mt-1">
                                    <div class="w-9 h-9 rounded-lg {{ $bgIcon }} {{ $textIcon }} flex items-center justify-center text-lg shadow-sm">
                                        <i class="bi {{ $icon }}"></i>
                                    </div>
                                </div>

                                {{-- Konten Teks --}}
                                <div class="flex-grow-1 min-w-0"> {{-- min-w-0 penting untuk text-truncate --}}
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="font-bold text-gray-800 text-sm md:text-base m-0 text-truncate pe-2">
                                            {{ $info->judul }}
                                        </h6>
                                        <span class="badge {{ $badgeClass }} rounded-pill px-2 py-0.5 text-[9px] uppercase tracking-wider border border-white shadow-sm shrink-0">
                                            {{ $badgeLabel }}
                                        </span>
                                    </div>
                                    
                                    {{-- Text Limit 2 Baris --}}
                                    <p class="text-xs md:text-sm text-gray-600 leading-snug mb-2 line-clamp-2">
                                        {{ $info->isi }}
                                    </p>

                                    {{-- Footer Item: Tanggal & Tombol Baca --}}
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2 text-[10px] text-gray-400 font-medium">
                                            <span><i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($info->created_at)->isoFormat('D MMM Y') }}</span>
                                            <span class="text-gray-300">•</span>
                                            <span><i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($info->created_at)->format('H:i') }}</span>
                                        </div>
                                        
                                        {{-- Tombol Trigger Modal --}}
                                        <button class="btn btn-sm btn-link text-decoration-none p-0 text-[11px] font-bold text-blue-600 hover:text-blue-800"
                                            data-bs-toggle="modal" data-bs-target="#infoModal{{ $info->id }}">
                                            Baca Selengkapnya <i class="bi bi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL DETAIL PENGUMUMAN (Pop Up) --}}
                        <div class="modal fade" id="infoModal{{ $info->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-xl">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="w-8 h-8 rounded-full {{ $bgIcon }} {{ $textIcon }} flex items-center justify-center">
                                                <i class="bi {{ $icon }}"></i>
                                            </div>
                                            <h5 class="modal-title font-bold text-gray-800 text-base">{{ $badgeLabel }}</h5>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body pt-3 pb-4 px-4">
                                        <h4 class="font-bold text-lg text-gray-900 mb-2">{{ $info->judul }}</h4>
                                        <div class="text-xs text-gray-400 mb-3 pb-3 border-b border-dashed">
                                            <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($info->created_at)->isoFormat('dddd, D MMMM Y') }} 
                                            at {{ \Carbon\Carbon::parse($info->created_at)->format('H:i') }} WITA
                                        </div>
                                        <div class="text-sm text-gray-700 leading-relaxed text-justify whitespace-pre-line">
                                            {{ $info->isi }}
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                                        <button type="button" class="btn btn-light btn-sm w-100 font-bold text-gray-600" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        {{-- Empty State --}}
                        <div class="p-8 text-center bg-gray-50">
                            <div class="d-inline-block p-3 rounded-full bg-white shadow-sm mb-2">
                                <i class="bi bi-clipboard-check text-gray-300 text-3xl"></i>
                            </div>
                            <h6 class="text-gray-600 font-bold text-sm">Belum ada informasi</h6>
                        </div>
                    @endforelse
                </div>

            </div>
            
            {{-- Footer Kecil --}}
            <div class="text-center mt-5 text-gray-400 text-xs">
                &copy; {{ date('Y') }} E-Learning UKI Toraja. All rights reserved.
            </div>

        </div>
    </div>
@endsection