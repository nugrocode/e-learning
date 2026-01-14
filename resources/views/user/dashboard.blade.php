@extends('layouts.app')

@section('title', 'Dashboard - E-Learning')

@section('content')
    <div class="text-center mb-5 animate-fade-in-up">
        <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo" class="mx-auto w-20 md:w-24 mb-4 drop-shadow-md">

        {{-- Judul Responsif --}}
        <h2 class="font-bold text-xl md:text-3xl text-gray-800 uppercase tracking-wide">
            Selamat Datang Di Pembelajaran Elektronik
        </h2>
        <h3 class="font-semibold text-sm md:text-2xl text-gray-600 mt-1">
            Universitas Kristen Indonesia Toraja
        </h3>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="d-flex justify-content-between align-items-center mb-4 border-b pb-2">
                    <h5 class="font-bold text-gray-700 flex items-center gap-2 text-sm md:text-lg">
                        <i class="bi bi-info-circle-fill text-blue-600"></i> INFORMASI TERBARU
                    </h5>
                    <span class="text-[10px] md:text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">Update Terkini</span>
                </div>

                {{-- Item Info --}}
                <div class="bg-yellow-50 p-3 md:p-4 rounded-lg border-l-4 border-yellow-400 mb-3">
                    <h6 class="font-bold text-gray-800 text-sm md:text-base">Jadwal Pengisian KRS</h6>
                    <p class="text-xs md:text-sm text-gray-600 mt-1">Pengisian KRS dimulai 20 Oktober 2025.</p>
                </div>

                <div class="bg-blue-50 p-3 md:p-4 rounded-lg border-l-4 border-blue-600">
                    <h6 class="font-bold text-gray-800 text-sm md:text-base">Maintenance Server</h6>
                    <p class="text-xs md:text-sm text-gray-600 mt-1">Sabtu pukul 23:00 WITA.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
