@extends('layouts.app')

@section('title', 'Dashboard - E-Learning')

@section('content')
    <div class="text-center mb-5 animate-fade-in-up">
        <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo Besar"
            class="mx-auto w-24 mb-4 drop-shadow-md hover:scale-110 transition duration-500">
        <h2 class="font-bold text-2xl md:text-3xl text-gray-800 uppercase tracking-wide">
            Selamat Datang Di Pembelajaran Elektronik
        </h2>
        <h3 class="font-semibold text-xl md:text-2xl text-gray-600 mt-1">
            Universitas Kristen Indonesia Toraja
        </h3>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-hover p-4 shadow-inner bg-gray-200 border-none">

                <div class="d-flex justify-content-between align-items-center mb-3 border-b border-gray-400 pb-2">
                    <h5 class="font-bold text-gray-700 flex items-center gap-2">
                        <i class="bi bi-info-circle-fill text-blue-600"></i> INFORMASI TERBARU
                    </h5>
                    <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">Update Terkini</span>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm mb-3 border-l-4 border-yellow-400">
                    <h6 class="font-bold text-gray-800">Jadwal Pengisian KRS Semester Genap</h6>
                    <p class="text-sm text-gray-600 mt-1">
                        Pengisian KRS akan dimulai pada tanggal 20 Oktober 2025. Pastikan Anda telah menyelesaikan
                        administrasi.
                    </p>
                    <small class="text-gray-400 text-xs mt-2 block">Diposting: 23 Nov 2025</small>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-600">
                    <h6 class="font-bold text-gray-800">Maintenance Server</h6>
                    <p class="text-sm text-gray-600 mt-1">
                        Akan dilakukan pemeliharaan sistem pada hari Sabtu pukul 23:00 WITA. Mohon simpan pekerjaan Anda
                        sebelumnya.
                    </p>
                    <small class="text-gray-400 text-xs mt-2 block">Diposting: 21 Nov 2025</small>
                </div>

            </div>
        </div>
    </div>
@endsection
