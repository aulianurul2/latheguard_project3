<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="LatheGuard IIoT - Sistem Monitoring & Predictive Maintenance Mesin Bubut">

    <title>Masuk - LatheGuard IIoT</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('adminkit/css/app.css') }}" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f1c2e 0%, #13233a 55%, #193763 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Aksen dekorasi halus di latar belakang */
        body::before {
            content: "";
            position: fixed;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: rgba(29, 99, 237, 0.14);
            top: -120px;
            right: -120px;
            pointer-events: none;
        }

        body::after {
            content: "";
            position: fixed;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.08);
            bottom: -100px;
            left: -100px;
            pointer-events: none;
        }

        .login-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(8px);
            background: #ffffff;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(29, 99, 237, 0.18);
            color: #93c5fd;
            border: 1px solid rgba(147, 197, 253, 0.25);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .form-control {
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 0.95rem;
            border-color: #dbe2ea;
        }

        .form-control:focus {
            border-color: #1d63ed;
            box-shadow: 0 0 0 0.2rem rgba(29, 99, 237, 0.15);
        }

        .btn-login {
            background: #1d63ed;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.2px;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background: #164ec0;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(29, 99, 237, 0.35);
        }

        .password-toggle {
            border-radius: 0 10px 10px 0 !important;
            border-color: #dbe2ea;
            color: #64748b;
        }

        .input-group .form-control.has-toggle {
            border-radius: 10px 0 0 10px;
        }
    </style>
</head>

<body>
    <main class="d-flex w-100 py-4">
        <div class="container d-flex flex-column">
            <div class="row min-vh-100 align-items-center justify-content-center">
                <div class="col-sm-10 col-md-8 col-lg-5 col-xl-4">

                    <!-- Header Brand -->
                    <div class="text-center mb-4">
                        <div class="brand-badge mb-3">
                            <span>●</span>
                            <span>IIoT &bull; PREDICTIVE MAINTENANCE</span>
                        </div>
                        <h1 class="h2 fw-bold text-white mb-1">LatheGuard</h1>
                        <p class="text-white-50 mb-0" style="font-size: 0.92rem;">
                            Sistem Pemantauan &amp; Penjadwalan Servis Mesin Bubut
                        </p>
                    </div>

                    <!-- Card Form Login -->
                    <div class="card login-card">
                        <div class="card-body p-4 p-sm-5">
                            <div class="mb-4">
                                <h4 class="fw-bold text-dark mb-1">Selamat Datang</h4>
                                <p class="text-muted small mb-0">Masuk menggunakan akun panel administrator Anda.</p>
                            </div>

                            @if(session('status'))
                                <div class="alert alert-info py-2 px-3 small rounded-3">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ url('/login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold text-dark small">Alamat Email</label>
                                    <input id="email"
                                           type="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           placeholder="nama@bengkel.id"
                                           class="form-control @error('email') is-invalid @enderror"
                                           required
                                           autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold text-dark small">Kata Sandi</label>
                                    <div class="input-group">
                                        <input id="password"
                                               type="password"
                                               name="password"
                                               placeholder="Masukkan kata sandi"
                                               class="form-control has-toggle @error('password') is-invalid @enderror"
                                               required>
                                        <button class="btn btn-outline-secondary password-toggle"
                                                type="button"
                                                id="togglePassword"
                                                aria-label="Tampilkan atau sembunyikan kata sandi">
                                            Lihat
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                        <label class="form-check-label text-muted small" for="remember">
                                            Ingat saya
                                        </label>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-login text-white">
                                        Masuk ke Dashboard
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Footer Kecil -->
                    <div class="text-center mt-4">
                        <small class="text-white-50">
                            LatheGuard IIoT &copy; {{ date('Y') }} &bull; Bengkel Teknik Mesin
                        </small>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleBtn.textContent = isPassword ? 'Sembunyi' : 'Lihat';
                });
            }
        });
    </script>
</body>

</html>