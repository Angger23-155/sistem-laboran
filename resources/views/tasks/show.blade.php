@extends('layouts.admin')

@section('title', 'Detail Pekerjaan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Penugasan</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">Jenis Pekerjaan</th>
                                <td>{{ $task->jenis_pekerjaan }}</td>
                            </tr>
                            <tr>
                                <th>Rincian Pekerjaan</th>
                                <td>{!! nl2br(e($task->uraian_pekerjaan)) !!}</td>
                            </tr>

                            {{-- PEMBERI TUGAS: Admin, Laboran, Superadmin --}}
                            @if (in_array(auth()->user()->role, ['superadmin', 'admin', 'laboran']))
                                <tr>
                                    <th>Pemberi Tugas</th>
                                    <td>
                                        @if ($task->pemberiTugas->role === 'superadmin')
                                            <span class="badge badge-dark"><i class="fas fa-user-shield"></i>
                                                {{ $task->pemberiTugas->name }}</span>
                                        @else
                                            <span class="badge badge-info"><i class="fas fa-user-tie"></i>
                                                {{ $task->pemberiTugas->name ?? 'System' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            {{-- PELAKSANA: Kepala Lab, Superadmin --}}
                            @if (in_array(auth()->user()->role, ['superadmin', 'kepala_lab']))
                                <tr>
                                    <th>Pelaksana</th>
                                    <td>{{ $task->penerimaTugas->name }}</td>
                                </tr>
                            @endif

                            <tr>
                                <th>Tenggat Waktu</th>
                                <td>{{ \Carbon\Carbon::parse($task->tenggat_waktu)->format('d F Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Status Pekerjaan</th>
                                <td>
                                    @if ($task->status == 'Pending')
                                        {{-- Cek jika Tugas Harian kedaluwarsa --}}
                                        @if ($task->kategori_tugas == 'harian' && \Carbon\Carbon::parse($task->tenggat_waktu)->isPast())
                                            <span class="badge badge-danger">Hangus</span>
                                        @elseif ($task->status_approval == 'Revisi')
                                            <span class="badge badge-danger"><i class="fas fa-exclamation-circle"></i>
                                                Revisi (Pending)</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    @elseif($task->status == 'Selesai Tepat Waktu')
                                        <span class="badge badge-success">Selesai Tepat Waktu</span>
                                    @else
                                        <span class="badge badge-danger">Selesai Terlambat</span>
                                    @endif

                                    {{-- Status Approval --}}
                                    @if ($task->status != 'Pending' && $task->kategori_tugas != 'harian')
                                        <br>
                                        @if ($task->status_approval == 'Menunggu')
                                            <span class="badge badge-info mt-1"><i class="fas fa-clock"></i> Validasi</span>
                                        @elseif ($task->status_approval == 'Disetujui')
                                            <span class="badge badge-primary mt-1"><i class="fas fa-check-double"></i>
                                                Disetujui</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Menampilkan Box Laporan JIKA statusnya sudah bukan Pending dan ada data laporannya --}}
                @if ($task->status !== 'Pending' && $task->report)
                    <div class="card card-success mt-3">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-check-circle"></i> Laporan Hasil Pekerjaan</h3>
                        </div>
                        <div class="card-body text-center">
                            <p>Pekerjaan ini diselesaikan pada: <br>
                                <b>{{ \Carbon\Carbon::parse($task->report->waktu_diselesaikan)->format('d F Y, H:i:s') }}</b>
                            </p>

                            {{-- Menampilkan Argumen Jika Terlambat --}}
                            @if ($task->status == 'Selesai Terlambat' && $task->report->argumen_terlambat)
                                <div class="alert alert-danger text-left mt-3">
                                    <strong><i class="fas fa-comment-dots"></i> Alasan Keterlambatan:</strong><br>
                                    {{ $task->report->argumen_terlambat }}
                                </div>
                            @endif

                            <hr>
                            <h5>File Bukti Pekerjaan:</h5>
                            <button type="button" class="btn btn-primary mt-2" data-toggle="modal"
                                data-target="#modalBukti">
                                <i class="fas fa-search-plus"></i> Lihat Dokumen / Foto Laporan
                            </button>
                        </div>
                    </div>

                    {{-- Modal Pratinjau File Laporan --}}
                    <div class="modal fade" id="modalBukti" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Bukti Dokumentasi Pekerjaan</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    @php
                                        $ekstensi = pathinfo($task->report->file_bukti, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($ekstensi), ['jpg', 'jpeg', 'png', 'gif']);

                                        // Path ke folder penyimpanan
                                        $fileUrl = asset('storage/bukti_laporan/' . $task->report->file_bukti);
                                    @endphp

                                    @if ($isImage)
                                        <img src="{{ $fileUrl }}" alt="Bukti Laporan" class="img-fluid rounded border">
                                    @elseif(strtolower($ekstensi) == 'pdf')
                                        <iframe src="{{ $fileUrl }}" width="100%" height="500px"
                                            style="border: none;"></iframe>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> File (.{{ $ekstensi }}) tidak dapat
                                            dipratinjau langsung.
                                        </div>
                                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-success">
                                            <i class="fas fa-download"></i> Unduh File
                                        </a>
                                    @endif
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Menampilkan Box Validasi: Hanya untuk Kepala Lab dan Superadmin JIKA status masih Menunggu DAN BUKAN TUGAS HARIAN --}}
                @if (in_array(auth()->user()->role, ['kepala_lab', 'superadmin']) &&
                        $task->status_approval == 'Menunggu' &&
                        $task->kategori_tugas !== 'harian' &&
                        $task->report)
                    <div class="card mt-3">
                        <div class="card-header bg-light">
                            <h3 class="card-title font-weight-bold text-danger"><i class="fas fa-gavel"></i> Keputusan </h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('tasks.approve', $task->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Status Validasi:</label>
                                    <select name="status_approval" class="form-control" id="selectStatus" required>
                                        <option value="">-- Pilih Keputusan --</option>
                                        <option value="Disetujui">Terima Laporan (Selesai)</option>
                                        <option value="Revisi">Tolak & Minta Revisi</option>
                                    </select>
                                </div>
                                <div id="boxCatatan" style="display:none">
                                    <label class="text-danger">Catatan Revisi *</label>
                                    <textarea name="catatan_revisi" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-3"><i class="fas fa-save"></i>
                                    Simpan Keputusan</button>
                            </form>
                        </div>
                    </div>

                    <script>
                        document.getElementById('selectStatus').addEventListener('change', function() {
                            var boxCatatan = document.getElementById('boxCatatan');
                            if (this.value === 'Revisi') {
                                boxCatatan.style.display = 'block';
                                boxCatatan.querySelector('textarea').required = true;
                            } else {
                                boxCatatan.style.display = 'none';
                                boxCatatan.querySelector('textarea').required = false;
                            }
                        });
                    </script>
                @endif

                <div class="text-center mt-3 mb-5">
                    <a href="{{ route('tasks.index') }}" class="btn btn-dark px-4 shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Tugas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                document.body.appendChild(modal);
            });
        });
    </script>
@endsection