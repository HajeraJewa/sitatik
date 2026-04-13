<x-app-layout>
    <x-slot name="header"> Pusat Laporan </x-slot>
    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-6">
        <div class="flex-1 max-w-[1200px] mx-auto w-full flex flex-col space-y-6">

            {{-- HEADER --}}
            <div class="mt-4">
                <h2 class="text-2xl font-bold text-slate-800 uppercase tracking-tight">
                    Pusat Laporan
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Satu Data SITATIK - Sulawesi Tengah
                </p>
            </div>

            {{-- DATA --}}
            @php
                $reports = [
                    [
                        'id' => 'pengguna',
                        'title' => 'Laporan Pengguna',
                        'desc' => 'Daftar semua operator OPD',
                        'icon' => '👤',
                        'color' => 'blue',
                    ],
                    [
                        'id' => 'opd',
                        'title' => 'Laporan OPD',
                        'desc' => 'Master data Perangkat Daerah',
                        'icon' => '🏛️',
                        'color' => 'emerald',
                    ],
                    [
                        'id' => 'sumber_data',
                        'title' => 'Laporan Sumber Data',
                        'desc' => 'Katalog rujukan data statistik',
                        'icon' => '📊',
                        'color' => 'amber',
                    ],
                    [
                        'id' => 'rekomendasi',
                        'title' => 'Izin Rekomendasi',
                        'desc' => 'Rekapitulasi izin statistik',
                        'icon' => '📜',
                        'color' => 'rose',
                    ],
                ];
            @endphp

            {{-- GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                @foreach ($reports as $report)
                    <div
                        class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-xl hover:scale-[1.02] transition-all duration-300">

                        {{-- LEFT --}}
                        <div class="flex items-center space-x-4">
                            <div
                                class="text-2xl bg-{{ $report['color'] }}-50 text-{{ $report['color'] }}-600 w-14 h-14 flex items-center justify-center rounded-2xl transition-transform duration-300 group-hover:scale-110">
                                {{ $report['icon'] }}
                            </div>

                            <div>
                                <h4 class="font-bold text-slate-800 uppercase text-sm leading-tight">
                                    {{ $report['title'] }}
                                </h4>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    {{ $report['desc'] }}
                                </p>
                            </div>
                        </div>

                        {{-- FORM (TETAP SAMA LOGIC) --}}
                        <form action="{{ route('reports.generate') }}" method="POST" class="flex space-x-2">
                            @csrf
                            <input type="hidden" name="type" value="{{ $report['id'] }}">

                            {{-- PDF --}}
                            <button type="submit" name="format" value="pdf"
                                class="bg-{{ $report['color'] }}-600 text-white px-4 py-2 rounded-xl text-[11px] font-semibold hover:bg-{{ $report['color'] }}-700 transition flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>PDF</span>
                            </button>

                            {{-- EXCEL --}}
                            <button type="submit" name="format" value="excel"
                                class="border border-{{ $report['color'] }}-600 text-{{ $report['color'] }}-600 px-4 py-2 rounded-xl text-[11px] font-semibold hover:bg-{{ $report['color'] }}-50 transition flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>XLSX</span>
                            </button>
                        </form>

                    </div>
                @endforeach

            </div>
        </div>
    </div>
</x-app-layout>
