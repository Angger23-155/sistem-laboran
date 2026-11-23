@extends('layouts.admin')
@section('title', 'Tambah Pengguna Baru')
@section('content')
    <div class="container-fluid">
        <div class="card card-primary w-50 mx-auto">
            <div class="card-header">
                <h3 class="card-title">Form Tambah Pengguna</h3>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="@gmail.com" class="form-control" value="{{ old('email') }}" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>No. WhatsApp (Awali dengan 62 atau +62)</label>
                        <input type="text" name="no_wa" class="form-control" value="{{ old('no_wa') }}"
                            placeholder="Contoh: +628123456789" oninput="this.value = this.value.replace(/[^0-9+]/g, '');">
                        @error('no_wa')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label>Peran (Role)</label>
                        <select name="role" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="laboran" {{ old('role') == 'laboran' ? 'selected' : '' }}>Laboran</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="kepala_lab" {{ old('role') == 'kepala_lab' ? 'selected' : '' }}>Kepala Lab
                            </option>
                            <option value="atasan" {{ old('role') == 'atasan' ? 'selected' : '' }}>Atasan (Kaprodi / Dekan /
                                Perlengkapan)</option>
                            <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Super Admin
                            </option>
                        </select>
                    </div>
                    
                    {{-- FORM PASSWORD DENGAN FITUR MATA --}}
                    <div class="form-group">
                        <label for="password">Password Akun</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" required minlength="8">
                            <div class="input-group-append">
                                <span class="input-group-text bg-light" id="togglePassword" style="cursor: pointer;">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <a href="{{ route('users.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left mr-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fas fa-save mr-1"></i>Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JAVASCRIPT UNTUK FITUR MATA PASSWORD --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const passwordInput = document.querySelector('#password');
            const eyeIcon = document.querySelector('#eyeIcon');

            if (togglePassword) {
                togglePassword.addEventListener('click', function () {
                    // Ubah tipe input dari password ke text atau sebaliknya
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Ubah icon mata
                    if (type === 'text') {
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                    } else {
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>
@endsection