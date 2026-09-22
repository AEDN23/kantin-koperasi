<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Warung Koperasi</title>

    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9; /* Warna latar belakang abu-abu terang standar */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            padding: 15px;
        }

        .card {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
        }

        .password-toggle {
            cursor: pointer;
            background-color: #fff;
        }
        
        .password-toggle:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>

    <div class="login-box">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark mb-1">🏪 Warung Koperasi</h3>
            <p class="text-muted">Silakan login untuk melanjutkan</p>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                
                {{-- Alert Flash Message --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show small" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" autocomplete="off">
                    @csrf

                    <!-- Input Username -->
                    <div class="mb-3">
                        <label for="loginInput" class="form-label fw-semibold small">Nama / NIP / Username</label>
                        <input type="text" 
                               class="form-control @error('login') is-invalid @enderror" 
                               id="loginInput" 
                               name="login" 
                               value="{{ old('login') }}" 
                               required 
                               autofocus>
                    </div>

                    <!-- Input Password -->
                    <div class="mb-3">
                        <label for="passwordInput" class="form-label fw-semibold small">Password</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="passwordInput" 
                                   name="password" 
                                   required>
                            <span class="input-group-text password-toggle" id="togglePassword">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="rememberMe">
                                Ingat Saya
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        Login
                    </button>

                    <!-- Shortcut Transaksi Tanpa Login -->
                    <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary w-100 mb-4">
                        <i class="bi bi-cart"></i> Transaksi Baru (Tamu)
                    </a>
                </form>

                <!-- Petunjuk Login -->
                <div class="alert alert-secondary mb-0 p-3 small" style="border-radius: 6px;">
                    <strong>Petunjuk:</strong><br>
                    - Karyawan: Gunakan NIP atau Nama.<br>
                    - Password bawaan adalah NIP Anda.
                </div>

            </div>
        </div>
        
        <div class="text-center mt-3 text-muted small">
            &copy; {{ date('Y') }} Koperasi
        </div>
    </div>

    <!-- Script Bootstrap & Show/Hide Password -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');
        const toggleIcon = document.getElementById('toggleIcon');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'password') {
                    toggleIcon.classList.remove('bi-eye-slash');
                    toggleIcon.classList.add('bi-eye');
                } else {
                    toggleIcon.classList.remove('bi-eye');
                    toggleIcon.classList.add('bi-eye-slash');
                }
            });
        }
    </script>
</body>

</html>