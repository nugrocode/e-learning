@extends('layouts.admin')

@section('title', 'Kelola Pengumuman')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="font-bold text-2xl text-gray-800">Papan Pengumuman</h2>
            <p class="text-gray-500 text-sm">Informasi ini akan tampil di Dashboard Mahasiswa.</p>
        </div>
        
        <button class="btn font-bold shadow-sm transition w-full md:w-auto py-2 px-4 text-sm rounded-lg" 
            style="background-color: #FACC15; color: #2d3748; border: none;"
            data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-2"></i> Buat Pengumuman
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">Judul & Isi</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-end px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($pengumuman as $item)
                        <tr>
                            <td class="px-4 py-3" style="max-width: 350px;">
                                <strong class="text-gray-800 d-block mb-1">{{ $item->judul }}</strong>
                                <span class="text-gray-500 text-xs d-block text-truncate" style="max-width: 300px;">
                                    {{ Str::limit($item->isi, 80) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $badges = [
                                        'info' => 'bg-blue-100 text-blue-700',
                                        'penting' => 'bg-yellow-100 text-yellow-700',
                                        'libur' => 'bg-red-100 text-red-700'
                                    ];
                                    
                                    $icons = [
                                        'info' => 'bi-info-circle',
                                        'penting' => 'bi-star-fill',
                                        'libur' => 'bi-exclamation-octagon-fill'
                                    ];
                                    
                                    $labels = [
                                        'info' => 'Informasi',
                                        'penting' => 'Penting',
                                        'libur' => 'Peringatan / Mendesak'
                                    ];
                                @endphp
                                <span class="badge {{ $badges[$item->tipe] }} rounded-pill px-2 py-1 text-[10px] uppercase border border-white shadow-sm">
                                    <i class="bi {{ $icons[$item->tipe] }} me-1"></i> {{ $labels[$item->tipe] }}
                                </span>
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="text-green-600 text-xs font-bold bg-green-50 px-2 py-1 rounded border border-green-100">Aktif</span>
                                @else
                                    <span class="text-gray-400 text-xs bg-gray-50 px-2 py-1 rounded border border-gray-200">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-gray-500 text-xs">{{ $item->created_at->format('d M Y') }}</td>
                            <td class="text-end px-4">
                                <button class="btn btn-sm btn-light border rounded-lg hover:bg-blue-50 transition" 
                                    style="color: #0d6efd;"
                                    data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <form action="{{ url('/admin/pengumuman/'.$item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-red-600 border rounded-lg ms-1 hover:bg-red-50 transition" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-xl border-0 shadow-lg">
                                    <div class="modal-header py-3 border-bottom-0" style="background-color: #2d3748;">
                                        <h5 class="modal-title font-bold text-sm" style="color: #FACC15;">Edit Pengumuman</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ url('/admin/pengumuman/'.$item->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label text-xs font-bold text-gray-500 uppercase">Judul</label>
                                                <input type="text" name="judul" class="form-control rounded-lg" value="{{ $item->judul }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-xs font-bold text-gray-500 uppercase">Isi Pesan</label>
                                                <textarea name="isi" class="form-control rounded-lg" rows="4" required>{{ $item->isi }}</textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="form-label text-xs font-bold text-gray-500 uppercase">Tipe</label>
                                                    <select name="tipe" class="form-select rounded-lg text-sm">
                                                        <option value="info" {{ $item->tipe == 'info' ? 'selected' : '' }}>Informasi (Biru)</option>
                                                        <option value="penting" {{ $item->tipe == 'penting' ? 'selected' : '' }}>Penting (Kuning)</option>
                                                        <option value="libur" {{ $item->tipe == 'libur' ? 'selected' : '' }}>Peringatan / Mendesak (Merah)</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label text-xs font-bold text-gray-500 uppercase">Status</label>
                                                    <select name="is_active" class="form-select rounded-lg text-sm">
                                                        <option value="1" {{ $item->is_active ? 'selected' : '' }}>Aktif (Tampil)</option>
                                                        <option value="0" {{ !$item->is_active ? 'selected' : '' }}>Sembunyikan</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-light text-sm font-bold rounded-lg" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn text-sm font-bold rounded-lg px-4" style="background-color: #FACC15; color: #2d3748; border: none;">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-gray-400">
                                <i class="bi bi-clipboard-x text-4xl mb-3 d-block opacity-50"></i>
                                Belum ada pengumuman yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-xl border-0 shadow-lg">
                <div class="modal-header py-3 border-bottom-0" style="background-color: #2d3748;">
                    <h5 class="modal-title font-bold text-sm" style="color: #FACC15;">Buat Pengumuman Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ url('/admin/pengumuman') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">Judul Pengumuman</label>
                            <input type="text" name="judul" class="form-control rounded-lg" placeholder="Contoh: Jadwal UTS Semester Genap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">Isi Pesan</label>
                            <textarea name="isi" class="form-control rounded-lg" rows="4" placeholder="Tulis detail informasi di sini..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-bold text-gray-500 uppercase">Tipe Pengumuman</label>
                            <select name="tipe" class="form-select rounded-lg text-sm">
                                <option value="info">Informasi (Biru)</option>
                                <option value="penting">Penting (Kuning)</option>
                                <option value="libur">Peringatan / Mendesak (Merah)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light text-sm font-bold rounded-lg" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn text-sm font-bold rounded-lg px-4" style="background-color: #FACC15; color: #2d3748; border: none;">Terbitkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection