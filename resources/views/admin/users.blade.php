@extends('layouts.admin')

@section('title', 'Data Pengguna')

@section('content')
    
    {{-- HEADER & FILTER CARDS --}}
    <div class="row g-3 mb-4">
        {{-- Card Total (Info Only) --}}
        <div class="col-md-3">
            <div class="bg-white p-3 border rounded shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 10px; letter-spacing: 1px;">Total User</span>
                    <h4 class="mb-0 fw-bold text-dark">{{ $stats['total'] }}</h4>
                </div>
                <div class="bg-light p-2 rounded text-secondary opacity-50"><i class="bi bi-people"></i></div>
            </div>
        </div>
        
        {{-- Card Mahasiswa --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/users?role=mahasiswa') }}" class="text-decoration-none">
                <div class="bg-white p-3 border rounded shadow-sm d-flex justify-content-between align-items-center transition-all hover:shadow-md {{ request('role') == 'mahasiswa' ? 'border-success border-2 bg-green-50' : '' }}">
                    <div>
                        <span class="text-muted text-uppercase fw-bold" style="font-size: 10px; letter-spacing: 1px;">Mahasiswa</span>
                        <h4 class="mb-0 fw-bold text-dark">{{ $stats['mahasiswa'] }}</h4>
                    </div>
                    <div class="bg-light p-2 rounded text-success"><i class="bi bi-mortarboard"></i></div>
                </div>
            </a>
        </div>

        {{-- Card Dosen --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/users?role=dosen') }}" class="text-decoration-none">
                <div class="bg-white p-3 border rounded shadow-sm d-flex justify-content-between align-items-center transition-all hover:shadow-md {{ request('role') == 'dosen' ? 'border-primary border-2 bg-blue-50' : '' }}">
                    <div>
                        <span class="text-muted text-uppercase fw-bold" style="font-size: 10px; letter-spacing: 1px;">Dosen</span>
                        <h4 class="mb-0 fw-bold text-dark">{{ $stats['dosen'] }}</h4>
                    </div>
                    <div class="bg-light p-2 rounded text-primary"><i class="bi bi-briefcase"></i></div>
                </div>
            </a>
        </div>

        {{-- Card Admin --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/users?role=admin') }}" class="text-decoration-none">
                <div class="bg-white p-3 border rounded shadow-sm d-flex justify-content-between align-items-center transition-all hover:shadow-md {{ request('role') == 'admin' ? 'border-warning border-2 bg-yellow-50' : '' }}">
                    <div>
                        <span class="text-muted text-uppercase fw-bold" style="font-size: 10px; letter-spacing: 1px;">Admin</span>
                        <h4 class="mb-0 fw-bold text-dark">{{ $stats['admin'] }}</h4>
                    </div>
                    <div class="bg-light p-2 rounded text-warning"><i class="bi bi-shield-lock"></i></div>
                </div>
            </a>
        </div>
    </div>

    {{-- AREA UTAMA --}}
    <div class="bg-white border rounded shadow-sm overflow-hidden">
        
        <div class="p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <h5 class="m-0 fw-bold text-secondary d-flex align-items-center gap-2">
                @if(request('role') == 'mahasiswa') <i class="bi bi-mortarboard text-success"></i> Data Mahasiswa
                @elseif(request('role') == 'dosen') <i class="bi bi-briefcase text-primary"></i> Data Dosen
                @elseif(request('role') == 'admin') <i class="bi bi-shield-lock text-warning"></i> Data Administrator
                @else <i class="bi bi-people text-dark"></i> Semua Pengguna
                @endif
            </h5>

            <div class="d-flex gap-2">
                <form action="{{ url('/admin/users') }}" method="GET" class="d-flex border rounded overflow-hidden bg-light" style="width: 250px;">
                    <input type="hidden" name="role" value="{{ request('role') }}">
                    <input type="text" name="search" class="form-control border-0 bg-transparent text-sm px-3" 
                           placeholder="Cari User..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-link text-secondary text-decoration-none px-3">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
                
                <button class="btn btn-primary fw-bold text-nowrap text-sm px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Data
                </button>
            </div>
        </div>

        {{-- TABEL DATA (NO SCROLLBAR) --}}
        <div class="table-responsive no-scrollbar">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                <thead class="bg-light text-secondary text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="px-4 py-3 border-bottom ps-4">Identitas User</th>
                        <th class="py-3 border-bottom">NIM / NIDN</th> 
                        <th class="py-3 border-bottom text-center">Status</th>
                        <th class="py-3 border-bottom text-end px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="px-4 py-3 ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    {{-- FIX PATH: MENGGUNAKAN STORAGE/PROFILES DAN ANTI GEPENG --}}
                                    <img src="{{ $user->foto_profil && $user->foto_profil != 'default.png' ? asset('storage/profiles/' . $user->foto_profil) : asset('images/logo_ukit.png') }}" 
                                         class="rounded-circle border shadow-sm" width="38" height="38" 
                                         style="object-fit: cover; aspect-ratio: 1/1;"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}&background=E9ECEF&color=6C757D&bold=true'">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->nama_lengkap }}</div>
                                        <div class="text-muted small" style="font-size: 11px;">ID: #{{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="font-monospace text-dark fw-bold">
                                {{ $user->nim_nidn ?? '-' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-normal px-3">
                                    <i class="bi bi-dot"></i> Aktif
                                </span>
                            </td>
                            <td class="text-end px-4">
                                <div class="d-inline-flex gap-2">
                                    <button class="btn btn-sm btn-light text-primary border shadow-sm rounded-lg" 
                                        data-bs-toggle="modal" data-bs-target="#modalEdit{{ $user->id }}" 
                                        title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    
                                    @if($user->id != session('user_id'))
                                        <form action="{{ url('/admin/users/'.$user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-light text-danger border shadow-sm rounded-lg" title="Hapus Permanen">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-light text-muted border shadow-sm disabled rounded-lg" title="Ini Akun Anda">
                                            <i class="bi bi-person-check-fill"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL EDIT --}}
                        <div class="modal fade" id="modalEdit{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header py-3 bg-light border-bottom-0">
                                        <h6 class="modal-title fw-bold text-gray-800">Edit Pengguna</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ url('/admin/users/'.$user->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label text-secondary small fw-bold">Nama Lengkap</label>
                                                <input type="text" name="nama_lengkap" class="form-control rounded-lg" value="{{ $user->nama_lengkap }}" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-secondary small fw-bold">NIM / NIDN</label>
                                                    <input type="text" name="nim" class="form-control rounded-lg" value="{{ $user->nim_nidn }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-secondary small fw-bold">Role</label>
                                                    <input type="text" class="form-control bg-light rounded-lg" value="{{ ucfirst($user->role) }}" readonly>
                                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-secondary small fw-bold">Reset Password (Opsional)</label>
                                                <input type="password" name="password" class="form-control rounded-lg" placeholder="Isi hanya jika ingin mengganti">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label text-secondary small fw-bold">Ganti Foto Profil</label>
                                                <input type="file" name="foto" class="form-control rounded-lg" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer py-2 border-top-0">
                                            <button type="submit" class="btn btn-primary w-100 font-bold py-2 rounded-lg shadow-sm">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="mb-2 opacity-20"><i class="bi bi-people" style="font-size: 3rem;"></i></div>
                                <h6 class="text-secondary fw-bold">Data Tidak Ditemukan</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-3 border-top d-flex justify-content-end no-scrollbar">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header py-3 bg-primary text-white border-bottom-0">
                    <h6 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Tambah Data Baru</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ url('/admin/users') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-secondary small text-uppercase fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control rounded-lg" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small text-uppercase fw-bold">NIM / NIDN</label>
                                <input type="text" name="nim" class="form-control rounded-lg" placeholder="Nomor Induk" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small text-uppercase fw-bold">Role Default</label>
                                <input type="text" class="form-control bg-light fw-bold text-primary rounded-lg" value="{{ ucfirst(request('role', 'mahasiswa')) }}" readonly>
                                <input type="hidden" name="role" value="{{ request('role', 'mahasiswa') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small text-uppercase fw-bold">Password Login</label>
                            <input type="password" name="password" class="form-control rounded-lg" placeholder="Min. 4 karakter" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-secondary small fw-bold">Foto Profil (Opsional)</label>
                            <input type="file" name="foto" class="form-control rounded-lg" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer py-2 border-top-0">
                        <button type="submit" class="btn btn-primary w-100 font-bold py-2 rounded-lg shadow-sm">Simpan User Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection