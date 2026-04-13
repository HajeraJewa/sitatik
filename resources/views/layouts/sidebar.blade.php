<aside class="w-60 bg-slate-900 text-white min-h-screen flex-shrink-0 shadow-xl flex flex-col">

    {{-- HEADER / BRAND --}}
    <div class="p-5 border-b border-slate-800">
        <h1 class="text-xl font-extrabold tracking-wide text-blue-400">
            SITATIK<span class="text-white">+</span>
        </h1>

        <p class="text-[13px] text-slate-500 leading-tight">
            Sistem Informasi Data Statistik
        </p>

        <p class="text-[9px] text-slate-600 uppercase tracking-widest mt-3">
            {{ Auth::user()->role === 'admin' ? 'Administrator' : 'Operator OPD' }}
        </p>
    </div>

    {{-- NAV --}}
    <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-1">

        {{-- MAIN --}}
        <div class="text-[9px] font-bold text-slate-500 uppercase px-3 pt-2 pb-1 tracking-wider">
            Menu Utama
        </div>

        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <span class="text-sm">Dashboard</span>
        </x-sidebar-link>

        <x-sidebar-link :href="route('statistics.index')" :active="request()->routeIs('statistics.index')">
            <span class="text-sm">Data Statistik</span>
        </x-sidebar-link>

        {{-- ADMIN ONLY --}}
        @if (Auth::user()->role === 'admin')
            <div class="text-[9px] font-bold text-slate-500 uppercase px-3 pt-4 pb-1 tracking-wider">
                Master Data
            </div>

            <x-sidebar-link href="{{ route('categories.index') }}" :active="request()->routeIs('categories.index')">
                <span class="text-sm">Kategori</span>
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('sources.index') }}" :active="request()->routeIs('sources.index')">
                <span class="text-sm">Sumber Data</span>
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('perangkat-daerah.index') }}" :active="request()->routeIs('perangkat-daerah.index')">
                <span class="text-sm">Perangkat Daerah</span>
            </x-sidebar-link>

            <div class="text-[9px] font-bold text-slate-500 uppercase px-3 pt-4 pb-1 tracking-wider">
                Laporan & Pengguna
            </div>

            <x-sidebar-link href="{{ route('reports.index') }}" :active="request()->routeIs('reports.index')">
                <span class="text-sm">Cetak Laporan</span>
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')">
                <span class="text-sm">Pengguna</span>
            </x-sidebar-link>
        @endif

        {{-- OTHER --}}
        <div class="text-[9px] font-bold text-slate-500 uppercase px-3 pt-4 pb-1 tracking-wider">
            Lainnya
        </div>

        <x-sidebar-link href="{{ route('recommendations.index') }}" :active="request()->routeIs('recommendations.index')">
            <span class="text-sm">Rekomendasi</span>
        </x-sidebar-link>

        <x-sidebar-link href="{{ route('activity-logs.index') }}" :active="request()->routeIs('activity-logs.index')">
            <span class="text-sm">Log Aktivitas</span>
        </x-sidebar-link>

    </nav>

    {{-- FOOTER (OPSIONAL, biar keliatan lebih "niat") --}}
    <div class="p-4 border-t border-slate-800 text-[9px] text-slate-500 text-center">
        © {{ date('Y') }} SITATIK+
    </div>

</aside>
