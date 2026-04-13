<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SITATIK+</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Animasi masuk */
        .fade-slide {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeSlide 0.6s ease forwards;
        }

        @keyframes fadeSlide {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-slate-950 min-h-screen flex items-center justify-center relative overflow-hidden">

    <!-- BG -->
    <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-500/10 rounded-full blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-indigo-500/10 rounded-full blur-[140px]"></div>

    <div class="w-full max-w-md px-6 relative z-10 fade-slide">

        <!-- CARD -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl">

            <!-- HEADER -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold tracking-tight">
                    <span class="text-blue-400">SITATIK</span>
                    <span class="text-white">+</span>
                </h1>

                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mt-2">
                    Sistem Informasi Data Statistik
                </p>

                <div class="h-[2px] w-10 bg-blue-500 mx-auto mt-3 rounded-full"></div>
            </div>

            <!-- DESKRIPSI -->
            <div class="mb-6 text-sm text-slate-400 text-center leading-relaxed font-medium">
                {{ __('Masukkan email Anda dan kami akan mengirimkan tautan reset untuk membuat yang baru.') }}
            </div>

            <!-- STATUS -->
            @if (session('status'))
                <div
                    class="mb-6 p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl text-blue-400 font-semibold text-center text-xs">
                    {{ session('status') }}
                </div>
            @endif

            <!-- FORM -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email"
                        class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 ml-1">
                        Alamat Email
                    </label>

                    <input id="email"
                        class="block w-full bg-[#1e293b]/50 border border-slate-700 text-white px-5 py-4 rounded-2xl 
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent transition 
                        placeholder:text-slate-600 shadow-inner"
                        type="email" name="email" placeholder="user@sultengprov.go.id" value="{{ old('email') }}"
                        required autofocus />

                    @if ($errors->has('email'))
                        <p class="mt-2 text-red-400 text-xs font-medium ml-1">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>

                <!-- BUTTON -->
                <div class="flex flex-col gap-5">
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold 
                        hover:bg-blue-700 transition-all shadow-blue-500/25 active:scale-[0.98]">
                        {{ __('Kirim Tautan Reset') }}
                    </button>

                    <a href="{{ route('login') }}"
                        class="text-center text-sm text-slate-500 hover:text-white transition-colors font-semibold">
                        ← Kembali ke Login
                    </a>
                </div>
            </form>
        </div>

        <!-- FOOTER -->
        <p class="text-center mt-10 text-[10px] text-slate-600 font-bold uppercase tracking-[0.2em]">
            © {{ date('Y') }} DISKOMINFO SULAWESI TENGAH
        </p>

    </div>

</body>

</html>
