@extends('layouts.admin')

@section('title', 'Layanan Komplain Fasilitas')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-tools mr-2"></i>Daftar Komplain
                </h3>

                @if (in_array(auth()->user()->role, ['kepala_lab', 'superadmin']))
                    <div class="card-tools ml-auto">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalBuatKomplain">
                            <i class="fas fa-plus-circle"></i> <span class="d-none d-sm-inline">Buat Komplain</span>
                        </button>
                    </div>
                @endif
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover tabel-datatable mb-0">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Barang</th>
                                <th class="d-none d-md-table-cell">Deskripsi</th>

                                {{-- Superadmin dan Atasan melihat Pelapor --}}
                                @if (in_array(auth()->user()->role, ['superadmin', 'atasan']))
                                    <th>Pelapor</th>
                                @endif

                                {{-- HANYA Superadmin yang melihat Penanggap --}}
                                @if (auth()->user()->role === 'superadmin')
                                    <th class="d-none d-lg-table-cell">Penanggap</th>
                                @endif

                                <th>Status</th>
                                <th style="width: 150px" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($complains as $index => $complain)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><b>{{ $complain->nama_barang }}</b></td>
                                    <td class="d-none d-md-table-cell">{{ Str::limit($complain->deskripsi_kerusakan, 30) }}
                                    </td>

                                    {{-- Kolom Pelapor --}}
                                    @if (in_array(auth()->user()->role, ['superadmin', 'atasan']))
                                        <td><small>{{ explode(' ', $complain->pelapor->name)[0] }}</small></td>
                                    @endif

                                    {{-- Kolom Penanggap HANYA untuk Superadmin --}}
                                    @if (auth()->user()->role === 'superadmin')
                                        <td class="d-none d-lg-table-cell">
                                            <small>{{ $complain->penanggap->name ?? '-' }}</small>
                                        </td>
                                    @endif

                                    <td>
                                        @if ($complain->status == 'Diproses')
                                            <span class="badge badge-info">Proses</span>
                                        @elseif($complain->status == 'Selesai')
                                            <span class="badge badge-success">Selesai</span>
                                        @else
                                            <span class="badge badge-warning">Baru</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <div class="btn-group" style="gap: 5px;">
                                            {{-- TOMBOL DETAIL (Mata) --}}
                                            <button class="btn btn-info btn-xs" data-toggle="modal"
                                                data-target="#modalDetail{{ $complain->id }}" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            {{-- TOMBOL TANGGAPI --}}
                                            @if (in_array(auth()->user()->role, ['atasan', 'superadmin']) &&
                                                    $complain->status != 'Diproses' &&
                                                    $complain->status != 'Selesai' &&
                                                    auth()->user()->id != $complain->pelapor_id)
                                                <button class="btn btn-primary btn-xs" data-toggle="modal"
                                                    data-target="#modalTanggapi{{ $complain->id }}" title="Beri Tanggapan">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                            @endif

                                            {{-- TOMBOL SELESAI (CENTANG) --}}
                                            @if (in_array(auth()->user()->role, ['atasan', 'superadmin']) &&
                                                    $complain->status == 'Diproses' &&
                                                    auth()->user()->id != $complain->pelapor_id)
                                                <form action="{{ url('/complains/' . $complain->id . '/complete') }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-xs"
                                                        title="Tandai Selesai">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- TOMBOL HAPUS --}}
                                            @if (auth()->user()->role === 'superadmin' || auth()->user()->id == $complain->pelapor_id)
                                                <form action="{{ route('complains.destroy', $complain->id) }}"
                                                    method="POST" data-ajax-delete class="d-inline">
                                                    @csrf @method('DELETE')
                                                    {{-- Tombol Pemanggil Modal Hapus --}}
                                                    <button type="button" class="btn btn-danger btn-xs" data-toggle="modal"
                                                        data-target="#modalHapusKomplain{{ $complain->id }}"
                                                        title="Hapus Komplain">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL BUAT KOMPLAIN --}}
    <div class="modal fade" id="modalBuatKomplain">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('complains.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title font-weight-bold">Buat Komplain Baru</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi Kerusakan</label>
                            <textarea name="deskripsi_kerusakan" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Foto Bukti</label>
                            <input type="file" name="foto_bukti" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-block">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL & TANGGAPI --}}
    @foreach ($complains as $complain)
        <div class="modal fade" id="modalDetail{{ $complain->id }}">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg" style="border-radius: 12px;">
                    <div class="modal-header border-0 pb-0">
                        <h4 class="modal-title font-weight-bold">Detail Komplain</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $complain->foto_bukti) }}" class="img-fluid rounded mb-3"
                            style="max-height: 250px; width: 100%; object-fit: cover;">
                        <div class="text-left bg-light p-3 rounded">
                            <p class="mb-1"><b>Barang:</b> {{ $complain->nama_barang }}</p>
                            <p class="mb-0"><b>Status:</b> {{ $complain->status ?? 'Menunggu' }}</p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalTanggapi{{ $complain->id }}">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ url('/complains/' . $complain->id . '/respond') }}" method="POST">
                        @csrf
                        <div class="modal-header bg-primary text-white">
                            <h4 class="modal-title">Tanggapi Komplain</h4>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>Barang: <b>{{ $complain->nama_barang }}</b></p>
                            <div class="form-group">
                                <label>Pesan Balasan</label>
                                <textarea name="pesan_balasan" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Estimasi Selesai</label>
                                <input type="date" name="estimasi_selesai" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btn-block">Kirim Tanggapan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
    {{-- MODAL HAPUS UNTUK MASING-MASING KOMPLAIN --}}
    @foreach ($complains as $complain)
        <div class="modal fade" id="modalHapusKomplain{{ $complain->id }}" tabindex="-1" role="dialog"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Data Komplain</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('complains.destroy', $complain->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus data komplain untuk barang
                            <strong>{{ $complain->barang ?? 'ini' }}</strong>?
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

    {{-- SCRIPT UNTUK MEMINDAHKAN MODAL AGAR TIDAK TERTUTUP BACKGROUND --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                document.body.appendChild(modal);
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //  Pindahkan modal ke body (Penting agar tidak tertutup overlay di HP)
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                document.body.appendChild(modal);
            });

            //  Trik Auto-Open Modal yang lebih kuat
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'create') {
                // Berikan jeda 300ms agar sistem siap
                setTimeout(function() {
                    $('#modalBuatKomplain').modal('show');
                }, 400);
            }
        });
    </script>
@endsection
