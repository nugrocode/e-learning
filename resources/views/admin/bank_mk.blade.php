@extends('layouts.admin')

@section('title', 'Bank Mata Kuliah')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="font-bold text-2xl text-gray-800">Bank Mata Kuliah</h2>
            <p class="text-gray-500 text-sm">Pusat data seluruh mata kuliah. Tambah, Edit, atau Hapus master data di sini.</p>
        </div>
        
        <button class="btn btn-primary bg-blue-900 border-0 rounded-lg font-bold py-2 px-4 shadow-sm hover:bg-blue-800 transition" 
            data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-2"></i> Buat Master Baru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-lg d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-xl"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-lg">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Mata Kuliah</th>
                        <th>Dosen Pengampu</th>
                        <th>Deskripsi</th>
                        <th>Digunakan Di</th>
                        <th class="text-end px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($courses as $mk)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    @if($mk->gambar)
                                        <img src="{{ asset('images/' . $mk->gambar) }}" class="w-10 h-10 rounded object-cover border">
                                    @else
                                        <div class="w-10 h-10 rounded bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-xs border">
                                            {{ substr($mk->nama_mk, 0, 2) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong class="text-gray-800 d-block">{{ $mk->nama_mk }}</strong>
                                        <span class="text-xs text-gray-400">Created: {{ $mk->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($mk->dosen)
                                    <span class="text-gray-700 font-medium text-xs">{{ $mk->dosen->nama_lengkap }}</span>
                                @else
                                    <span class="text-red-400 text-xs italic">Belum diset</span>
                                @endif
                            </td>
                            <td class="text-gray-500" style="max-width: 250px;">
                                <p class="line-clamp-2 m-0 text-xs">{{ $mk->deskripsi }}</p>
                            </td>
                            <td>
                                <span class="badge bg-blue-50 text-blue-600 border border-blue-100">
                                    {{ $mk->concentrations->count() }} Prodi
                                </span>
                            </td>
                            <td class="text-end px-4">
                                <button class="btn btn-sm btn-light text-blue-600 border rounded hover:bg-blue-50 me-1" 
                                    data-bs-toggle="modal" data-bs-target="#modalEdit{{ $mk->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ url('/admin/bank-mata-kuliah/'.$mk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('PERINGATAN: Menghapus Bank Data akan menghilangkan MK ini dari SEMUA Prodi. Lanjutkan?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-red-600 border rounded hover:bg-red-50">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- MODAL EDIT --}}
                        <div class="modal fade" id="modalEdit{{ $mk->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-xl border-0 shadow-lg">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title font-bold">Edit Master Data</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ url('/admin/bank-mata-kuliah/'.$mk->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label text-xs font-bold uppercase">Nama MK</label>
                                                <input type="text" name="nama_mk" class="form-control" value="{{ $mk->nama_mk }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs font-bold uppercase">Dosen Pengampu</label>
                                                <select name="dosen_id" class="form-select" required>
                                                    <option value="">-- Pilih Dosen --</option>
                                                    @foreach($dosens as $dosen)
                                                        <option value="{{ $dosen->id }}" {{ $mk->dosen_id == $dosen->id ? 'selected' : '' }}>
                                                            {{ $dosen->nama_lengkap }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs font-bold uppercase">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control" rows="3">{{ $mk->deskripsi }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs font-bold uppercase">Ganti Gambar</label>
                                                <input type="file" name="gambar" class="form-control" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="submit" class="btn btn-primary bg-blue-900 border-0 w-100 font-bold">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-gray-400">
                                <i class="bi bi-hdd-stack text-2xl d-block mb-2 opacity-50"></i>
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
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title font-bold">Buat Master Mata Kuliah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ url('/admin/bank-mata-kuliah') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold uppercase">Nama MK</label>
                            <input type="text" name="nama_mk" class="form-control" placeholder="Contoh: Algoritma Lanjut" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold uppercase">Dosen Pengampu</label>
                            <select name="dosen_id" class="form-select" required>
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold uppercase">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan isi materi..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold uppercase">Gambar Cover</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="submit" class="btn btn-primary bg-blue-900 border-0 font-bold">Simpan ke Bank Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection