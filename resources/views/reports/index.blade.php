<x-app-layout>
  <div class="py-10 px-6">
    <div class="max-w-5xl mx-auto">
      <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-800 uppercase tracking-tighter leading-none">Pusat Laporan</h2>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">Satu Data SITATIK - Sulawesi Tengah
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @php
          $reports = [
            ['id' => 'pengguna', 'title' => 'Laporan Pengguna', 'desc' => 'Daftar semua operator OPD', 'icon' => '👤', 'color' => 'blue'],
            ['id' => 'opd', 'title' => 'Laporan OPD', 'desc' => 'Master data Perangkat Daerah', 'icon' => '🏛️', 'color' => 'emerald'],
            ['id' => 'sumber_data', 'title' => 'Laporan Sumber Data', 'desc' => 'Katalog rujukan data statistik', 'icon' => '📊', 'color' => 'amber'],
            ['id' => 'rekomendasi', 'title' => 'Izin Rekomendasi', 'desc' => 'Rekapitulasi izin statistik', 'icon' => '📜', 'color' => 'rose'],
          ];
        @endphp

        @foreach($reports as $report)
          <div
            class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-xl transition-all duration-300">
            <div class="flex items-center space-x-5">
              <div
                class="text-4xl bg-{{ $report['color'] }}-50 p-5 rounded-3xl group-hover:scale-110 transition-transform duration-300">
                {{ $report['icon'] }}
              </div>
              <div>
                <h4 class="font-black text-slate-800 uppercase text-sm leading-tight">{{ $report['title'] }}</h4>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">{{ $report['desc'] }}</p>
              </div>
            </div>

            {{-- FORM AKSI CETAK --}}
            <form action="{{ route('reports.generate') }}" method="POST" class="flex flex-col space-y-2">
              @csrf
              <input type="hidden" name="type" value="{{ $report['id'] }}">

              {{-- TOMBOL PDF --}}
              <button type="submit" name="format" value="pdf"
                class="bg-{{ $report['color'] }}-600 text-white px-5 py-3 rounded-2xl hover:bg-{{ $report['color'] }}-700 transition-all shadow-lg shadow-{{ $report['color'] }}-100 flex items-center justify-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-[10px] font-black uppercase tracking-tighter">PDF</span>
              </button>

              {{-- TOMBOL EXCEL --}}
              <button type="submit" name="format" value="excel"
                class="bg-white border-2 border-{{ $report['color'] }}-600 text-{{ $report['color'] }}-600 px-5 py-2 rounded-2xl hover:bg-{{ $report['color'] }}-50 transition-all flex items-center justify-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span
                  class="text-[10px] font-black uppercase tracking-tighter text-{{ $report['color'] }}-600">XLSX</span>
              </button>
            </form>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</x-app-layout>