<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Laboran | @yield('title', 'Dashboard')</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <style>
        :root {
            --primary-blue: #2563eb;
            --sidebar-bg: #0f172a;
            --header-dark: #1e293b;
        }

        /* 1. NAVBAR ATAS & FOOTER BAWAH*/
        .main-header.navbar,
        .main-footer {
            background-color: var(--sidebar-bg) !important;
            border: none !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
            color: #94a3b8 !important;
        }

        .main-header .nav-link {
            color: #cbd5e1 !important;
        }

        .main-header .nav-link:hover {
            color: #ffffff !important;
        }

        .card {
            background-color: #f8fafc !important;
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
            overflow: hidden;
        }

        .card-header {
            background-color: var(--header-dark) !important;
            color: #ffffff !important;
            border-bottom: none !important;
            padding: 1.2rem 1.5rem !important;
        }

        .card-title {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .table {
            margin-bottom: 0 !important;
        }

        .table thead th {
            background-color: #334155 !important;
            color: #e2e8f0 !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border: none !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #ffffff !important;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #f1f5f9 !important;
        }

        .table td {
            vertical-align: middle !important;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
        }

        /* 4. TOMBOL LOGOUT & STATUS */
        .btn {
            border-radius: 6px !important;
            font-weight: 600;
            box-shadow: none !important;
        }

        .btn-danger {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
            color: white !important;
        }

        .btn-danger:hover {
            background-color: #b91c1c !important;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .badge-warning {
            background-color: #f59e0b !important;
            color: #fff !important;
            border: none;
        }

        .badge-success {
            background-color: #10b981 !important;
            color: #fff !important;
        }

        .badge-info {
            background-color: #3b82f6 !important;
            color: #fff !important;
        }

        .content-wrapper {
            background-image: url('{{ asset('img/Lab bg.jpeg') }}') !important;
            background-size: cover !important;
            background-position: center !important;
            background-attachment: fixed !important;
            position: relative;
        }

        .content-wrapper::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.7) 100%);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 0;
        }

        .content-header h1 {
            color: #ffffff !important;
            font-weight: 700;
        }

        .content-wrapper>.content-header,
        .content-wrapper>.content {
            position: relative;
            z-index: 1;
        }

        /* 6. LOGO & SIDEBAR */
        .main-sidebar {
            background-color: var(--sidebar-bg) !important;
        }

        .brand-link {
            background-color: #020617 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        body.sidebar-collapse #sidebar-kontak {
            display: none !important;
        }

        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }

        .password-strength {
            transition: all 0.3s ease;
            font-size: 12px;
        }

        .btn-loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .table-hover tbody tr:hover {
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .btn:hover {
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .dashboard-auto-refresh {
            position: relative;
        }

        .auto-refresh-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 12px;
            color: #6c757d;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* 1. Menyembunyikan teks "Cari:" bawaan */
        .dataTables_filter label {
            font-size: 0;
            margin-bottom: 0;
        }

        /* 2. Mengubah Kotak Pencarian menjadi Modern (Membulat + Icon) */
        .dataTables_filter input {
            font-size: 14px;
            color: #334155;
            border: 1px solid #cbd5e1;
            border-radius: 20px !important;
            padding: 8px 15px 8px 35px !important;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'%3E%3C/line%3E%3C/svg%3E") no-repeat 12px center;
            outline: none;
            box-shadow: none !important;
            width: 250px !important;
            transition: all 0.3s ease;
        }

        .dataTables_filter input:focus {
            border-color: #3b82f6;
            background-color: #f8fafc;
        }

        /* Merapikan posisi bagian atas tabel */
        .dataTables_wrapper .row:first-child {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-top: 10px;
        }

        /* 3. Kustomisasi Tombol Pagination (Kotak kecil & warna soft) */
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 15px;
        }

        .page-item .page-link {
            border: 1px solid #f1f5f9;
            color: #475569;
            background-color: #ffffff;
            border-radius: 8px !important;
            margin: 0 4px;
            font-weight: 600;
            padding: 6px 14px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .page-item.active .page-link {
            background-color: #eff6ff !important;
            color: #2563eb !important;
            border-color: #eff6ff !important;
            box-shadow: none;
        }

        .page-item.disabled .page-link {
            color: #cbd5e1;
            background-color: #f8fafc;
            border-color: #f1f5f9;
            box-shadow: none;
        }

        .page-link:hover:not(.disabled) {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        /* 4. Kustomisasi Teks Info (Menampilkan 1 dari X) */
        .dataTables_info {
            color: #64748b !important;
            font-weight: 600;
            font-size: 0.85rem;
            padding-top: 20px !important;
        }


        .card-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
        }

        .card-header .card-title {
            margin-bottom: 0;
            white-space: nowrap;
        }

        .card-header .btn {
            white-space: nowrap;
            margin-left: auto;
        }

        .dataTables_filter {
            margin: 0 auto !important;
            padding: 0 20px;
            text-align: center;
        }

        .dataTables_filter input {
            font-size: 14px;
            color: #334155 !important;
            border: none !important;
            border-radius: 20px !important;
            padding: 8px 15px 8px 35px !important;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'%3E%3C/line%3E%3C/svg%3E") no-repeat 12px center !important;
            outline: none;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2) !important;
            width: 300px !important;
            transition: all 0.3s ease;
        }

        .dataTables_filter input:focus {
            box-shadow: 0 3px 15px rgba(59, 130, 246, 0.4) !important;
            transform: scale(1.02);
        }

        .card-body .dataTables_wrapper {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        .dataTables_wrapper .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .dataTables_wrapper .col-sm-12 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        table.dataTable {
            margin-top: 0 !important;
            border-top: none !important;
            border-collapse: collapse !important;
        }

        .badge-danger {
            animation: pulse 2s infinite;
        }

        .table tbody tr {
            border: 1px solid transparent;
            border-bottom: 1px solid #dee2e6;
            transition: background-color 0.2s ease-in-out, transform 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05) !important;
            transform: translateZ(0);
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm mt-1"><i class="fas fa-sign-out-alt"></i>
                            Logout</button>
                    </form>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('dashboard') }}" class="brand-link">
                <img src="{{ asset('img/Logo UTM berwarna.JPEG') }}" alt="Logo UTM"
                    class="brand-image img-circle elevation-3" style="opacity: .9">
                <span class="brand-text font-weight-light">LAB TIF UTM</span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
                    <div class="info">
                        @if (auth()->user()->role == 'superadmin')
                            <span class="badge badge-dark elevation-1">SUPER ADMIN</span>
                        @elseif(auth()->user()->role == 'kepala_lab')
                            <span class="badge badge-warning elevation-1">KEPALA LAB</span>
                        @elseif(auth()->user()->role == 'atasan')
                            <span class="badge badge-primary elevation-1">ATASAN</span>
                        @elseif(auth()->user()->role == 'admin')
                            <span class="badge badge-success elevation-1">ADMIN LAB</span>
                        @else
                            <span class="badge badge-success elevation-1">LABORAN</span>
                        @endif
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        {{-- MENU DASHBOARD (SEMUA ROLE) --}}
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard Utama</p>
                            </a>
                        </li>

                        {{-- KHUSUS SUPERADMIN: ADMINISTRASI SISTEM --}}
                        @if (auth()->user()->role === 'superadmin')
                            <li class="nav-header">ADMINISTRASI SISTEM</li>
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}"
                                    class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users-cog"></i>
                                    <p>Kelola Pengguna</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('master-tugas.index') }}"
                                    class="nav-link {{ request()->is('master-tugas*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-list-ul"></i>
                                    <p>Kelola Tugas Harian</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('hari-libur.index') }}"
                                    class="nav-link {{ request()->is('hari-libur*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-calendar-times"></i>
                                    <p>Kelola Hari Libur</p>
                                </a>
                            </li>

                            {{-- MENU MONITORING TAMBAHAN KHUSUS SUPERADMIN --}}
                            <li class="nav-header">MONITORING GLOBAL</li>
                            <li class="nav-item">
                                <a href="{{ route('tasks.index') }}"
                                    class="nav-link {{ request()->is('tasks*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tasks"></i>
                                    <p>Monitoring Tugas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('complains.index') }}"
                                    class="nav-link {{ request()->is('complains*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-hands-helping"></i>
                                    <p>Monitoring Komplain</p>
                                </a>
                            </li>
                        @endif

                        {{-- MENU UNTUK KEPALA LAB, ADMIN, & LABORAN (TIDAK BERUBAH) --}}
                        @if (in_array(auth()->user()->role, ['kepala_lab', 'admin', 'laboran']))
                            <li class="nav-header">OPERASIONAL LAB</li>
                            <li class="nav-item">
                                <a href="{{ route('tasks.index') }}"
                                    class="nav-link {{ request()->is('tasks*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>Daftar Tugas</p>
                                </a>
                            </li>
                        @endif

                        {{-- MENU LAPORAN (SEMUA KECUALI ATASAN) --}}
                        @if (auth()->user()->role !== 'atasan')
                            <li class="nav-header">PELAPORAN</li>
                            <li class="nav-item">
                                <a href="{{ route('reports.index') }}"
                                    class="nav-link {{ request()->is('laporan-tugas*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-pdf"></i>
                                    <p>Laporan Kinerja</p>
                                </a>
                            </li>
                        @endif

                        {{-- MENU UNTUK ATASAN & KEPALA LAB --}}
                        @if (in_array(auth()->user()->role, ['atasan', 'kepala_lab']))
                            <li class="nav-header">LAYANAN PUBLIK</li>
                            <li class="nav-item">
                                <a href="{{ route('complains.index') }}"
                                    class="nav-link {{ request()->is('complains*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-hands-helping"></i>
                                    <p>Layanan Komplain</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            <div id="sidebar-kontak" class="px-3 pb-3"
                style="position: absolute; bottom: 0; width: 100%; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px; background: inherit;">
                <h6 class="text-uppercase text-muted font-weight-bold"
                    style="font-size: 0.7rem; letter-spacing: 1px;">
                    Informasi lebih Lanjut hubungi</h6>

                <ul class="list-unstyled mt-2 mb-0" style="font-size: 0.8rem; color: #cbd5e1;">
                    <li class="mb-2 d-flex align-items-center">
                        <i class="fas fa-phone-alt text-info mr-2" style="width: 15px; text-align: center;"></i>
                        <span>031-3011147</span>
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="fas fa-envelope text-info mr-2" style="width: 15px; text-align: center;"></i>
                        <span>informatika @trunojoyo.ac.id</span>
                    </li>
                </ul>
            </div>

        </aside>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>@yield('title', 'Dashboard')</h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                @yield('content')
            </section>
        </div>

        {{-- FOOTER --}}
        <footer class="main-footer"
            style="background-color: transparent; border-top: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 0.85rem; padding: 15px 20px;">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-2 mb-md-0">
                    © {{ date('Y') }} <b style="color: #f8fafc; letter-spacing: 0.5px;">TIF LAB Workspace</b>.
                    Teknik Informatika UTM.
                </div>
                <div class="text-md-right text-muted" style="font-size: 0.8rem;">
                    <i class="fas fa-map-marker-alt text-danger mr-1"></i> <a
                        href="https://maps.app.goo.gl/iBVNoB6YaZia8qWQ6">Jl. Raya Telang PO BOX 2 Kamal Box 2, Kamal,
                        Bangkalan, Indonesia</a>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>


    <script>
        $(document).ready(function() {

            // ===== LOADING STATES =====
            function showLoading(button, text = 'Memproses...') {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>' + text;
                button.disabled = true;
                button.classList.add('btn-loading');
                button.setAttribute('data-original-text', originalText);
                return originalText;
            }

            function hideLoading(button) {
                const originalText = button.getAttribute('data-original-text');
                if (originalText) {
                    button.innerHTML = originalText;
                }
                button.disabled = false;
                button.classList.remove('btn-loading');
            }

            // ===== ENHANCED CONFIRMATION DIALOGS =====
            function showConfirmDialog(message, title = 'Konfirmasi', type = 'warning') {
                return new Promise((resolve) => {
                    // Create modal HTML
                    const modalHtml = `
                    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-${type}">
                                    <h5 class="modal-title text-white">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>${title}
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-0">${message}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fas fa-times mr-1"></i>Batal
                                    </button>
                                    <button type="button" class="btn btn-${type}" id="confirmBtn">
                                        <i class="fas fa-check mr-1"></i>Ya, Lanjutkan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                    const existingModal = document.getElementById('confirmModal');
                    if (existingModal) {
                        existingModal.remove();
                    }

                    document.body.insertAdjacentHTML('beforeend', modalHtml);

                    $('#confirmModal').modal('show');

                    document.getElementById('confirmBtn').addEventListener('click', function() {
                        $('#confirmModal').modal('hide');
                        resolve(true);
                    });

                    $('#confirmModal').on('hidden.bs.modal', function() {
                        $(this).remove();
                        resolve(false);
                    });
                });
            }

            // ===== NOTIFICATION SYSTEM =====
            function showNotification(message, type = 'info', duration = 5000) {
                let container = document.getElementById('notification-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'notification-container';
                    container.className = 'notification-container';
                    document.body.appendChild(container);
                }

                const notification = document.createElement('div');
                const iconClass = type === 'success' ? 'check-circle' :
                    type === 'error' ? 'exclamation-circle' :
                    type === 'warning' ? 'exclamation-triangle' : 'info-circle';

                notification.className =
                    `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show fade-in`;
                notification.innerHTML = `
                <i class="fas fa-${iconClass} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>×</span>
                </button>
            `;

                container.appendChild(notification);

                if (duration > 0) {
                    setTimeout(() => {
                        if (notification.parentNode) {
                            $(notification).alert('close');
                        }
                    }, duration);
                }
            }

            // ===== FORM ENHANCEMENTS =====
            function enhanceForms() {
                const phoneInputs = document.querySelectorAll('input[name="no_wa"]');
                phoneInputs.forEach(input => {
                    input.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/[^0-9+]/g, '');
                        if (value.startsWith('0')) {
                            value = '+62' + value.substring(1);
                        } else if (value.startsWith('62')) {
                            value = '+' + value;
                        }
                        e.target.value = value;
                    });
                });

                const emailInputs = document.querySelectorAll('input[name="email"]');
                emailInputs.forEach(input => {
                    input.addEventListener('blur', function(e) {
                        const email = e.target.value;
                        const errorElement = e.target.parentNode.querySelector('.email-error');

                        if (email && !email.toLowerCase().endsWith('@gmail.com')) {
                            if (!errorElement) {
                                const error = document.createElement('small');
                                error.className = 'text-danger email-error';
                                error.innerHTML =
                                    '<i class="fas fa-exclamation-circle mr-1"></i>Email harus menggunakan domain @gmail.com';
                                e.target.parentNode.appendChild(error);
                            }
                        } else {
                            if (errorElement) {
                                errorElement.remove();
                            }
                        }
                    });
                });

                const passwordInputs = document.querySelectorAll('input[name="password"]');
                passwordInputs.forEach(input => {
                    const strengthIndicator = document.createElement('div');
                    strengthIndicator.className = 'password-strength mt-1';
                    input.parentNode.appendChild(strengthIndicator);

                    input.addEventListener('input', function(e) {
                        const password = e.target.value;
                        let strength = 0;
                        let feedback = [];

                        if (password.length >= 8) strength++;
                        else feedback.push('Minimal 8 karakter');

                        if (/[A-Z]/.test(password)) strength++;
                        else feedback.push('Huruf besar');

                        if (/[a-z]/.test(password)) strength++;
                        else feedback.push('Huruf kecil');

                        if (/[0-9]/.test(password)) strength++;
                        else feedback.push('Angka');

                        if (/[^A-Za-z0-9]/.test(password)) strength++;
                        else feedback.push('Karakter khusus');

                        let strengthText = '';
                        let strengthClass = '';

                        if (strength <= 2) {
                            strengthText = 'Lemah';
                            strengthClass = 'text-danger';
                        } else if (strength <= 4) {
                            strengthText = 'Sedang';
                            strengthClass = 'text-warning';
                        } else {
                            strengthText = 'Kuat';
                            strengthClass = 'text-success';
                        }

                        strengthIndicator.innerHTML = `
                        <small class="${strengthClass}">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Kekuatan Password: ${strengthText}
                            ${feedback.length > 0 ? '<br><i class="fas fa-info-circle mr-1"></i>' + feedback.join(', ') : ''}
                        </small>
                    `;
                    });
                });
            }

            // ===== DATATABLES INITIALIZATION =====
            function initDataTables() {
                if ($('.tabel-datatable').length > 0) {
                    $('.tabel-datatable').DataTable({
                        "paging": true,
                        "pageLength": 10,
                        "lengthChange": false,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "responsive": true,
                        // Menambahkan opsi render langsung yang lebih agresif
                        "deferRender": true, 
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                            "search": "",
                            "searchPlaceholder": "Search...",
                            "paginate": {
                                "previous": "<i class='fas fa-chevron-left'></i>",
                                "next": "<i class='fas fa-chevron-right'></i>"
                            }
                        },
                        "initComplete": function() {
                            var $card = $(this).closest('.card');
                            var $header = $card.find('.card-header');
                            var $searchBar = $(this).closest('.dataTables_wrapper').find(
                                '.dataTables_filter');

                            $header.find('.dataTables_filter').remove();

                            if ($header.length > 0) {
                                $searchBar.insertAfter($header.find('.card-title'));
                            }
                            $(this).closest('.dataTables_wrapper').find('.row:first-child').hide();
                        }
                    });
                }
            }

            function setupAjaxDelete() {
                $(document).off('submit', 'form[data-ajax-delete]').on('submit', 'form[data-ajax-delete]', function(
                    e) {
                    e.preventDefault();

                    const form = $(this);
                    const url = form.attr('action');
                    const message = form.attr('data-confirm-message') || 'Yakin ingin menghapus data ini?';
                    const submitBtn = form.find('button[type="submit"]')[0];

                    TIFLab.showConfirmDialog(message, 'Konfirmasi Hapus', 'danger').then(confirmed => {
                        if (confirmed) {
                            TIFLab.showLoading(submitBtn, 'Menghapus...');

                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: {
                                    '_method': 'DELETE',
                                    '_token': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(data) {
                                    if (data.success) {
                                        TIFLab.showNotification(data.message,
                                            'success');
                                        const table = $('.tabel-datatable').DataTable();
                                        table.row(form.closest('tr')).remove().draw(
                                            false);
                                    } else {
                                        TIFLab.showNotification(data.message, 'error');
                                        TIFLab.hideLoading(
                                            submitBtn
                                        );
                                    }
                                },
                                error: function(xhr) {
                                    TIFLab.showNotification('Gagal menghapus data',
                                        'error');
                                    TIFLab.hideLoading(
                                        submitBtn
                                    );
                                }
                            });
                        }

                    });
                });
            }

            // ===== INITIALIZE ALL FEATURES =====
            function initializeApp() {
                enhanceForms();
                initDataTables();
                setupAjaxDelete();

                document.querySelectorAll('form:not([data-ajax-delete])').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        const submitBtn = form.querySelector('button[type="submit"]');
                        if (submitBtn && !submitBtn.disabled) {
                            TIFLab.showLoading(submitBtn);
                        }
                    });
                });

                const successMessage = document.querySelector('.alert-success');
                if (successMessage) {
                    showNotification(successMessage.textContent.trim(), 'success');
                }

                const errorMessage = document.querySelector('.alert-danger');
                if (errorMessage) {
                    showNotification(errorMessage.textContent.trim(), 'error');
                }

                $('[data-toggle="tooltip"]').tooltip();
                $('[data-toggle="popover"]').popover();

                $('.card').addClass('fade-in');

                $('.badge-danger').addClass('pulse');
            }

            initializeApp();

            // ===== EXPORT FUNCTIONS FOR GLOBAL USE =====
            window.TIFLab = {
                showConfirmDialog,
                showNotification,
                showLoading,
                hideLoading
            };
        });

        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
</body>

</html>