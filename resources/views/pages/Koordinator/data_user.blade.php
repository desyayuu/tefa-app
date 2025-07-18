@extends('layouts.app')

@section('title', 'TEFA | Dashboard Koordinator')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')        
        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('koordinator.dataUser') }}" class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-project-diagram me-1"></i>
                            Data User
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                    </li>
                </ol>
            </nav>
        </div>

        <div class="content-table" style="font-size: 14px;">
            <!-- Handling Error and Success -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <div class="title-table d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0">Data User</h5>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="{{ route('koordinator.dataUser') }}" method="GET">
                            <input type="text" name="search" class="form-control pe-5 form-search" placeholder="Cari User..." value="{{$search ?? '' }}">
                            <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y pe-2 py-2 border-0 bg-transparent">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7664" cy="11.7669" r="8.98856" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.0181 18.4854L21.5421 22.0002" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Button Clear Search-->
                    @if(isset($search) && $search)
                    <a href="{{ route('koordinator.dataUser') }}" class="btn btn-tutup btn-outline-secondary">
                        Hapus Filter
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- Handling Hasil Search -->
            @if(isset($search) && $search)
            <div class="alert alert-info">
                Menampilkan {{ count($user) }} hasil untuk pencarian "{{ $search }}"
            </div>
            @endif

            <table class="table" style="font-size: 14px;">
                <thead>
                    <tr>
                    <!-- <th scope="col">#</th> -->
                    <th scope="col">Nama</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tanggal Registrasi</th>
                    <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($user as $u)
                    <tr>
                        <td>
                            @if ($u->role == 'Dosen')
                                {{ $u->nama_dosen }}
                            @elseif ($u->role == 'Mahasiswa')
                                {{ $u->nama_mahasiswa }}
                            @elseif ($u->role == 'Profesional')
                                {{ $u->nama_profesional }}
                            @elseif ($u->role == 'Koordinator')
                                {{ $u->nama_koordinator }}
                            @endif
                        </td>
                        <td>{{ $u->email}}</td>
                        <td>{{ $u->role}}</td>
                        <td><x-ui.status-badge :status="$u->status" /></td>
                        <td>{{ \Carbon\Carbon::parse($u->created_at)->translatedFormat('d F Y') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalUser{{ $u->user_id }}">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                                        <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
                                    </svg>
                                </a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $u->user_id }}">
                                    <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit and Detail Data -->
                    <div class="modal fade" id="modalUser{{ $u->user_id }}" tabindex="-1" aria-labelledby="dosenLabel{{ $u->user_id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.updateStatusUser', $u->user_id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="dosenLabel{{ $u->user_id }}">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6 mt-2">
                                                <label for="status" class="form-label">Status Akun</label>
                                                <select class="form-select" id="status" name="status">
                                                    <option value="Active" {{ $u->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                    <option value="Rejected" {{ $u->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                    <option value="Pending" {{ $u->status == 'Pending' ? 'selected' : ''}}>Pending</option>
                                                    <option value="Disabled" {{ $u->status == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label for="role" class="form-label">Role</label>
                                                <input type="text" class="form-control" id="role" name="role" value="{{ $u->role }}" disabled>
                                            </div>
                                        
                                            <div class="col-md-6 mt-2">
                                                <label for="nama_dosen" class="form-label">Nama</label>
                                                <input type="text" class="form-control" id="nama_dosen" name="nama_dosen" value="{{ $u->role == 'Dosen' ? $u->nama_dosen : ($u->role == 'Mahasiswa' ? $u->nama_mahasiswa : ($u->role == 'Profesional' ? $u->nama_profesional : $u->nama_koordinator)) }}" disabled>
                                            </div>

                                            @if($u->role == 'Dosen')
                                            <div class="col-md-6 mt-2">
                                                <label for="nidn_dosen" class="form-label">NIDN/NIP</label>
                                                <input type="text" class="form-control" id="nidn_dosen" name="nidn_dosen" value="{{ $u->nidn_dosen }}" disabled>
                                            </div>
                                            @elseif($u->role == 'Mahasiswa')
                                            <div class="col-md-6 mt-2">
                                                <label for="nim_mahasiswa" class="form-label">NIM</label>
                                                <input type="text" class="form-control" id="nim_mahasiswa" name="nim_mahasiswa" value="{{ $u->nim_mahasiswa }}" disabled>
                                            </div>
                                            @elseif($u->role == 'Koordinator')
                                            <div class="col-md-6 mt-2">
                                                <label for="nidn_koordinator" class="form-label">NIP/NIDN</label>
                                                <input type="text" class="form-control" id="nidn_koordinator" name="nidn_koordinator" value="{{ $u->nidn_koordinator }}" disabled>
                                            </div>
                                            @endif

                                            <div class="col-md-6 mt-2">
                                                <label for="email_dosen" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email_dosen" name="email_dosen" value="{{ $u->email }}" disabled>
                                            </div>

                                            @if($u->role == 'Dosen')
                                                <div class="col-md-6 mt-2">
                                                    <label for="telepon_dosen" class="form-label">Telepon</label>
                                                    <input type="text" class="form-control" id="telepon_dosen" name="telepon_dosen" value="{{ $u->telepon_dosen }}"disabled>
                                                </div>
                                            @elseif($u->role == 'Mahasiswa')
                                                <div class="col-md-6 mt-2">
                                                    <label for="telepon_mahasiswa" class="form-label">Telepon</label>
                                                    <input type="text" class="form-control" id="telepon_mahasiswa" name="telepon_mahasiswa" value="{{ $u->telepon_mahasiswa }}"disabled>
                                                </div>
                                            @elseif($u->role == 'Profesional')
                                                <div class="col-md-6 mt-2">
                                                    <label for="telepon_profesional" class="form-label">Telepon</label>
                                                    <input type="text" class="form-control" id="telepon_profesional" name="telepon_profesional" value="{{ $u->telepon_profesional }}"disabled>
                                                </div>
                                            @elseif($u->role == 'Koordinator')
                                                <div class="col-md-6 mt-2">
                                                    <label for="telepon_koordinator" class="form-label">Telepon</label>
                                                    <input type="text" class="form-control" id="telepon_koordinator" name="telepon_koordinator" value="{{ $u->telepon_koordinator }}"disabled>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-add">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <!-- Modal konfirmasi delete -->
                    <div class="modal fade" id="modalDelete{{ $u->user_id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $u->user_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.deleteUser', $u->user_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteLabel{{ $u->user_id }}">Konfirmasi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah yakin ingin menghapus data 
                                        <strong>
                                                @if ($u->role == 'Dosen')
                                                    {{ $u->nama_dosen }}
                                                @elseif ($u->role == 'Mahasiswa')
                                                    {{ $u->nama_mahasiswa }}
                                                @elseif ($u->role == 'Profesional')
                                                    {{ $u->nama_profesional }}
                                                @elseif ($u->role == 'Koordinator')
                                                    {{ $u->nama_koordinator }}
                                                @endif
                                        </strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-hapus">Hapus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            @if(isset($search) && $search)
                                Tidak ada hasil yang cocok dengan pencarian "{{ $search }}"
                            @else
                                Belum ada data mitra
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center">
                <div class="showing-text">
                    Showing {{ $user->firstItem() }} to {{ $user->lastItem() }} of {{ $user->total() }} entries
                </div>
                <div class="pagination-links">
                    {{ $user->appends(['search' => request('search')])->links('vendor.pagination.custom_master') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection