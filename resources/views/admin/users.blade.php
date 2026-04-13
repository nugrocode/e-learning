{{-- users.blade.php --}}
@extends('layouts.admin')

@section('title', 'Data Pengguna')

@section('content')
    
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="bg-white p-2 p-md-3 border rounded shadow-sm d-flex justify-content-between align-items-center h-100">
                <div>
                    <span class="text-muted text-uppercase fw-bold text-[9px] md:text-[10px]" style="letter-spacing: 1px;">Total User</span>
                    <h4 class="mb-0 fw-bold text-dark text-lg md:text-2xl">{{ $stats['total'] }}</h4>
                </div>
                <div class="bg-light p-2 rounded text-secondary opacity-50 d-none d-md-block"><i class="bi bi-people"></i></div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <a href="{{ url('/admin/users?role=mahasiswa') }}" class="text-decoration-none h-100 d-block">
                <div class="bg-white p-2 p-md-3 border rounded shadow-sm d-flex justify-content-between align-items-center h-100 transition-all hover:shadow-md {{ request('role') == 'mahasiswa' ? 'border-success border-2 bg-green-50' : '' }}">
                    <div>
                        <span class="text-muted text-uppercase fw-bold text-[9px] md:text-[10px]" style="letter-spacing: 1px;">Mahasiswa</span>
                        <h4 class="mb-0 fw-bold text-dark text-lg md:text-2xl">{{ $stats['mahasiswa'] }}</h4>
                    </div>
                    <div class="bg-light p-2 rounded text-success d-none d-md-block"><i class="bi bi-mortarboard"></i></div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="{{ url('/admin/users?role=dosen') }}" class="text-decoration-none h-100 d-block">
                <div class="bg-white p-2 p-md-3 border rounded shadow-sm d-flex justify-content-between align-items-center h-100 transition-all hover:shadow-md {{ request('role') == 'dosen' ? 'border-primary border-2 bg-blue-50' : '' }}">
                    <div>
                        <span class="text-muted text-uppercase fw-bold text-[9px] md:text-[10px]" style="letter-spacing: 1px;">Dosen</span>
                        <h4 class="mb-0 fw-bold text-dark text-lg md:text-2xl">{{ $stats['dosen'] }}</h4>
                    </div>
                    <div class="bg-light p-2 rounded text-primary d-none d-md-block"><i class="bi bi-briefcase"></i></div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="{{ url('/admin/users?role=admin') }}" class="text-decoration-none h-100 d-block">
                <div class="bg-white p-2 p-md-3 border rounded shadow-sm d-flex justify-content-between align-items-center h-100 transition-all hover:shadow-md {{ request('role') == 'admin' ? 'border-warning border-2 bg-yellow-50' : '' }}">
                    <div>
                        <span class="text-muted text-uppercase fw-bold text-[9px] md:text-[10px]" style="letter-spacing: 1px;">Admin</span>
                        <h4 class="mb-0 fw-bold text-dark text-lg md:text-2xl">{{ $stats['admin'] }}</h4>
                    </div>
                    <div class="bg-light p-2 rounded text-warning d-none d-md-block"><i class="bi bi-shield-lock"></i></div>
                </div>
            </a>
        </div>
    </div>

    <div class="bg-white border rounded shadow-sm overflow-hidden">
        
        <div class="p-3 p-md-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h5 class="m-0 fw-bold text-secondary d-flex align-items-center gap-2 text-sm md:text-base">
                @if(request('role') == 'mahasiswa') <i class="bi bi-mortarboard text-success"></i> Data Mahasiswa
                @elseif(request('role') == 'dosen') <i class="bi bi-briefcase text-primary"></i> Data Dosen
                @elseif(request('role') == 'admin') <i class="bi bi-shield-lock text-warning"></i> Data Administrator
                @else <i class="bi bi-people text-dark"></i> Semua Pengguna
                @endif
            </h5>

            <div class="d-flex flex-column flex-md-row gap-2 w-full md:w-auto">
                <form action="{{ url('/admin/users') }}" method="GET" class="d-flex border rounded overflow-hidden bg-light w-full md:w-auto" style="min-width: 250px;">
                    <input type="hidden" name="role" value="{{ request('role') }}">
                    <input type="text" name="search" class="form-control border-0 bg-transparent text-xs md:text-sm px-3" 
                           placeholder="Cari User..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-link text-secondary text-decoration-none px-3">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
                
                <button class="btn fw-bold text-sm px-4 shadow-sm w-full md:w-auto text-nowrap" 
                        style="background-color: #FACC15; color: #2d3748; border: none;" 
                        data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Data
                </button>
            </div>
        </div>

        <div class="table-responsive no-scrollbar">
            <table class="table table-hover align-middle mb-0 text-xs md:text-sm">
                <thead class="bg-light text-secondary text-uppercase fw-bold text-[10px] md:text-[12px]" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="px-3 md:px-4 py-3 border-bottom">Identitas User</th>
                        <th class="py-3 border-bottom">NIM / NIDN</th> 
                        <th class="py-3 border-bottom text-center d-none d-md-table-cell">Status</th>
                        <th class="py-3 border-bottom text-end px-3 md:px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="px-3 md:px-4 py-2 md:py-3">
                                <div class="d-flex align-items-center gap-2 md:gap-3">
                                    <img src="{{ $user->foto_profil && $user->foto_profil != 'default.png' ? asset('storage/profiles/' . $user->foto_profil) : asset('images/logo_ukit.png') }}" 
                                         class="rounded-circle border shadow-sm w-8 h-8 md:w-10 md:h-10" 
                                         style="object-fit: cover; aspect-ratio: 1/1;"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->nama_lengkap) }}&background=E9ECEF&color=6C757D&bold=true'">
                                    <div class="lh-sm">
                                        <div class="fw-bold text-dark text-xs md:text-sm">{{ $user->nama_lengkap }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="font-monospace text-dark fw-bold text-xs md:text-sm">
                                {{ $user->nim_nidn ?? '-' }}
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-normal px-3">
                                    <i class="bi bi-dot"></i> Aktif
                                </span>
                            </td>
                            <td class="text-end px-3 md:px-4">
                                <div class="d-inline-flex gap-1 md:gap-2">
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

                        <div class="modal fade" id="modalEdit{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header py-3 border-bottom-0" style="background-color: #2d3748;">
                                        <h6 class="modal-title fw-bold text-sm" style="color: #FACC15;">Edit Pengguna</h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ url('/admin/users/'.$user->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label text-secondary text-[10px] uppercase fw-bold">Nama Lengkap</label>
                                                <input type="text" name="nama_lengkap" class="form-control text-sm rounded-lg" value="{{ $user->nama_lengkap }}" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-secondary text-[10px] uppercase fw-bold">NIM / NIDN</label>
                                                    <input type="text" name="nim" class="form-control text-sm rounded-lg" value="{{ $user->nim_nidn }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-secondary text-[10px] uppercase fw-bold">Role</label>
                                                    <input type="text" class="form-control text-sm bg-light rounded-lg" value="{{ ucfirst($user->role) }}" readonly>
                                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-secondary text-[10px] uppercase fw-bold">Reset Password</label>
                                                <input type="password" name="password" class="form-control text-sm rounded-lg" placeholder="Isi hanya jika ingin mengganti">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label text-secondary text-[10px] uppercase fw-bold">Ganti Foto Profil</label>
                                                <input type="file" name="foto" class="form-control text-sm rounded-lg" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer py-2 border-top-0">
                                            <button type="submit" class="btn w-100 font-bold py-2 rounded-lg text-sm shadow-sm" 
                                                    style="background-color: #FACC15; color: #2d3748; border: none;">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="mb-2 opacity-20"><i class="bi bi-people" style="font-size: 3rem;"></i></div>
                                <h6 class="text-secondary fw-bold text-sm">Data Tidak Ditemukan</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-2 p-md-3 border-top d-flex justify-content-center justify-content-md-end no-scrollbar">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header py-3 border-bottom-0" style="background-color: #2d3748;">
                    <h6 class="modal-title fw-bold text-sm" style="color: #FACC15;"><i class="bi bi-person-plus me-2"></i>Tambah Data Baru</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ url('/admin/users') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-secondary text-[10px] text-uppercase fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control text-sm rounded-lg" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary text-[10px] text-uppercase fw-bold">NIM / NIDN</label>
                                <input type="text" name="nim" class="form-control text-sm rounded-lg" placeholder="Nomor Induk" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-secondary text-[10px] text-uppercase fw-bold">Role Default</label>
                                <input type="text" class="form-control text-sm bg-light fw-bold rounded-lg" style="color: #2d3748;" value="{{ ucfirst(request('role', 'mahasiswa')) }}" readonly>
                                <input type="hidden" name="role" value="{{ request('role', 'mahasiswa') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary text-[10px] text-uppercase fw-bold">Password Login</label>
                            <input type="password" name="password" class="form-control text-sm rounded-lg" placeholder="Min. 4 karakter" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-secondary text-[10px] fw-bold">Foto Profil (Opsional)</label>
                            <input type="file" name="foto" class="form-control text-sm rounded-lg" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer py-2 border-top-0">
                        <button type="submit" class="btn w-100 font-bold py-2 text-sm rounded-lg shadow-sm" 
                                style="background-color: #FACC15; color: #2d3748; border: none;">Simpan User Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection