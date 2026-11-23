@extends('layouts.admin')

@section('title', 'Berikan Tugas Baru')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0"><i class="fas fa-clipboard-list mr-2"></i> Formulir Penugasan Laboratorium</h3>
                </div>
                
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        
                        {{-- 1. KATEGORI TUGAS (Pemicu Form Dinamis) --}}
                        <div class="form-group border-bottom pb-3">
                            <label class="font-weight-bold">Kategori Penugasan <span class="text-danger">*</span></label>
                            <select name="kategori_tugas" id="kategori_tugas" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Kategori Tugas --</option>
                                <option value="insidentil">Tugas Insidentil </option>
                                <option value="harian">Tugas Harian </option>
                            </select>
                        </div>

                        {{-- 2. PELAKSANA (Berlaku untuk Keduanya) --}}
                        <div class="form-group" id="form-pelaksana" style="display: none;">
                            <label>Pilih Pelaksana (Laboran/Admin) <span class="text-danger">*</span></label>
                            {{-- TAMBAHAN: id="select-pelaksana" untuk dibaca JS --}}
                            <select name="ditugaskan_ke_id" id="select-pelaksana" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Siapa yang Mengerjakan --</option>
                                @foreach($users as $user)
                                    {{-- TAMBAHAN: data-role agar JS tahu ini admin/laboran --}}
                                    <option value="{{ $user->id }}" data-role="{{ $user->role }}">{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- FORM A: TUGAS INSIDENTIL (Tampilan Biasa) --}}
                        <div id="form-insidentil" style="display: none;">
                            <div class="form-group">
                                <label>Jenis Pekerjaan (Judul) <span class="text-danger">*</span></label>
                                <input type="text" name="jenis_pekerjaan" id="input_jenis" class="form-control" placeholder="Misal: Perbaiki Kabel LAN di Lab A">
                            </div>
                            
                            <div class="form-group">
                                <label>Uraian Pekerjaan <span class="text-danger">*</span></label>
                                <textarea name="uraian_pekerjaan" id="input_uraian" class="form-control" rows="4" placeholder="Jelaskan detail yang harus dikerjakan..."></textarea>
                            </div>

                            <div class="form-group">
                                <label>Tenggat Waktu (Deadline) <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="tenggat_waktu" id="input_tenggat" class="form-control">
                            </div>
                        </div>

                        {{-- FORM B: TUGAS HARIAN (Tampilan Setahun) --}}
                        <div id="form-harian" style="display: none;" class="bg-light p-3 border rounded">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Sistem akan otomatis men-generate tugas untuk hari Senin-Jumat selama tahun yang dipilih, melewati hari Sabtu, Minggu, dan Tanggal Merah.
                            </div>

                            <div class="form-group">
                                <label>Tahun Penugasan <span class="text-danger">*</span></label>
                                <select name="tahun" class="form-control">
                                    <option value="{{ date('Y') }}">{{ date('Y') }} (Tahun Ini)</option>
                                    <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }} (Tahun Depan)</option>
                                </select>
                            </div>

                            {{-- TAMBAHAN: Container di-hide awal, dan ditambahkan class tugas-item serta role --}}
                            <div class="form-group mb-0" id="container-master-tugas" style="display: none;">
                                <label>Pilih Komponen Master Tugas <span class="text-danger">*</span></label>
                                <p class="text-muted small">Centang tugas apa saja yang harus dikerjakan pelaksana setiap harinya:</p>
                                
                                <div class="row">
                                    @forelse($masters as $master)
                                    <div class="col-md-6 mb-2 tugas-item role-{{ $master->role_tujuan }}" style="display: none;">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input checkbox-tugas" id="master{{ $master->id }}" name="master_tugas[]" value="{{ $master->id }}">
                                            <label class="custom-control-label" for="master{{ $master->id }}">
                                                <strong>{{ $master->nama_tugas }}</strong>
                                                <div class="small text-muted">{{ Str::limit($master->uraian_tugas, 50) }}</div>
                                            </label>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="col-12">
                                        <div class="text-danger small"><i class="fas fa-exclamation-triangle"></i> Belum ada Master Tugas Harian. Silakan tambah di menu Kelola Tugas Harian terlebih dahulu.</div>
                                    </div>
                                    @endforelse
                                </div>

                                {{-- Pesan jika tugas kosong untuk role tersebut --}}
                                <div id="pesan-kosong" class="alert alert-warning mt-2 small" style="display: none;">
                                    <i class="fas fa-info-circle"></i> Belum ada Master Tugas yang tersedia untuk peran pengguna ini. Silakan tambahkan tugas di menu Kelola Tugas Harian.
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('tasks.index') }}" class="btn btn-default mr-2">Batal</a>
                        <button type="submit" class="btn btn-primary" id="btn-submit" disabled><i class="fas fa-paper-plane mr-1"></i> Simpan Penugasan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT UNTUK FORM DINAMIS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectKategori = document.getElementById('kategori_tugas');
        const formPelaksana = document.getElementById('form-pelaksana');
        const formInsidentil = document.getElementById('form-insidentil');
        const formHarian = document.getElementById('form-harian');
        const btnSubmit = document.getElementById('btn-submit');

        // Input field Insidentil
        const inputJenis = document.getElementById('input_jenis');
        const inputUraian = document.getElementById('input_uraian');
        const inputTenggat = document.getElementById('input_tenggat');

        // Elemen Filter Master Tugas
        const selectPelaksana = document.getElementById('select-pelaksana');
        const containerTugas = document.getElementById('container-master-tugas');
        const semuaTugas = document.querySelectorAll('.tugas-item');
        const pesanKosong = document.getElementById('pesan-kosong');

        // LOGIKA 1: FILTER INSIDENTIL vs HARIAN (Bawaan Kodemu)
        selectKategori.addEventListener('change', function() {
            formPelaksana.style.display = 'block';
            btnSubmit.removeAttribute('disabled');

            if (this.value === 'insidentil') {
                formInsidentil.style.display = 'block';
                formHarian.style.display = 'none';
                inputJenis.setAttribute('required', 'required');
                inputUraian.setAttribute('required', 'required');
                inputTenggat.setAttribute('required', 'required');
            } 
            else if (this.value === 'harian') {
                formHarian.style.display = 'block';
                formInsidentil.style.display = 'none';
                inputJenis.removeAttribute('required');
                inputUraian.removeAttribute('required');
                inputTenggat.removeAttribute('required');
            }
        });

        // LOGIKA 2: FILTER CHECKBOX BERDASARKAN ROLE PELAKSANA
        selectPelaksana.addEventListener('change', function() {
            // Ambil opsi yang dipilih
            const selectedOption = this.options[this.selectedIndex];
            
            // Jika memilih opsi kosong/default
            if (!selectedOption.value) {
                containerTugas.style.display = 'none';
                return;
            }

            // Ambil role pengguna dari atribut data-role
            const userRole = selectedOption.getAttribute('data-role');
            
            // Tampilkan container
            containerTugas.style.display = 'block';
            let adaTugas = false;

            // Saring tugas sesuai role
            semuaTugas.forEach(function(elemen) {
                // Hilangkan centang ketika pelaksana berganti agar aman
                const checkbox = elemen.querySelector('.checkbox-tugas');
                if(checkbox) checkbox.checked = false;

                // Cek kesesuaian role
                if (elemen.classList.contains('role-' + userRole)) {
                    elemen.style.display = 'block'; // Tampilkan tugas
                    adaTugas = true;
                } else {
                    elemen.style.display = 'none'; // Sembunyikan tugas
                }
            });

            // Tampilkan peringatan jika kosong
            if(adaTugas) {
                pesanKosong.style.display = 'none';
            } else {
                pesanKosong.style.display = 'block';
            }
        });
    });
</script>
@endsection