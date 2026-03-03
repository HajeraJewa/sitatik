<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-2xl font-bold text-slate-800">SITATIK</h2>
        <p class="text-sm text-gray-600">Silakan login untuk mengakses data statistik</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required autofocus />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
        </div>

        <div class="flex items-center justify-between mt-6">
            <label class="inline-flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm">
                <span class="ms-2 text-sm text-gray-600">Ingat Saya</span>
            </label>
            <x-primary-button>Log in</x-primary-button>
        </div>
    </form>
</x-guest-layout>