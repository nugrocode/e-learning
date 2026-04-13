@extends('layouts.admin')

@section('title', 'Bank Mata Kuliah')

@section('content')
    {{-- HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="font-bold text-2xl text-gray-800">Bank Mata Kuliah</h2>
            <p class="text-gray-500 text-sm">Pusat data seluruh mata kuliah. Tambah, Edit, atau Hapus master data di sini.</p>
        </div>
        
        {{-- UPDATE WAKNA: Tombol Buat Master Baru --}}
        <button class="btn rounded-lg font-bold py-2 px-4 shadow-sm transition" 
            style="background-color: #FACC15; color: #2d3748; border: none;"
            data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-2"></i> Tambah Mata Kuliah
        </button>
    </div>

    {{-- ALERT SYSTEM --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-lg d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-xl"></i> {{ session('success') }}
        </div>
    @endif

    {{-- TABLE CONTAINER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="table-responsive no-scrollbar">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3" style="width: 30%;">Mata Kuliah</th>
                        <th style="width: 20%;">Dosen Pengampu</th>
                        <th style="width: 30%;">Deskripsi</th>
                        <th style="width: 10%;">Prodi</th>
                        <th class="text-end px-4" style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($courses as $mk)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    @if($mk->gambar)
                                        <img src="{{ asset('storage/thumbnails/' . $mk->gambar) }}" 
                                             class="w-12 h-12 rounded-lg object-cover border shadow-sm"
                                             style="aspect-ratio: 1/1; object-fit: cover;"
                                             onerror="this.src='{{ asset('images/logo_ukit.png') }}'">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xs border">
                                            {{ substr($mk->nama_mk, 0, 2) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong class="text-gray-800 d-block leading-tight">{{ $mk->nama_mk }}</strong>
                                        <span class="text-[10px] text-gray-400">Dibuat: {{ $mk->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($mk->dosen)
                                    <span class="text-gray-700 font-medium text-xs">{{ $mk->dosen->nama_lengkap }}</span>
                                @else
                                    <span class="badge bg-red-100 text-red-600 font-normal text-[10px]">Belum diset</span>
                                @endif
                            </td>
                            <td class="text-gray-500">
                                <p class="line-clamp-2 m-0 text-xs leading-relaxed">{{ $mk->deskripsi }}</p>
                            </td>
                            <td>
                                <span class="badge bg-blue-50 text-blue-600 border border-blue-100 text-[10px]">
                                    {{ $mk->concentrations->count() }} Prodi
                                </span>
                            </td>
                            <td class="px-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-light text-blue-600 border rounded-lg hover:bg-blue-50" 
                                        data-bs-toggle="modal" data-bs-target="#modalEdit{{ $mk->id }}" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    
                                    <form action="{{ url('/admin/bank-mk/'.$mk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Peringatan: Menghapus data ini akan melepas MK dari semua prodi. Lanjutkan?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-red-600 border rounded-lg hover:bg-red-50" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL EDIT --}}
                        <div class="modal fade" id="modalEdit{{ $mk->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-xl border-0 shadow-lg">
                                    {{-- UPDATE WARNA: Header Modal Edit --}}
                                    <div class="modal-header py-3 border-bottom-0" style="background-color: #2d3748;">
                                        <h5 class="modal-title font-bold text-sm" style="color: #FACC15;">Edit Mata Kuliah</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ url('/admin/bank-mk/'.$mk->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label text-xs font-bold uppercase text-gray-500">Nama Mata Kuliah</label>
                                                <input type="text" name="nama_mk" class="form-control rounded-lg" value="{{ $mk->nama_mk }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs font-bold uppercase text-gray-500">Dosen Pengampu</label>
                                                <select name="dosen_id" class="form-select rounded-lg" required>
                                                    @foreach($dosens as $dosen)
                                                        <option value="{{ $dosen->id }}" {{ $mk->dosen_id == $dosen->id ? 'selected' : '' }}>
                                                            {{ $dosen->nama_lengkap }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs font-bold uppercase text-gray-500">Deskripsi MK</label>
                                                <textarea name="deskripsi" class="form-control rounded-lg" rows="3">{{ $mk->deskripsi }}</textarea>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label text-xs font-bold uppercase text-gray-500">Ganti Cover (Opsional)</label>
                                                <input type="file" name="gambar" class="form-control rounded-lg" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            {{-- UPDATE WARNA: Tombol Submit Modal Edit --}}
                                            <button type="submit" class="btn w-100 font-bold py-2 rounded-lg" 
                                                    style="background-color: #FACC15; color: #2d3748; border: none;">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-gray-400">
                                <i class="bi bi-hdd-stack text-3xl d-block mb-2 opacity-30"></i>
                                Bank Data Kosong.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH BARU --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-xl border-0 shadow-lg">
                {{-- UPDATE WARNA: Header Modal Tambah --}}
                <div class="modal-header py-3 border-bottom-0" style="background-color: #2d3748;">
                    <h5 class="modal-title font-bold text-sm" style="color: #FACC15;">Tambah Mata Kuliah</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ url('/admin/bank-mk') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold uppercase text-gray-500">Nama Mata Kuliah</label>
                            <input type="text" name="nama_mk" class="form-control rounded-lg" placeholder="Contoh: Algoritma Lanjut" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold uppercase text-gray-500">Dosen Pengampu</label>
                            <select name="dosen_id" class="form-select rounded-lg" required>
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold uppercase text-gray-500">Deskripsi Singkat</label>
                            <textarea name="deskripsi" class="form-control rounded-lg" rows="3" placeholder="Jelaskan isi materi..."></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-xs font-bold uppercase text-gray-500">Gambar Cover</label>
                            <input type="file" name="gambar" class="form-control rounded-lg" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        {{-- UPDATE WARNA: Tombol Submit Modal Tambah --}}
                        <button type="submit" class="btn w-100 font-bold py-2 rounded-lg" 
                                style="background-color: #FACC15; color: #2d3748; border: none;">Simpan ke Bank Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection