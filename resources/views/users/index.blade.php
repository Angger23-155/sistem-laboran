@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                {{-- Notifikasi Sukses/Error --}}
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Daftar Akun Pengguna Sistem</h3>
                        <div class="card-tools ml-auto">
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-user-plus"></i> Tambah Pengguna Baru
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-striped table-hover tabel-datatable">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>No. WhatsApp</th>
                                    <th>Peran (Role)</th>
                                    <th style="width: 150px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->no_wa ?? '-' }}</td>
                                        <td>
                                            @if ($user->role == 'superadmin')
                                                <span class="badge badge-dark">Super Admin</span>
                                            @elseif($user->role == 'kepala_lab')
                                                <span class="badge badge-warning">Kepala Lab</span>
                                            @elseif($user->role == 'atasan')
                                                <span class="badge badge-primary">Atasan</span>
                                            @elseif($user->role == 'admin')
                                                <span class="badge badge-success">Admin</span>
                                            @else
                                                <span class="badge badge-success">Laboran</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-xs"
                                                data-toggle="tooltip" title="Edit pengguna">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>

                                            {{-- TOMBOL PEMANGGIL MODAL HAPUS --}}
                                            @if ($user->id !== auth()->id())
                                                <button class="btn btn-danger btn-xs" data-toggle="modal"
                                                    data-target="#modalHapusUser{{ $user->id }}" title="Hapus pengguna">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-5">
                    <a href="{{ route('dashboard') }}" class="btn btn-default">Kembali ke Dashboard</a>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL HAPUS UNTUK MASING-MASING PENGGUNA --}}
    @foreach ($users as $user)
        @if ($user->id !== auth()->id())
            <div class="modal fade" id="modalHapusUser{{ $user->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Hapus Akun Pengguna</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                Apakah Anda yakin ingin menghapus pengguna <strong>{{ $user->name }}</strong>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    {{-- SCRIPT UNTUK MEMINDAHKAN MODAL --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                document.body.appendChild(modal);
            });
        });
    </script>
@endsection
