<aside class="w-60 bg-slate-900 text-white min-h-screen flex-shrink-0 shadow-xl overflow-y-auto">
  <div class="p-4 border-b border-slate-800">
    <h1 class="text-xl font-bold tracking-wider text-blue-400">SITATIK</h1>
    <p class="text-[9px] text-gray-500 uppercase tracking-widest mt-0.5">
      {{ Auth::user()->role === 'admin' ? 'Administrator' : 'Operator OPD' }}
    </p>
  </div>

  <nav class="mt-3 px-3 space-y-1">
    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
      <span class="text-sm">Dashboard</span>
    </x-sidebar-link>

    <x-sidebar-link :href="route('statistics.index')" :active="request()->routeIs('statistics.index')">
      <span class="text-sm">Data Statistik</span>
    </x-sidebar-link>

    @if(Auth::user()->role === 'admin')
      <div class="pt-3 pb-1 text-[9px] font-bold text-slate-500 uppercase px-3 tracking-tighter">Master Data</div>

      <x-sidebar-link href="{{ route('categories.index') }}" :active="request()->routeIs('categories.index')" class="py-1.5">
        <span class="text-sm">Kategori</span>
      </x-sidebar-link>
      <x-sidebar-link href="{{ route('sources.index') }}" :active="request()->routeIs('sources.index')" class="py-1.5">
        <span class="text-sm">Sumber Data</span>
      </x-sidebar-link>
      <x-sidebar-link href="{{ route('perangkat-daerah.index') }}" :active="request()->routeIs('perangkat-daerah.index')" class="py-1.5">
        <span class="text-sm">Perangkat Daerah</span>
      </x-sidebar-link>

      <div class="pt-3 pb-1 text-[9px] font-bold text-slate-500 uppercase px-3 tracking-tighter">Laporan & User</div>
      <x-sidebar-link href="{{ route('reports.index') }}" :active="request()->routeIs('reports.index')" class="py-1.5">
        <span class="text-sm">Cetak Laporan</span>
      </x-sidebar-link>
      <x-sidebar-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')" class="py-1.5">
        <span class="text-sm">Pengguna</span>
      </x-sidebar-link>
    @endif

    <div class="pt-3 pb-1 text-[9px] font-bold text-slate-500 uppercase px-3 tracking-tighter">Lainnya</div>

    <x-sidebar-link href="{{ route('recommendations.index') }}" :active="request()->routeIs('recommendations.index')" class="py-1.5">
      <span class="text-sm">Rekomendasi</span>
    </x-sidebar-link>

    <x-sidebar-link href="{{ route('activity-logs.index') }}" :active="request()->routeIs('activity-logs.index')" class="py-1.5">
      <span class="text-sm">Log Aktivitas</span>
    </x-sidebar-link>
  </nav>
</aside>