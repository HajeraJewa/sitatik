<nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="w-full px-6">
        <div class="flex justify-between h-16 items-center">

            {{-- LEFT: PAGE TITLE --}}
            <div>
                <h1 class="text-base font-semibold text-slate-500">
                    @isset($header)
                        {{ $header }}
                    @else
                        <span class="text-slate-400">-</span>
                    @endisset
                </h1>
            </div>

            {{-- RIGHT: USER --}}
            <div class="flex items-center gap-4">

                {{-- USER INFO --}}
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-sm font-medium text-slate-700">
                        {{ Auth::user()->name }}
                    </span>
                    <span class="text-xs text-slate-500">
                        {{ Auth::user()->role ?? 'User' }}
                    </span>
                </div>

                {{-- DROPDOWN --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="p-2 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

            </div>
        </div>
    </div>
</nav>