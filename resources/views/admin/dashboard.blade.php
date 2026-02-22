@extends('layouts.admin')

@section('title', 'Dashboard Analytics')

@push('styles')
    {{-- Memanggil ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        .apexcharts-tooltip {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
        }
    </style>
@endpush

@section('content')
    
    {{-- HEADER ANALITIK PROFESIONAL --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="font-bold text-gray-800 mb-1 tracking-tight">Overview Analytics</h3>
            <p class="text-gray-500 text-sm m-0">Ringkasan performa dan metrik utama sistem e-learning.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-gray-600 border border-gray-200 shadow-sm px-3 py-2 rounded-pill text-xs font-medium d-flex align-items-center">
                <i class="bi bi-calendar3 text-blue-500 me-2"></i> 
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </span>
            <span class="badge bg-green-50 text-green-600 border border-green-200 shadow-sm px-3 py-2 rounded-pill text-xs font-bold d-flex align-items-center">
                <i class="bi bi-circle-fill text-[8px] me-2 animate-pulse"></i> System Normal
            </span>
        </div>
    </div>

    {{-- STATISTIK CARDS (KEY PERFORMANCE INDICATORS) - Tanpa Efek Hover --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 h-100 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <h3 class="font-bold text-3xl text-gray-800 m-0 leading-none">{{ number_format($total_mhs) }}</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-2 mb-0">Total Mahasiswa</p>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 h-100 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 text-xl">
                        <i class="bi bi-person-video3"></i>
                    </div>
                </div>
                <h3 class="font-bold text-3xl text-gray-800 m-0 leading-none">{{ number_format($total_dosen) }}</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-2 mb-0">Total Dosen</p>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 h-100 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-600 text-xl">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                </div>
                <h3 class="font-bold text-3xl text-gray-800 m-0 leading-none">{{ number_format($total_konsentrasi) }}</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-2 mb-0">Konsentrasi / Prodi</p>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 h-100 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600 text-xl">
                        <i class="bi bi-book-half"></i>
                    </div>
                </div>
                <h3 class="font-bold text-3xl text-gray-800 m-0 leading-none">{{ number_format($total_mk) }}</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-2 mb-0">Mata Kuliah Aktif</p>
            </div>
        </div>
    </div>

    {{-- BARIS GRAFIK 1: Trend & Komposisi User --}}
    <div class="row g-3 mb-4">
        {{-- Area Chart: Tren Pendaftaran --}}
        <div class="col-12 col-lg-8">
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="font-bold text-gray-800 mb-0">Tren Pertumbuhan Pengguna</h6>
                        <p class="text-[11px] text-gray-400 mb-0">Statistik pendaftaran 6 bulan terakhir</p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                        <i class="bi bi-graph-up text-sm"></i>
                    </div>
                </div>
                <div id="chartTrend" class="chart-container"></div>
            </div>
        </div>

        {{-- Donut Chart: Komposisi Pengguna --}}
        <div class="col-12 col-lg-4">
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="font-bold text-gray-800 mb-0">Komposisi Entitas</h6>
                        <p class="text-[11px] text-gray-400 mb-0">Rasio pembagian hak akses</p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                        <i class="bi bi-pie-chart-fill text-sm"></i>
                    </div>
                </div>
                <div id="chartUsers" class="d-flex justify-content-center align-items-center" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    {{-- BARIS GRAFIK 2: Distribusi Kurikulum & Tipe Materi --}}
    <div class="row g-3 pb-4">
        {{-- Bar Chart: Distribusi Mata Kuliah per Prodi --}}
        <div class="col-12 col-lg-8">
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="font-bold text-gray-800 mb-0">Distribusi Mata Kuliah</h6>
                        <p class="text-[11px] text-gray-400 mb-0">Perbandingan kepadatan kurikulum tiap Prodi</p>
                    </div>
                    <a href="{{ url('/admin/kurikulum') }}" class="btn btn-sm btn-light text-[11px] font-bold text-gray-500 rounded-lg hover:text-gray-800">
                        Kelola Kurikulum <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div id="chartProdi" class="chart-container"></div>
            </div>
        </div>

        {{-- Pie Chart: Analisis Tipe Konten --}}
        <div class="col-12 col-lg-4">
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="font-bold text-gray-800 mb-0">Rasio Tipe Materi</h6>
                        <p class="text-[11px] text-gray-400 mb-0">Metode pembelajaran (Video vs Evaluasi)</p>
                    </div>
                </div>
                <div id="chartMateri" class="d-flex justify-content-center align-items-center" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {

    // Konfigurasi Global ApexCharts agar font konsisten dengan layout
    const commonOptions = {
        fontFamily: 'Poppins, sans-serif',
        toolbar: { show: false }
    };

    // 1. CHART TREN PENDAFTARAN (Area Chart)
    const trendOptions = {
        series: [
            { name: 'Mahasiswa', data: @json($chart_trend['mahasiswa']) },
            { name: 'Dosen', data: @json($chart_trend['dosen']) }
        ],
        chart: { 
            type: 'area', 
            height: 300, 
            toolbar: { show: false }, 
            zoom: { enabled: false },
            fontFamily: 'Poppins, sans-serif'
        },
        colors: ['#3b82f6', '#8b5cf6'], // Blue & Purple
        fill: { 
            type: 'gradient', 
            gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] } 
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: { 
            categories: @json($chart_trend['labels']),
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 } }
        },
        yaxis: { 
            labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 } } 
        },
        legend: { position: 'top', horizontalAlign: 'right', fontWeight: 600, fontSize: '12px' },
        grid: { borderColor: '#f8fafc', strokeDashArray: 4 }
    };
    new ApexCharts(document.querySelector("#chartTrend"), trendOptions).render();

    // 2. CHART KOMPOSISI PENGGUNA (Donut Chart)
    const usersOptions = {
        series: [@json($chart_users['mahasiswa']), @json($chart_users['dosen']), @json($chart_users['admin'])],
        labels: ['Mahasiswa', 'Dosen', 'Admin'],
        chart: { type: 'donut', height: 290, fontFamily: 'Poppins, sans-serif' },
        colors: ['#3b82f6', '#8b5cf6', '#eab308'], // Blue, Purple, Yellow
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        name: { fontSize: '11px', color: '#64748b', fontWeight: 600 },
                        value: { fontSize: '28px', fontWeight: 'bold', color: '#1e293b' },
                        total: { show: true, label: 'Total Pengguna', color: '#94a3b8', fontWeight: 600 }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        legend: { position: 'bottom', fontWeight: 500, fontSize: '12px', markers: { radius: 12 } },
        stroke: { width: 0 }
    };
    new ApexCharts(document.querySelector("#chartUsers"), usersOptions).render();

    // 3. CHART DISTRIBUSI MATERI PER PRODI (Bar Chart)
    const prodiOptions = {
        series: [{ name: 'Jumlah MK', data: @json($chart_prodi['data']) }],
        chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Poppins, sans-serif' },
        colors: ['#eab308'], // Yellow (Sesuai tema Admin)
        plotOptions: {
            bar: { borderRadius: 4, columnWidth: '35%', distributed: true }
        },
        dataLabels: { enabled: false },
        xaxis: { 
            categories: @json($chart_prodi['labels']),
            labels: { 
                style: { colors: '#64748b', fontSize: '11px', fontWeight: 500 },
                trim: true,
                hideOverlappingLabels: false
            }
        },
        yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 } } },
        grid: { borderColor: '#f8fafc', strokeDashArray: 4 },
        legend: { show: false } // Sembunyikan legend karena sudah distributed
    };
    new ApexCharts(document.querySelector("#chartProdi"), prodiOptions).render();

    // 4. CHART TIPE MATERI (Pie Chart)
    const materiOptions = {
        series: [@json($chart_materi['video']), @json($chart_materi['kuis']), @json($chart_materi['tugas'])],
        labels: ['Video Pasif', 'Kuis/Ujian', 'Tugas / Link'],
        chart: { type: 'pie', height: 280, fontFamily: 'Poppins, sans-serif' },
        colors: ['#3b82f6', '#eab308', '#22c55e'], // Blue, Yellow, Green
        dataLabels: { 
            enabled: true, 
            style: { fontSize: '11px', fontWeight: 'bold' },
            dropShadow: { enabled: false }
        },
        legend: { position: 'bottom', fontWeight: 500, fontSize: '12px', markers: { radius: 12 } },
        stroke: { width: 3, colors: ['#ffffff'] }
    };
    new ApexCharts(document.querySelector("#chartMateri"), materiOptions).render();

});
</script>
@endpush