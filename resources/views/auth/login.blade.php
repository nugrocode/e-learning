<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Learning UKI Toraja</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo_ukit.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

</head>

<body>

    <div class="bg-login d-flex justify-content-center align-items-center min-h-screen">

        <div class="login-card p-4 mx-3 animate-fade-in-up w-full max-w-[360px]">

            <div class="text-center mb-3">
                <img src="{{ asset('images/logo_ukit.png') }}" alt="Logo UKI Toraja"
                    class="mx-auto h-16 w-auto drop-shadow-md">
            </div>

            <div class="text-center mb-4">
                <h4 class="font-bold text-gray-800 text-base">SELAMAT DATANG</h4>
                <p class="text-gray-500 text-xs">
                    E-Learning Universitas Kristen Indonesia Toraja
                </p>
            </div>

            @if (session('error'))
                <div class="alert alert-danger text-xs text-center py-2 shadow-sm border-0 rounded-lg mb-3">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ url('/login-proses') }}" method="POST" autocomplete="off">

                @csrf

                <div class="mb-3">
                    <label class="form-label text-xs font-bold text-gray-600 uppercase tracking-wide">NIM / NIDN</label>
                    <input type="text" name="nim" class="form-control py-2 text-sm" placeholder="Masukkan NIM / NIDN"
                        required autocomplete="off">
                </div>

                <div class="mb-4">
                    <label class="form-label text-xs font-bold text-gray-600 uppercase tracking-wide">Kata Sandi</label>
                    <input type="password" name="password" class="form-control py-2 text-sm" placeholder="Password" required>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit"
                        class="btn btn-custom-blue py-2 rounded-lg font-bold text-sm shadow-md transition transform hover:-translate-y-0.5">
                        Masuk
                    </button>
                </div>

                <div class="text-center">
                    <a href="#" class="text-xs text-blue-800 hover:text-blue-600 font-medium hover:underline">
                        Lupa kata sandi?
                    </a>
                </div>

            </form>
        </div>

        <div class="absolute bottom-4 text-white text-[10px] opacity-70 z-10">
            &copy; 2026 Universitas Kristen Indonesia Toraja
        </div>

    </div>

</body>

</html>