@extends('layouts.admin')
@section('title', 'Edit Data Pengguna')
@section('content')
    <div class="container-fluid">
        <div class="card card-warning w-50 mx-auto">
            <div class="card-header">
                <h3 class="card-title">Form Edit Pengguna</h3>
            </div>
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}"
                            required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="@gmail.com" class="form-control" value="{{ old('email', $user->email) }}"
                            required>

                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>No. WhatsApp</label>
                        <input type="text" name="no_wa" class="form-control" value="{{ old('no_wa', $user->no_wa) }}"
                            placeholder="Contoh: +628123456789" oninput="this.value = this.value.replace(/[^0-9+]/g, '');">

                        @error('no_wa')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Peran (Role)</label>
                        <select name="role" class="form-control" required>
                            <option value="laboran" {{ old('role', $user->role) == 'laboran' ? 'selected' : '' }}>Laboran
                            </option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin
                            </option>
                            <option value="kepala_lab" {{ old('role', $user->role) == 'kepala_lab' ? 'selected' : '' }}>
                                Kepala Lab</option>
                            <option value="atasan" {{ old('role', $user->role) == 'atasan' ? 'selected' : '' }}>Atasan
                            </option>
                            <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>
                                Super Admin</option>
                        </select>
                    </div>
                    
                    {{-- BAGIAN RESET PASSWORD YANG SUDAH DILENGKAPI TOMBOL MATA --}}
                    <div class="form-group">
                        <label>Reset Password (Opsional)</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" 
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Kosongkan jika tidak ingin mengubah password">
                            <div class="input-group-append">
                                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </span>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">Isi hanya jika ingin mengganti password pengguna ini.</small>
                        
                        {{-- Notifikasi Error untuk Password (Jaga-jaga jika validasi gagal) --}}
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
                <div class="card-footer">
                    <a href="{{ route('users.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left mr-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-warning float-right">
                        <i class="fas fa-save mr-1"></i>Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT UNTUK TOMBOL MATA --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            const eyeIcon = document.querySelector('#eyeIcon');

            if (togglePassword && password && eyeIcon) {
                togglePassword.addEventListener('click', function () {
                    // Ubah tipe input dari password ke text atau sebaliknya
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    
                    // Ubah ikon mata terbuka / tertutup
                    if (type === 'password') {
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                    } else {
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                    }
                });
            }
        });
    </script>
@endsection