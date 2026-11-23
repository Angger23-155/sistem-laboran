@extends('layouts.admin')

@section('title', 'Manajemen Tugas Harian')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                {{-- Notifikasi Sukses/Error (Disamakan dengan users) --}}
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Daftar Tugas Harian</h3>
                        <div class="card-tools ml-auto">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahMaster">
                                <i class="fas fa-plus"></i> Tambah Tugas Harian
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-striped table-hover tabel-datatable">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Nama Tugas</th>
                                    <th>Rincian Tugas</th>
                                    <th>Pelaksana</th>
                                    <th style="width: 150px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- PERULANGAN BERADA DI SINI, SEMUA YANG MEMAKAI $master HARUS DI DALAM SINI --}}
                                @foreach ($masters as $index => $master)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $master->nama_tugas }}</td>
                                        <td>{{ Str::limit($master->uraian_tugas, 100) }}</td>
                                        <td>
                                            {{-- Pengecekan Badge Diletakkan di dalam Kolom Pelaksana --}}
                                            @if ($master->role_tujuan == 'admin')
                                                <span class="badge badge-primary">Admin</span>
                                            @else
                                                <span class="badge badge-success">Laboran</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-xs" data-toggle="modal"
                                                data-target="#modalEditMaster{{ $master->id }}" title="Edit tugas">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger btn-xs" data-toggle="modal"
                                                data-target="#modalHapusMaster{{ $master->id }}" title="Hapus tugas">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tombol Kembali ke Dashboard --}}
                <div class="mb-5">
                    <a href="{{ route('dashboard') }}" class="btn btn-default">Kembali ke Dashboard</a>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambahMaster" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Master Tugas Harian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('master-tugas.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Tugas</label>
                            <input type="text" class="form-control" name="nama_tugas"
                                placeholder="Misal: Cek Kebersihan Lab" required>
                        </div>
                        <div class="form-group">
                            <label>Rincian Tugas</label>
                            <textarea class="form-control" name="uraian_tugas" rows="4"
                                placeholder="Jelaskan detail apa saja yang harus dilakukan..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Tugas Ini Untuk?</label>
                            <select name="role_tujuan" class="form-control" required>
                                <option value="">-- Pilih Pelaksana --</option>
                                <option value="laboran">Laboran </option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Master Tugas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT & HAPUS --}}
    @foreach ($masters as $master)
        <div class="modal fade" id="modalEditMaster{{ $master->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Master Tugas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('master-tugas.update', $master->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama Tugas</label>
                                <input type="text" class="form-control" name="nama_tugas"
                                    value="{{ $master->nama_tugas }}" required>
                            </div>
                            <div class="form-group">
                                <label>Rincian Tugas</label>
                                <textarea class="form-control" name="uraian_tugas" rows="4" required>{{ $master->uraian_tugas }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Tugas Ini Untuk?</label>
                                <select name="role_tujuan" class="form-control" required>
                                    <option value="laboran" {{ $master->role_tujuan == 'laboran' ? 'selected' : '' }}>
                                        Laboran</option>
                                    <option value="admin" {{ $master->role_tujuan == 'admin' ? 'selected' : '' }}>Admin
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalHapusMaster{{ $master->id }}" tabindex="-1" role="dialog"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Master Tugas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('master-tugas.destroy', $master->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus tugas <strong>{{ $master->nama_tugas }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                document.body.appendChild(modal);
            });
        });
    </script>
@endsection