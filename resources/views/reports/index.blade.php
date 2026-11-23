@extends('layouts.admin')

@section('title', 'Cetak Laporan Kinerja')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0"><i class="fas fa-print mr-2"></i> Cetak Laporan Kinerja Laboratorium</h3>
                </div>
                
                <form action="{{ route('reports.cetak') }}" method="POST" target="_blank">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> Pilih rentang tanggal dan kolom apa saja yang ingin ditampilkan dalam laporan cetak.
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Dari Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tgl_awal" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Sampai Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tgl_akhir" class="form-control" required>
                            </div>
                        </div>

                        @if(in_array(Auth::user()->role, ['superadmin', 'kepala_lab']))
                            <div class="form-group border-top pt-3 mt-2">
                                <label>Pilih Pelaksana (Opsional)</label>
                                <select name="user_id" id="select_pelaksana" class="form-control">
                                    <option value="">-- Cetak Laporan Semua Laboran & Admin --</option>
                                    @foreach($laborans as $lab)
                                        <option value="{{ $lab->id }}">{{ $lab->name }} ({{ ucfirst($lab->role) }})</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Kosongkan jika ingin merekap pekerjaan seluruh staf sekaligus.</small>
                            </div>
                        @else
                            <div class="form-group border-top pt-3 mt-2">
                                <label>Pelaksana Tugas</label>
                                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                <small class="text-muted text-danger">Anda hanya memiliki akses untuk mencetak laporan kinerja Anda sendiri.</small>
                            </div>
                        @endif

                        <div class="form-group border-top pt-3 mt-3">
                            <label>Pilih Kolom yang Ditampilkan <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="col_deadline" name="kolom[]" value="deadline" checked>
                                        <label class="custom-control-label" for="col_deadline">Tenggat Waktu</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="col_pelaksana" name="kolom[]" value="pelaksana" checked>
                                        <label class="custom-control-label" for="col_pelaksana" id="label_pelaksana">Nama Pelaksana</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="col_pekerjaan" name="kolom[]" value="pekerjaan" checked>
                                        <label class="custom-control-label" for="col_pekerjaan">Nama Pekerjaan</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="col_kategori" name="kolom[]" value="kategori" checked>
                                        <label class="custom-control-label" for="col_kategori">Kategori Tugas</label>
                                    </div>
                                </div>
                                {{-- KOLOM BARU: RINCIAN TUGAS --}}
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="col_uraian" name="kolom[]" value="uraian" checked>
                                        <label class="custom-control-label" for="col_uraian">Rincian Tugas</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="col_status" name="kolom[]" value="status" checked>
                                        <label class="custom-control-label" for="col_status">Status Pengerjaan</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="col_persetujuan" name="kolom[]" value="persetujuan" checked>
                                        <label class="custom-control-label" for="col_persetujuan">Persetujuan</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-danger btn-lg" onclick="return checkColumns()"><i class="fas fa-file-pdf mr-2"></i> Generate Laporan PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Logika untuk menonaktifkan checkbox "Nama Pelaksana" jika sudah memilih pelaksana tertentu dari dropdown
    document.addEventListener('DOMContentLoaded', function() {
        const selectPelaksana = document.getElementById('select_pelaksana');
        const cbPelaksana = document.getElementById('col_pelaksana');
        const labelPelaksana = document.getElementById('label_pelaksana');

        function updatePelaksanaCheckbox() {
            if (selectPelaksana) {
                if (selectPelaksana.value !== "") {
                    cbPelaksana.checked = false;
                    cbPelaksana.disabled = true;
                    labelPelaksana.classList.add('text-muted');
                } else {
                    cbPelaksana.disabled = false;
                    cbPelaksana.checked = true;
                    labelPelaksana.classList.remove('text-muted');
                }
            } else {
                if (cbPelaksana) {
                    cbPelaksana.checked = false;
                    cbPelaksana.disabled = true;
                    labelPelaksana.classList.add('text-muted');
                }
            }
        }

        updatePelaksanaCheckbox();

        if (selectPelaksana) {
            selectPelaksana.addEventListener('change', updatePelaksanaCheckbox);
        }
    });
// Validasi agar minimal 1 kolom dipilih saat submit
    function checkColumns() {
        const checkboxes = document.querySelectorAll('input[name="kolom[]"]:checked');
        if (checkboxes.length === 0) {
            alert('Silakan pilih minimal 1 kolom untuk ditampilkan di laporan!');
            return false;
        }
        return true;
    }
</script>
@endsection