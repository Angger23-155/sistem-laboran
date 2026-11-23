<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | LAB TIF UTM</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <style>
        body.login-page {
            background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(37, 99, 235, 0.65)), url('{{ asset('img/Login UTM.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-logo a {
            color: #ffffff;
            font-weight: 800;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
            font-size: 1.8rem;
            letter-spacing: 1px;
        }

        .text-subtitle {
            font-size: 1.1rem;
            font-weight: 400;
            letter-spacing: 0.5px;
            color: #eff6ff;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            padding: 10px;
        }

        .input-group-text {
            background-color: transparent;
            border-right: none;
            color: #2563eb;
        }

        .form-control {
            border-left: none;
            border-radius: 8px;
            padding: 22px 15px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #2563eb;
        }

        .input-group {
            border: 1px solid #ced4da;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px !important;
        }

        .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
            border-radius: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.4);
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box" style="width: 420px;">

        <div class="login-logo mb-4">
            <a href="#"><b>TIF LAB Workspace</b><br><span class="text-subtitle">Teknik Informatika UTM</span></a>
        </div>

        <div class="card">
            <div class="card-body login-card-body" style="border-radius: 15px;">
                <div class="text-center mb-4 mt-2">
                    <img src="{{ asset('img/Logo UTM berwarna.JPEG') }}" alt="Logo UTM"
                        style="width: 80px; margin-bottom: 15px; drop-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <p class="text-muted" style="font-weight: 500;">Silakan masukkan kredensial Anda</p>
                </div>

                <form action="{{ route('login') }}" method="post">
                    @csrf

                    {{-- Input Email --}}
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                        </div>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="Alamat Email" required autofocus>
                    </div>
                    @error('email')
                        <span class="text-danger d-block mb-3"
                            style="font-size: 80%; margin-top: -15px;"><strong>{{ $message }}</strong></span>
                    @enderror

                    {{-- Input Password --}}
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                        </div>
                        {{-- Tambahkan id="password" di sini --}}
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="Kata Sandi"
                            required>
                        {{-- Tambahkan tombol mata di sini --}}
                        <div class="input-group-append">
                            <div class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                <span class="fas fa-eye" id="eyeIcon"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Notifikasi Error (Tetap Dipertahankan) --}}
                    @error('password')
                        <span class="text-danger d-block mb-3"
                            style="font-size: 80%; margin-top: -15px;"><strong>{{ $message }}</strong></span>
                    @enderror

                    {{-- Tombol Submit --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Masuk ke Sistem <i
                                    class="fas fa-sign-in-alt ml-1"></i></button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            const eyeIcon = document.querySelector('#eyeIcon');

            togglePassword.addEventListener('click', function() {
                // Ubah tipe input dari password ke teks atau sebaliknya
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
        });
    </script>

</body>

</html>
