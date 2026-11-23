@extends('layouts.admin')

@section('title', 'Manajemen Hari Libur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Hari Libur (Tanggal Merah)</h3>
                    <div class="card-tools ml-auto">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahLibur">
                            <i class="fas fa-plus"></i> Tambah Hari Libur
                        </button>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <table class="table table-striped table-hover tabel-datatable">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Tanggal Libur</th>
                                <th>Keterangan</th>
                                <th style="width: 150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($liburs as $index => $libur)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><b>{{ \Carbon\Carbon::parse($libur->tanggal)->translatedFormat('d F Y') }}</b></td>
                                <td>{{ $libur->keterangan }}</td>
                                <td>
                                    <button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#modalEditLibur{{ $libur->id }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modalHapusLibur{{ $libur->id }}">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
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

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahLibur" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Hari Libur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('hari-libur.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal Libur</label>
                        <input type="date" class="form-control" name="tanggal" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan Libur</label>
                        <input type="text" class="form-control" name="keterangan" placeholder="Misal: Cuti Bersama Idul Fitri" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT & HAPUS --}}
@foreach($liburs as $libur)
    {{-- Modal Edit --}}
    <div class="modal fade" id="modalEditLibur{{ $libur->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Hari Libur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('hari-libur.update', $libur->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tanggal Libur</label>
                            <input type="date" class="form-control" name="tanggal" value="{{ $libur->tanggal }}" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan Libur</label>
                            <input type="text" class="form-control" name="keterangan" value="{{ $libur->keterangan }}" required>
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

    {{-- Modal Hapus --}}
    <div class="modal fade" id="modalHapusLibur{{ $libur->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Hari Libur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('hari-libur.destroy', $libur->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        Yakin ingin menghapus libur <strong>{{ $libur->keterangan }}</strong> pada <strong>{{ \Carbon\Carbon::parse($libur->tanggal)->translatedFormat('d F Y') }}</strong>?
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