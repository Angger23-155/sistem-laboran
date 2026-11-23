@extends('layouts.admin')

@section('title', 'Dashboard Utama')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-dark shadow-sm border-0"
                    style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white;">
                    <h5><i class="icon fas fa-user-circle"></i> Selamat Datang, {{ auth()->user()->name }}!</h5>
                    <p class="mb-0">Anda masuk sebagai <b>{{ strtoupper(auth()->user()->role) }}</b>. Pantau statistik
                        laboratorium Anda di bawah ini.</p>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- ADMINISTRASI SISTEM (Hanya Muncul untuk Superadmin) --}}
            @if (auth()->user()->role === 'superadmin')
                {{-- 1. PENGGUNA --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info shadow">
                        <div class="inner">
                            <h3>{{ \App\Models\User::count() }}</h3>
                            <p>Total Pengguna</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="{{ route('users.index') }}" class="small-box-footer">Kelola Pengguna <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{-- 2. MASTER TUGAS HARIAN --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success shadow">
                        <div class="inner">
                            <h3>{{ \App\Models\MasterTugasHarian::count() }}</h3>
                            <p>Master Tugas Harian</p>
                        </div>
                        <div class="icon"><i class="fas fa-list-ul"></i></div>
                        <a href="{{ route('master-tugas.index') }}" class="small-box-footer">Kelola Tugas Harian <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{-- 3. HARI LIBUR --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary shadow">
                        <div class="inner">
                            <h3>{{ \App\Models\HariLibur::count() }}</h3>
                            <p>Total Hari Libur</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-times"></i></div>
                        <a href="{{ route('hari-libur.index') }}" class="small-box-footer">Kelola Hari Libur <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endif

            {{-- 4. TUGAS (Tampil untuk Semua Kecuali Atasan) --}}
            @if (auth()->user()->role !== 'atasan')
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary shadow">
                        <div class="inner">
                            <h3>{{ $tasks->count() }}</h3>
                            <p>
                                @if (auth()->user()->role === 'superadmin')
                                    Total Seluruh Tugas
                                @elseif(auth()->user()->role === 'kepala_lab')
                                    Daftar Tugas 
                                @else
                                    Tugas Saya
                                @endif
                            </p>
                        </div>
                        <div class="icon"><i class="fas fa-tasks"></i></div>
                        <a href="{{ route('tasks.index') }}" class="small-box-footer">Buka Daftar Tugas <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endif

            {{-- 5. KOMPLAIN (Tampil untuk Superadmin, Atasan, & Kepala Lab) --}}
            @if (in_array(auth()->user()->role, ['superadmin', 'atasan', 'kepala_lab']))
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning shadow">
                        <div class="inner">
                            <h3 class="text-white">
                                {{ auth()->user()->role === 'superadmin' ? \App\Models\Complain::count() : $complains->count() }}
                            </h3>
                            <p class="text-white">Total Komplain</p>
                        </div>
                        <div class="icon"><i class="fas fa-tools"></i></div>
                        <a href="{{ route('complains.index') }}" class="small-box-footer text-white">Lihat Detail Komplain
                            <i class="fas fa-arrow-circle-right text-white"></i></a>
                    </div>
                </div>
            @endif

            {{-- 6. LAPORAN KINERJA (Tampil untuk Semua Kecuali Atasan) --}}
            @if (auth()->user()->role !== 'atasan')
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger shadow">
                        <div class="inner">
                            <h3 class="text-white">PDF</h3>
                            <p class="text-white">Laporan Kinerja</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-pdf"></i></div>
                        <a href="{{ route('reports.index') }}" class="small-box-footer text-white">Menuju Halaman Cetak 
                            <i class="fas fa-arrow-circle-right text-white"></i></a>
                    </div>
                </div>
            @endif
        </div>

        {{-- BAGIAN AKSES CEPAT --}}
        @if (in_array(auth()->user()->role, ['superadmin', 'kepala_lab', 'atasan']))
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white">
                    <h3 class="card-title text-bold"><i class="fas fa-bolt text-warning mr-2"></i>Akses Cepat</h3>
                </div>
                <div class="card-body">
                    <div class="row">

                        {{-- AKSES CEPAT UNTUK SUPERADMIN & KEPALA LAB --}}
                        @if (in_array(auth()->user()->role, ['superadmin', 'kepala_lab']))
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('tasks.create') }}"
                                    class="btn btn-outline-primary btn-block py-3 shadow-sm"
                                    style="border-width: 2px; font-weight: bold;">
                                    <i class="fas fa-plus-circle fa-2x d-block mb-2"></i> Buat Tugas Baru
                                </a>
                            </div>

                            <div class="col-md-3 mb-2">
                                <a href="{{ route('complains.index', ['action' => 'create']) }}"
                                    class="btn btn-outline-danger btn-block py-3 shadow-sm"
                                    style="border-width: 2px; font-weight: bold;">
                                    <i class="fas fa-exclamation-triangle fa-2x d-block mb-2"></i> Buat Komplain Baru
                                </a>
                            </div>
                        @endif

                        {{-- AKSES CEPAT TAMBAHAN KHUSUS SUPERADMIN --}}
                        @if (auth()->user()->role === 'superadmin')
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('master-tugas.create') }}"
                                    class="btn btn-outline-success btn-block py-3 shadow-sm"
                                    style="border-width: 2px; font-weight: bold;">
                                    <i class="fas fa-list-ul fa-2x d-block mb-2"></i> Tambah Tugas Harian
                                </a>
                            </div>

                            <div class="col-md-3 mb-2">
                                <a href="{{ route('hari-libur.create') }}"
                                    class="btn btn-outline-secondary btn-block py-3 shadow-sm"
                                    style="border-width: 2px; font-weight: bold;">
                                    <i class="fas fa-calendar-plus fa-2x d-block mb-2"></i> Tambah Hari Libur
                                </a>
                            </div>
                        @endif

                        {{-- AKSES CEPAT KHUSUS ATASAN --}}
                        @if (auth()->user()->role === 'atasan')
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('complains.index') }}"
                                    class="btn btn-outline-warning btn-block py-3 shadow-sm"
                                    style="border-width: 2px; font-weight: bold;">
                                    <i class="fas fa-search fa-2x d-block mb-2"></i> Cek Komplain Masuk
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @endif

    </div> 
@endsection