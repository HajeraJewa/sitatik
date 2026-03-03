<x-app-layout>
  <div class="py-10 px-6" x-data>
    <div class="max-w-7xl mx-auto">
      <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tighter">Log Aktivitas</h2>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Jejak Digital Sistem SITATIK</p>
      </div>

      <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead class="bg-slate-50 text-[10px] font-black uppercase text-slate-400">
            <tr>
              <th class="px-6 py-5">Waktu</th>
              <th class="px-6 py-5">Pelaku & Instansi</th>
              <th class="px-6 py-5">Aktivitas</th>
              <th class="px-6 py-5">Detail Keterangan</th>
            </tr>
          </thead>
          <tbody class="text-xs font-bold text-slate-600 divide-y divide-gray-50">
            @foreach($logs as $log)
              <tr class="hover:bg-blue-50/30 transition-colors">
                <td class="px-6 py-5 text-slate-400 uppercase italic">
                  {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                </td>
                <td class="px-6 py-5">
                  <p class="text-slate-800 uppercase font-black leading-tight">
                    {{ $log->user->perangkatDaerah->alias_opd ?? 'SUPER ADMIN' }}
                  </p>
                  <p class="text-[9px] text-blue-500 font-medium italic lowercase">{{ $log->user->email }}</p>
                </td>
                <td class="px-6 py-5">
                  <span
                    class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[9px] font-black uppercase border border-blue-100">
                    {{ $log->activity }}
                  </span>
                </td>
                <td class="px-6 py-5 text-slate-500 font-medium leading-relaxed">
                  {{ $log->description }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div class="p-6 bg-slate-50">
          {{ $logs->links() }}
        </div>
      </div>
    </div>
  </div>
</x-app-layout>