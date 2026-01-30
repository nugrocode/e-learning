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
    <div class="bg-white border rounded shadow-sm">
        
        {{-- TOOLBAR (LAYOUT DIPERBAIKI: Justify Between) --}}
        <div class="p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            
            {{-- BAGIAN KIRI: JUDUL TABEL --}}
            <h5 class="m-0 fw-bold text-secondary d-flex align-items-center gap-2">
                @if(request('role') == 'mahasiswa') <i class="bi bi-mortarboard text-success"></i> Data Mahasiswa
                @elseif(request('role') == 'dosen') <i class="bi bi-briefcase text-primary"></i> Data Dosen
                @elseif(request('role') == 'admin') <i class="bi bi-shield-lock text-warning"></i> Data Administrator
                @endif
            </h5>

            {{-- BAGIAN KANAN: SEARCH & TOMBOL TAMBAH --}}
            <div class="d-flex gap-2">
                {{-- Form Search --}}
                <form action="{{ url('/admin/users') }}" method="GET" class="d-flex border rounded overflow-hidden bg-light" style="width: 250px;">
                    <input type="hidden" name="role" value="{{ request('role') }}">
                    <input type="text" name="search" class="form-control border-0 bg-transparent text-sm px-3" 
                           placeholder="Cari User..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-link text-secondary text-decoration-none px-3">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
                
                {{-- Tombol Tambah (SERAGAM: "Tambah Data") --}}
                <button class="btn btn-primary fw-bold text-nowrap text-sm px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Data
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success m-3 py-2 text-sm border-0 bg-success-subtle text-success-emphasis rounded-3">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger m-3 py-2 text-sm border-0 bg-danger-subtle text-danger-emphasis rounded-3">
                <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- TABEL DATA --}}
        <div class="table-responsive">
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
                                    <img src="{{ asset('images/' . ($user->foto_profil ?? 'default.png')) }}" 
                                         class="rounded-circle border shadow-sm" width="36" height="36" style="object-fit: cover;"
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
                                    <button class="btn btn-sm btn-light text-primary border shadow-sm" 
                                        data-bs-toggle="modal" data-bs-target="#modalEdit{{ $user->id }}" 
                                        title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    
                                    @if($user->id != session('user_id'))
                                        <form action="{{ url('/admin/users/'.$user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-light text-danger border shadow-sm" title="Hapus Permanen">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-light text-muted border shadow-sm disabled" title="Ini Akun Anda">
                                            <i class="bi bi-person-check-fill"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL EDIT --}}
                        <div class="modal fade" id="modalEdit{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header py-3 bg-light border-bottom-0">
                                        <h6 class="modal-title fw-bold">Edit Pengguna</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ url('/admin/users/'.$user->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label text-secondary small fw-bold">Nama Lengkap</label>
                                                <input type="text" name="nama_lengkap" class="form-control" value="{{ $user->nama_lengkap }}" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-secondary small fw-bold">NIM / NIDN</label>
                                                    <input type="text" name="nim" class="form-control" value="{{ $user->nim_nidn }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-secondary small fw-bold">Role</label>
                                                    <input type="text" class="form-control bg-light" value="{{ ucfirst($user->role) }}" readonly>
                                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-secondary small fw-bold">Reset Password</label>
                                                <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diganti">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-secondary small fw-bold">Ganti Foto</label>
                                                <input type="file" name="foto" class="form-control" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer py-2 border-top-0">
                                            <button type="button" class="btn btn-light text-secondary font-bold" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary font-bold">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="mb-2"><i class="bi bi-search text-gray-200" style="font-size: 3rem;"></i></div>
                                <h6 class="text-secondary fw-bold">Data Kosong</h6>
                                <p class="text-muted small">Belum ada user di kategori ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top d-flex justify-content-end">
            {{ $users->links() }}
        </div>
    </div>

    {{-- MODAL TAMBAH (SERAGAM "Tambah Data") --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
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
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Cth: Nugroho Indrayadi" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small text-uppercase fw-bold">NIM / NIDN</label>
                                <input type="text" name="nim" class="form-control" placeholder="Nomor Induk" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary small text-uppercase fw-bold">Role</label>
                                {{-- Role Terkunci Otomatis --}}
                                <input type="text" class="form-control bg-light fw-bold text-primary" value="{{ ucfirst(request('role')) }}" readonly>
                                <input type="hidden" name="role" value="{{ request('role') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small text-uppercase fw-bold">Password Default</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 4 karakter" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">Foto Profil</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer py-2 border-top-0">
                        <button type="button" class="btn btn-light text-secondary font-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary font-bold">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection