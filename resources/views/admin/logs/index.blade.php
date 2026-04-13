<x-app-layout>
    <x-slot name="header"> Log Aktivitas </x-slot>

    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-6">

        <div class="flex-1 max-w-[1200px] mx-auto w-full flex flex-col space-y-6">

            {{-- HEADER --}}
            <div class="mt-4">
                <h2 class="text-2xl font-bold text-slate-800 uppercase tracking-tight">
                    Log Aktivitas
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Jejak Digital Sistem SITATIK
                </p>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

                <table class="w-full text-left border-collapse">

                    <thead class="bg-slate-50 border-b border-gray-100 text-[10px] font-semibold uppercase text-slate-400">
                        <tr>
                            <th class="px-6 py-4 w-44 border-r">Waktu</th>
                            <th class="px-6 py-4 border-r">Pelaku</th>
                            <th class="px-6 py-4 border-r w-48">Aktivitas</th>
                            <th class="px-6 py-4">Keterangan</th>
                        </tr>
                    </thead>

                    <tbody class="text-sm text-slate-600 divide-y divide-gray-50">

                        @foreach($logs as $log)
                            <tr class="hover:bg-slate-50 transition">

                                {{-- WAKTU --}}
                                <td class="px-6 py-4 border-r align-top">
                                    <div class="text-xs text-slate-500">
                                        {{ $log->created_at->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        {{ $log->created_at->format('H:i') }}
                                    </div>
                                </td>

                                {{-- USER --}}
                                <td class="px-6 py-4 border-r align-top">
                                    <p class="text-sm font-semibold text-slate-800 uppercase leading-tight">
                                        {{ $log->user->perangkatDaerah->alias_opd ?? 'SUPER ADMIN' }}
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1 italic">
                                        {{ $log->user->email }}
                                    </p>
                                </td>

                                {{-- AKTIVITAS --}}
                                <td class="px-6 py-4 border-r align-top">
                                    <span class="inline-block bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[11px] font-semibold border border-blue-100">
                                        {{ $log->activity }}
                                    </span>
                                </td>

                                {{-- DESKRIPSI --}}
                                <td class="px-6 py-4 align-top text-sm text-slate-600 leading-relaxed">
                                    {{ $log->description }}
                                </td>

                            </tr>
                        @endforeach

                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>

            </div>

        </div>

    </div>
</x-app-layout>