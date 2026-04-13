<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SITATIK+</title>

    @vite('resources/css/app.css')

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

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

    {{-- Background glow halus --}}
    <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-500/10 rounded-full blur-[140px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-indigo-500/10 rounded-full blur-[140px]"></div>

    <div class="w-full max-w-md px-6 relative z-10 fade-slide">

        {{-- CARD --}}
        <div class="bg-slate-900/70 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-800 p-10">

            {{-- HEADER --}}
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

            {{-- ERROR --}}
            @if ($errors->any())
                <div class="mb-4 p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-lg text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- FORM --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- EMAIL --}}
                <div>
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                        Email
                    </label>

                    <input type="email" name="email" placeholder="user@sultengprov.go.id" required autofocus
                        class="mt-1 w-full px-4 py-3 rounded-xl bg-slate-800 border border-slate-700 text-sm text-slate-200
                        focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition
                        placeholder:text-slate-500">
                </div>

                {{-- PASSWORD --}}
                <div>
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            Kata Sandi
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-blue-400 hover:underline">
                                Lupa
                            </a>
                        @endif
                    </div>

                    <div class="relative mt-1">
                        <input id="password" type="password" name="password" placeholder="••••••••" required
                            class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-slate-700 text-sm text-slate-200 pr-10
                            focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition">

                        {{-- TOGGLE PASSWORD --}}
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                                    c4.477 0 8.268 2.943 9.542 7
                                    -1.274 4.057-5.065 7-9.542 7
                                    -4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                        </button>
                    </div>
                </div>

                {{-- REMEMBER --}}
                <div class="flex items-center">
                    <label class="flex items-center text-sm text-slate-400">
                        <input type="checkbox" name="remember"
                            class="rounded border-slate-600 bg-slate-800 text-blue-400 focus:ring-blue-400">
                        <span class="ml-2">Ingat Saya</span>
                    </label>
                </div>

                {{-- BUTTON --}}
                <button type="submit"
                    class="w-full bg-blue-400 hover:bg-blue-300 text-slate-900 py-3 rounded-xl text-sm font-semibold
                    transition shadow-lg shadow-blue-500/20 active:scale-[0.98]">
                    Masuk
                </button>

                <a href="{{ url('/') }}"
                    class="block text-center text-xs text-slate-500 hover:text-blue-400 transition mt-3">
                    ← Kembali ke Beranda
                </a>
            </form>
        </div>

        {{-- FOOTER --}}
        <div class="text-center mt-6">
            <p class="text-[10px] text-slate-500 uppercase tracking-wider">
                &copy; {{ date('Y') }} Diskominfo Sulawesi Tengah
            </p>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        function togglePassword() {
            const input = document.getElementById('password');

            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }
    </script>

</body>

</html>
