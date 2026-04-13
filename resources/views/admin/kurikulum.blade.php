@extends('layouts.admin')

@section('title', 'Kelola Kurikulum')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="font-bold text-2xl text-gray-800">Manajemen Kurikulum</h2>
            <p class="text-gray-500 text-sm">Pilih Konsentrasi untuk mengatur mata kuliah.</p>
        </div>

        <form action="{{ url('/admin/kurikulum/auto-distribute') }}" method="POST" id="formDistribute" onsubmit="showLoading(event)">
            @csrf
            {{-- UPDATE WARNA: Tombol AI Smart Distribute All --}}
            <button type="submit" id="btnDistribute" class="btn font-bold shadow-sm transition px-4 py-2 d-flex align-items-center gap-2" 
                    style="background-color: #FACC15; color: #2d3748; border: none;">
                <i class="bi bi-stars" id="iconDistribute"></i> <span id="textDistribute">AI Smart Distribute All</span>
            </button>
        </form>
    </div>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-lg d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-xl"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ALERT ERROR --}}
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-lg d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-xl"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        @forelse($konsentrasi as $item)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-100 d-flex flex-column hover:shadow-md transition-shadow">
                    
                    <div class="relative h-40 bg-gray-100 overflow-hidden">
                        @if($item->gambar)
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

                    <div class="p-4 flex-grow-1 d-flex flex-column">
                        <h5 class="font-bold text-lg text-gray-800 mb-2">{{ $item->nama_konsentrasi }}</h5>
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4 leading-relaxed">
                            {{ $item->deskripsi ?? 'Kelola struktur pembelajaran untuk prodi ini.' }}
                        </p>

                        <div class="mt-auto border-t pt-3">
                            {{-- UPDATE WARNA: Tombol Atur Kurikulum --}}
                            <a href="{{ url('/admin/mata-kuliah/' . $item->id) }}" class="btn w-100 border-0 font-bold text-sm py-2 rounded-lg transition" 
                               style="background-color: #2d3748; color: #FACC15;">
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

@push('scripts')
<script>
    // Agar tombol menampilkan animasi saat proses loading yang lama
    function showLoading(e) {
        if(!confirm('AI akan memindai SEMUA mata kuliah dan mendistribusikannya secara otomatis. Proses ini memakan waktu sekitar 10-20 detik. Lanjutkan?')) {
            e.preventDefault();
            return false;
        }
        
        let btn = document.getElementById('btnDistribute');
        let icon = document.getElementById('iconDistribute');
        let text = document.getElementById('textDistribute');
        
        btn.classList.add('disabled', 'opacity-75');
        btn.style.pointerEvents = 'none';
        icon.classList.remove('bi-stars');
        icon.classList.add('spinner-border', 'spinner-border-sm');
        text.innerText = 'AI Sedang Bekerja...';
    }
</script>
@endpush