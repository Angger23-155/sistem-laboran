@extends('layouts.admin')

@section('title', 'Manajemen Tugas')

@section('content')
    <div class="container-fluid">

        {{-- BLOK PENAMPIL NOTIFIKASI ERROR & SUCCESS --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong><i class="fas fa-ban mr-2"></i> Terdapat Kesalahan!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- AKHIR BLOK NOTIFIKASI --}}

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2"></i>Daftar Pekerjaan Laboratorium
                </h3>

                @if (in_array(auth()->user()->role, ['kepala_lab', 'superadmin']))
                    <div class="card-tools ml-auto d-flex" style="gap: 8px;">
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                            data-target="#modalBulkDelete" title="Hapus Massal Tugas Harian">
                            <i class="fas fa-trash-alt"></i> <span class="d-none d-sm-inline">Bersihkan Tugas Harian</span>
                        </button>

                        <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm" data-toggle="tooltip"
                            title="Berikan tugas baru">
                            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Berikan Tugas Baru</span>
                        </a>
                    </div>
                @endif
            </div>

            {{-- FILTER CARI TANGGAL (KHUSUS DOSEN/SUPERADMIN) --}}
            @if (in_array(auth()->user()->role, ['kepala_lab', 'superadmin']))
                <div class="card-body border-bottom bg-light">
                    <form action="{{ route('tasks.index') }}" method="GET" class="m-0">
                        <label class="mb-1 text-muted" style="font-size: 0.85rem;"><i class="fas fa-calendar-alt"></i> Mesin
                            Waktu (Cari Tugas Masa Depan)</label>
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="date" name="tanggal_cari" class="form-control"
                                value="{{ request('tanggal_cari') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                @if (request('tanggal_cari'))
                                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Reset</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover tabel-datatable mb-0">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Jenis Pekerjaan</th>

                                {{-- Admin, Laboran, & Superadmin melihat PEMBERI TUGAS --}}
                                @if (in_array(auth()->user()->role, ['superadmin', 'admin', 'laboran']))
                                    <th>Pemberi Tugas</th>
                                @endif

                                {{-- Kepala Lab & Superadmin melihat PELAKSANA --}}
                                @if (in_array(auth()->user()->role, ['superadmin', 'kepala_lab']))
                                    <th>Pelaksana</th>
                                @endif

                                <th class="d-none d-md-table-cell">Rincian</th>
                                <th>Tenggat Waktu</th>
                                <th>Status</th>
                                <th style="width: 140px" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $index => $task)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><b>{{ $task->jenis_pekerjaan }}</b></td>

                                    {{-- Kolom PEMBERI TUGAS --}}
                                    @if (in_array(auth()->user()->role, ['superadmin', 'admin', 'laboran']))
                                        <td>
                                            @if ($task->pemberiTugas->role === 'superadmin')
                                                <span class="badge badge-dark shadow-sm" style="font-size: 0.85rem;">
                                                    <i class="fas fa-user-shield mr-1"></i> {{ $task->pemberiTugas->name }}
                                                </span>
                                            @else
                                                <span class="badge badge-info shadow-sm">
                                                    <i class="fas fa-user-tie mr-1"></i>
                                                    {{ $task->pemberiTugas->name ?? 'System' }}
                                                </span>
                                            @endif
                                        </td>
                                    @endif

                                    {{-- Kolom PELAKSANA --}}
                                    @if (in_array(auth()->user()->role, ['superadmin', 'kepala_lab']))
                                        <td>{{ $task->penerimaTugas->name ?? '-' }}</td>
                                    @endif

                                    <td class="d-none d-md-table-cell">{{ Str::limit($task->uraian_pekerjaan, 40) }}</td>
                                    <td><small>{{ \Carbon\Carbon::parse($task->tenggat_waktu)->format('d/m/y H:i') }}</small>
                                    </td>

                                    {{-- Kolom STATUS --}}
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
                                            <span class="badge badge-success">Selesai</span>
                                        @else
                                            <span class="badge badge-danger">Telat</span>
                                        @endif

                                        {{-- Sembunyikan status approval jika tugasnya harian --}}
                                        @if ($task->status != 'Pending' && $task->kategori_tugas != 'harian')
                                            <br>
                                            @if ($task->status_approval == 'Menunggu')
                                                <span class="badge badge-info mt-1"><i class="fas fa-clock"></i>
                                                    Validasi</span>
                                            @elseif ($task->status_approval == 'Disetujui')
                                                <span class="badge badge-primary mt-1"><i class="fas fa-check-double"></i>
                                                    Disetujui</span>
                                            @endif
                                        @endif
                                    </td>

                                    {{-- Kolom AKSI --}}
                                    <td class="text-center">
                                        <div class="btn-group" style="gap: 5px;">
                                            @if (in_array(auth()->user()->role, ['laboran', 'admin']) &&
                                                    ($task->status == 'Pending' || $task->status_approval == 'Revisi'))
                                                {{-- Jika Tugas Harian sudah lewat batas, matikan tombol lapor --}}
                                                @if ($task->kategori_tugas == 'harian' && \Carbon\Carbon::parse($task->tenggat_waktu)->isPast())
                                                    <button class="btn btn-secondary btn-xs" disabled title="Waktu Habis">
                                                        <i class="fas fa-ban"></i> Lewat
                                                    </button>
                                                @else
                                                    <button class="btn btn-success btn-xs" data-toggle="modal"
                                                        data-target="#modalLapor{{ $task->id }}"
                                                        title="Laporkan Tugas">
                                                        <i class="fas fa-upload"></i> Lapor
                                                    </button>
                                                @endif
                                            @endif

                                            <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-info btn-xs"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            {{-- TOMBOL EDIT & HAPUS (KHUSUS DOSEN/SUPERADMIN) --}}
                                            @if (in_array(auth()->user()->role, ['kepala_lab', 'superadmin']))
                                                <a href="{{ route('tasks.edit', $task->id) }}"
                                                    class="btn btn-warning btn-xs" title="Edit Tugas">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- Tombol Pemicu Modal Hapus --}}
                                                <button class="btn btn-danger btn-xs" data-toggle="modal"
                                                    data-target="#modalHapusTask{{ $task->id }}" title="Hapus Tugas">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

    {{-- MODAL HAPUS UNTUK MASING-MASING TUGAS --}}
    @foreach ($tasks as $task)
        @if (in_array(auth()->user()->role, ['kepala_lab', 'superadmin']))
            <div class="modal fade" id="modalHapusTask{{ $task->id }}" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Hapus Tugas</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                Apakah Anda yakin ingin menghapus tugas <strong>{{ $task->jenis_pekerjaan }}</strong>?
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

    {{-- MODAL LAPOR CERDAS (DINAMIS) --}}
    @foreach ($tasks as $task)
        <div class="modal fade" id="modalLapor{{ $task->id }}">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ url('/tasks/' . $task->id . '/report') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header bg-success text-white">
                            <h4 class="modal-title"><i class="fas fa-clipboard-check mr-2"></i> Laporan Kerja</h4>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3">Tugas: <b>{{ $task->jenis_pekerjaan }}</b></p>

                            {{-- LOGIKA CHECKLIST KHUSUS TUGAS HARIAN --}}
                            @if ($task->kategori_tugas === 'harian')
                                <div class="form-group mb-4 p-3 bg-light rounded border">
                                    <label class="d-block mb-2 text-primary"><i class="fas fa-list-ul mr-1"></i> Centang
                                        Pekerjaan yang Telah Diselesaikan:</label>
                                    @php
                                        $barisUraian = explode("\n", $task->uraian_pekerjaan);
                                    @endphp

                                    @foreach ($barisUraian as $index => $baris)
                                        @if (trim($baris) != '' && !str_contains($baris, 'TUGAS RUTIN HARIAN:'))
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="check_{{ $task->id }}_{{ $index }}"
                                                    name="checklist_harian[]"
                                                    value="{{ trim(str_replace('- ', '', $baris)) }}" required>
                                                <label class="custom-control-label font-weight-normal"
                                                    for="check_{{ $task->id }}_{{ $index }}">
                                                    {{ str_replace('- ', '', $baris) }}
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach
                                    <small class="text-muted mt-2 d-block">*Anda wajib mencentang semua item untuk
                                        menyelesaikan laporan harian ini.</small>
                                </div>
                            @endif

                            {{-- UPLOAD BUKTI (Berlaku untuk keduanya) --}}
                            <div class="form-group">
                                <label><i class="fas fa-camera mr-1"></i> Upload Bukti Pendukung (1 File)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="fileBukti{{ $task->id }}"
                                        name="file_bukti" required accept="image/*,application/pdf">
                                    <label class="custom-file-label" for="fileBukti{{ $task->id }}">Pilih file
                                        foto/PDF...</label>
                                </div>
                            </div>

                            {{-- Field Alasan Terlambat (Otomatis muncul jika sudah lewat deadline) --}}
                            @if (now() > $task->tenggat_waktu)
                                <div class="callout callout-warning mt-4 shadow-sm border-left-warning">
                                    <h5 class="text-warning font-weight-bold">
                                        <i class="fas fa-exclamation-triangle"></i> Terlambat Mengumpulkan!
                                    </h5>
                                    <p class="mb-0">Tugas ini telah melewati batas waktu pengumpulan. Anda wajib mengisi
                                        alasan keterlambatan.</p>
                                </div>
                                <div class="form-group">
                                    <label class="text-danger">Alasan Keterlambatan *</label>
                                    <textarea name="argumen_terlambat" class="form-control" rows="2" required
                                        placeholder="Contoh: Terkendala jaringan internet saat akan melapor..."></textarea>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane mr-1"></i> Kirim
                                Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- MODAL HAPUS MASSAL --}}
    @if (in_array(auth()->user()->role, ['kepala_lab', 'superadmin']))
        <div class="modal fade" id="modalBulkDelete">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('tasks.bulkDelete') }}" method="POST">
                        @csrf
                        <div class="modal-header bg-danger text-white">
                            <h4 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Hapus Massal Tugas
                            </h4>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted">Fitur ini akan menghapus semua tugas harian di tahun yang dipilih yang
                                <b>belum dikerjakan</b> (Status Pending).
                            </p>

                            <div class="form-group">
                                <label>Pilih Laboran/Admin Target</label>
                                <select name="ditugaskan_ke_id" class="form-control" required>
                                    <option value="">-- Pilih Pelaksana --</option>
                                    @foreach (\App\Models\User::whereIn('role', ['laboran', 'admin'])->get() as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Pilih Tahun Hapus</label>
                                <select name="tahun" class="form-control" required>
                                    <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                    <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}</option>
                                    <option value="{{ date('Y') + 2 }}">{{ date('Y') + 2 }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Ya, Bersihkan Sekarang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Memindahkan modal ke body agar tidak tertutup elemen lain
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                document.body.appendChild(modal);
            });

            // Skrip untuk mengubah teks 'Pilih file...' menjadi nama file yang dipilih
            var fileInputs = document.querySelectorAll('.custom-file-input');
            fileInputs.forEach(function(input) {
                input.addEventListener('change', function(e) {
                    // Ambil nama file
                    var fileName = e.target.files[0].name;
                    // Cari elemen label di sebelahnya dan ubah teksnya
                    var label = e.target.nextElementSibling;
                    label.innerText = fileName;
                });
            });
        });
    </script>
@endsection
