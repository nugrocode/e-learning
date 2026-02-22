@extends('layouts.dosen')

@section('title', 'Dashboard Analytics Dosen')

@section('content')
    <style>
        .chart-container { position: relative; height: 300px; width: 100%; }
        .apexcharts-tooltip {
            background: #ffffff; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
        }
        .stat-card {
            border-radius: 1rem; background: #fff; border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
    </style>

    {{-- HEADER ANALITIK PROFESIONAL --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="font-bold text-gray-800 mb-1 tracking-tight">Akademik & Evaluasi</h3>
            <p class="text-gray-500 text-sm m-0">Ringkasan performa kelas dan keaktifan mahasiswa Anda.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-gray-600 border border-gray-200 shadow-sm px-3 py-2 rounded-pill text-xs font-medium d-flex align-items-center">
                <i class="bi bi-calendar3 text-yellow-500 me-2"></i> 
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    {{-- STATISTIK CARDS (KPI DOSEN) --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="stat-card p-4 h-100 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                </div>
                <h3 class="font-bold text-3xl text-gray-800 m-0 leading-none">{{ number_format($total_kelas) }}</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-2 mb-0">Kelas Diampu</p>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="stat-card p-4 h-100 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600 text-xl">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <h3 class="font-bold text-3xl text-gray-800 m-0 leading-none">{{ number_format($total_mhs) }}</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-2 mb-0">Total Mahasiswa</p>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="stat-card p-4 h-100 d-flex flex-column justify-content-center relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-xl z-10">
                        <i class="bi bi-inbox-fill"></i>
                    </div>
                    @if($belum_dinilai > 0)
                        <span class="badge bg-red-500 text-white text-[10px] px-2 py-1 rounded-pill shadow-sm z-10 animate-pulse">Perlu Aksi</span>
                    @endif
                </div>
                <h3 class="font-bold text-3xl {{ $belum_dinilai > 0 ? 'text-red-600' : 'text-gray-800' }} m-0 leading-none z-10">{{ number_format($belum_dinilai) }}</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-2 mb-0 z-10">Tugas Pending</p>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="stat-card p-4 h-100 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-600 text-xl">
                        <i class="bi bi-chat-dots-fill"></i>
                    </div>
                </div>
                <h3 class="font-bold text-3xl text-gray-800 m-0 leading-none">{{ number_format($total_diskusi) }}</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mt-2 mb-0">Total Diskusi</p>
            </div>
        </div>
    </div>

    {{-- BARIS GRAFIK 1: Tren Keaktifan & Status Penilaian --}}
    <div class="row g-3 mb-4">
        {{-- Area Chart: Tren Keaktifan --}}
        <div class="col-12 col-lg-8">
            <div class="stat-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="font-bold text-gray-800 mb-0">Tren Keaktifan Mahasiswa</h6>
                        <p class="text-[11px] text-gray-400 mb-0">Frekuensi pengumpulan tugas dan pertanyaan (6 Bulan)</p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                        <i class="bi bi-activity text-sm"></i>
                    </div>
                </div>
                <div id="chartTrend" class="chart-container"></div>
            </div>
        </div>

        {{-- Donut Chart: Status Penilaian Tugas --}}
        <div class="col-12 col-lg-4">
            <div class="stat-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="font-bold text-gray-800 mb-0">Status Penilaian Tugas</h6>
                        <p class="text-[11px] text-gray-400 mb-0">Progress pemeriksaan tugas</p>
                    </div>
                    <a href="{{ url('/dosen/tugas') }}" class="w-8 h-8 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-600 hover:bg-yellow-100 transition">
                        <i class="bi bi-arrow-right text-sm"></i>
                    </a>
                </div>
                <div id="chartPenilaian" class="d-flex justify-content-center align-items-center" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    {{-- BARIS GRAFIK 2: Kepadatan Materi & Tipe Konten --}}
    <div class="row g-3 pb-4">
        {{-- Bar Chart: Kepadatan Materi --}}
        <div class="col-12 col-lg-8">
            <div class="stat-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="font-bold text-gray-800 mb-0">Kepadatan Materi per Kelas</h6>
                        <p class="text-[11px] text-gray-400 mb-0">Perbandingan jumlah materi di tiap mata kuliah</p>
                    </div>
                    <a href="{{ url('/dosen/materi') }}" class="btn btn-sm btn-light text-[11px] font-bold text-gray-500 rounded-lg hover:text-gray-800">
                        Kelola Materi <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div id="chartKepadatan" class="chart-container"></div>
            </div>
        </div>

        {{-- Pie Chart: Analisis Tipe Konten Dosen --}}
        <div class="col-12 col-lg-4">
            <div class="stat-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="font-bold text-gray-800 mb-0">Gaya Mengajar Anda</h6>
                        <p class="text-[11px] text-gray-400 mb-0">Rasio metode pembelajaran</p>
                    </div>
                </div>
                <div id="chartMateri" class="d-flex justify-content-center align-items-center" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const commonOptions = { fontFamily: 'Poppins, sans-serif', toolbar: { show: false } };

    // 1. CHART TREN KEAKTIFAN (Area Chart)
    const trendOptions = {
        series: [
            { name: 'Tugas Terkumpul', data: @json($chart_trend['tugas']) },
            { name: 'Diskusi Masuk', data: @json($chart_trend['diskusi']) }
        ],
        chart: { type: 'area', height: 300, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Poppins, sans-serif' },
        colors: ['#eab308', '#3b82f6'], 
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] } },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: { 
            categories: @json($chart_trend['labels']),
            axisBorder: { show: false }, axisTicks: { show: false },
            labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 } }
        },
        yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 } } },
        legend: { position: 'top', horizontalAlign: 'right', fontWeight: 600, fontSize: '12px' },
        grid: { borderColor: '#f8fafc', strokeDashArray: 4 }
    };
    new ApexCharts(document.querySelector("#chartTrend"), trendOptions).render();

    // 2. CHART STATUS PENILAIAN (Donut Chart)
    const penilaianOptions = {
        series: [@json($chart_penilaian['dinilai']), @json($chart_penilaian['pending'])],
        labels: ['Sudah Dinilai', 'Menunggu Penilaian'],
        chart: { type: 'donut', height: 290, fontFamily: 'Poppins, sans-serif' },
        colors: ['#22c55e', '#ef4444'], 
        plotOptions: {
            pie: { donut: { size: '75%', labels: { show: true, name: { fontSize: '11px', color: '#64748b', fontWeight: 600 }, value: { fontSize: '28px', fontWeight: 'bold', color: '#1e293b' }, total: { show: true, label: 'Total Tugas', color: '#94a3b8', fontWeight: 600 } } } }
        },
        dataLabels: { enabled: false },
        legend: { position: 'bottom', fontWeight: 500, fontSize: '12px', markers: { radius: 12 } }, stroke: { width: 0 }
    };
    new ApexCharts(document.querySelector("#chartPenilaian"), penilaianOptions).render();

    // 3. CHART KEPADATAN MATERI (Bar Chart)
    const kepadatanOptions = {
        series: [{ name: 'Total Materi', data: @json($chart_kepadatan['data']) }],
        chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Poppins, sans-serif' },
        colors: ['#eab308'], 
        plotOptions: { bar: { borderRadius: 4, columnWidth: '35%', distributed: true } },
        dataLabels: { enabled: false },
        xaxis: { 
            categories: @json($chart_kepadatan['labels']),
            labels: { style: { colors: '#64748b', fontSize: '10px', fontWeight: 500 }, trim: true, hideOverlappingLabels: false }
        },
        yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 } } },
        grid: { borderColor: '#f8fafc', strokeDashArray: 4 }, legend: { show: false } 
    };
    new ApexCharts(document.querySelector("#chartKepadatan"), kepadatanOptions).render();

    // 4. CHART TIPE MATERI DOSEN (Pie Chart) - NAMA LABEL DAN SUSUNAN DIPERBAIKI
    const materiOptions = {
        series: [@json($chart_tipe['video']), @json($chart_tipe['tugas']), @json($chart_tipe['kuis'])],
        labels: ['Video (Pasif)', 'Video + Tugas', 'Soal Kuis'],
        chart: { type: 'pie', height: 280, fontFamily: 'Poppins, sans-serif' },
        colors: ['#3b82f6', '#22c55e', '#eab308'], // Blue=Video Pasif, Green=Video+Tugas, Yellow=Kuis
        dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 'bold' }, dropShadow: { enabled: false } },
        legend: { position: 'bottom', fontWeight: 500, fontSize: '12px', markers: { radius: 12 } }, stroke: { width: 3, colors: ['#ffffff'] }
    };
    new ApexCharts(document.querySelector("#chartMateri"), materiOptions).render();

});
</script>
@endpush