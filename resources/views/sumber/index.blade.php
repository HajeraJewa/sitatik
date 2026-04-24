<x-app-layout>
    <x-slot name="header"> Sumber Data </x-slot>

    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-4" x-data="{ openModal: false, openDelete: false, deleteUrl: '', selectedRec: '', sourceName: '' ,
            isEdit: false,
            actionUrl: '{{ route('sources.store') }}'
        }">

        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-4">

            {{-- ALERT --}}
            @if (session('success'))
                <div
                    class="mt-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl text-xs flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- HEADER --}}
            <div class="mt-4 bg-white px-6 py-4 rounded-2xl border shadow-sm">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-slate-800">Sumber Data</h1>
                        <p class="text-xs text-slate-500 mt-1">Pengaturan sumber data statistik per tabel</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        {{-- FILTER --}}
                        <form action="{{ route('sources.index') }}" method="GET"
                            class="flex flex-wrap items-center gap-2">

                            {{-- 1. Filter OPD --}}
                            <select name="opd_id"
                                class="bg-slate-50 border border-gray-200 text-[11px] font-semibold px-3 py-1.5 rounded-lg focus:ring-2 focus:ring-blue-100">
                                <option value="">Pilih OPD</option>
                                @foreach ($allOpd as $opd)
                                    <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>
                                        {{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- 2. Filter Kategori --}}
                            <select name="kategori"
                                class="bg-slate-50 border border-gray-200 text-[11px] font-semibold px-3 py-1.5 rounded-lg focus:ring-2 focus:ring-blue-100 w-44">
                                <option value="">Pilih Kategori</option>
                                @foreach ($allCategories as $cat)
                                    {{-- Gunakan $cat->id sebagai value --}}
                                    <option value="{{ $cat->id }}" {{ request('kategori') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- 3. Filter Nama Tabel --}}
                            <select name="table_name"
                                class="bg-slate-50 border border-gray-200 text-[11px] font-semibold px-3 py-1.5 rounded-lg focus:ring-2 focus:ring-blue-100 w-44">
                                <option value="">Pilih Tabel</option>
                                @foreach ($listTables as $t)
                                    <option value="{{ $t->table_name }}" {{ request('table_name') == $t->table_name ? 'selected' : '' }}>
                                        {{ Str::limit($t->table_name, 30) }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit"
                                class="bg-slate-800 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-black transition shadow-sm">
                                Filter
                            </button>

                            @if (request()->anyFilled(['opd_id', 'table_name', 'kategori']))
                                <a href="{{ route('sources.index') }}"
                                    class="text-rose-500 text-xs font-bold px-2 hover:underline">Reset</a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left text-sm border-collapse">
                    <thead
                        class="bg-slate-50/50 border-b border-gray-100 font-black uppercase text-[10px] text-slate-400">
                        <tr>
                            <th class="px-4 py-4 text-center border-r w-12">No</th>
                            <th class="px-6 py-4 border-r">Nama Tabel Statistik</th>
                            <th class="px-6 py-4 border-r text-center">Sumber (OPD)</th>
                            <th class="px-6 py-4 border-r text-center">Update Terakhir</th>
                            <th class="px-4 py-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($sources as $index => $source)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    {{-- Nomor --}}
                                    <td class="px-3 py-3 text-center text-slate-400 font-semibold text-[11px] border-r w-12">
                                        {{ $index + 1 }}
                                    </td>

                                    {{-- Nama Tabel --}}
                                    <td class="px-4 py-3 border-r text-center">
                                        <div class="flex flex-col items-center">
                                            <span
                                                class="text-slate-700 font-bold text-[12px] leading-tight uppercase tracking-tight">
                                                {{ $source->table_name }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- OPD & Keterangan --}}
                                    <td class="px-4 py-3 border-r text-center">
                                        <div class="flex flex-col items-center">
                                            <span
                                                class="inline-block px-2 py-0.5 bg-blue-50 text-blue-600 rounded-md font-bold text-[10px] uppercase border border-blue-100">
                                                {{ $source->user->perangkatDaerah->nama_opd ?? $source->user->name }}
                                            </span>
                                            @if ($source->data_source_name)
                                                <p class="text-[9px] text-slate-400 mt-1 font-semibold italic leading-none">
                                                    Ket: {{ $source->data_source_name }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Update Terakhir (Dibuat sebaris) --}}
                                    <td class="px-4 py-3 border-r text-center whitespace-nowrap">
                                        <span class="text-slate-600 font-bold text-[11px]">
                                            {{ $source->updated_at->translatedFormat('d M Y') }}
                                        </span>
                                        <span class="text-slate-400 text-[10px] font-medium ml-1">
                                            {{ $source->updated_at->format('H:i') }} WITA
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-3 py-3 text-center w-24">
                                        <div class="flex justify-center items-center gap-1">
                                            <button @click="
                                openModal = true; 
                                isEdit = true;
                                selectedRec = '{{ $source->recommendation_id }}';
                                sourceName = '{{ $source->data_source_name }}';
                                actionUrl = '{{ route('sources.update', $source->id) }}';
                            " class="text-amber-500 hover:bg-amber-50 p-1.5 rounded-lg transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>

                                            @if (auth()->user()->role == 'admin')
                                                <button
                                                    @click="openDelete = true; deleteUrl = '{{ route('sources.destroy', $source->id) }}';"
                                                    class="text-rose-500 hover:bg-rose-50 p-1.5 rounded-lg transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="px-6 py-12 text-center text-[10px] font-bold uppercase text-slate-300">
                                    Belum ada data sumber
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODAL FORM (TAMBAH & EDIT) --}}
        <div x-show="openModal"
            class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak
            x-transition>
            <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl"
                @click.away="openModal = false">
                <div class="p-5 bg-slate-800 text-white text-sm font-bold flex justify-between items-center">
                    <span x-text="isEdit ? 'PERBARUI SUMBER DATA' : 'TAMBAH SUMBER DATA BARU'"></span>
                    <button @click="openModal = false" class="hover:rotate-90 transition-transform">✕</button>
                </div>

                <form :action="actionUrl" method="POST" class="p-6 space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        @method('PUT')
                    </template>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Pilih
                            Tabel Referensi</label>
                        <select name="recommendation_id" x-model="selectedRec"
                            class="w-full bg-slate-50 border border-gray-200 rounded-xl text-sm px-4 py-2.5 focus:ring-2 focus:ring-blue-100"
                            required>
                            <option value="">-- Pilih Tabel --</option>
                            @foreach ($allRecommendations ?? [] as $rec)
                                <option value="{{ $rec->id }}">{{ $rec->table_name }}
                                    ({{ $rec->user->perangkatDaerah->nama_opd ?? $rec->user->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Nama
                            Sumber Manual (Opsional)</label>
                        <input type="text" name="data_source_name" x-model="sourceName"
                            placeholder="Contoh: Bidang Statistik"
                            class="w-full bg-slate-50 border border-gray-200 rounded-xl text-sm px-4 py-2.5 focus:ring-2 focus:ring-blue-100">
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-3 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-100">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL DELETE --}}
        <div x-show="openDelete"
            class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak
            x-transition>
            <div class="bg-white rounded-[2rem] w-full max-w-sm p-8 text-center shadow-xl"
                @click.away="openDelete = false">
                <div
                    class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    ⚠️</div>
                <h3 class="text-base font-bold text-slate-800 mb-2 uppercase tracking-tight">Hapus Sumber Data?</h3>
                <p class="text-xs text-slate-500 mb-6 font-medium">Tindakan ini tidak dapat dibatalkan. Pastikan anda
                    yakin.</p>

                <div class="flex space-x-3">
                    <button @click="openDelete = false"
                        class="flex-1 py-3 border border-slate-100 rounded-2xl text-xs font-bold text-slate-400 uppercase hover:bg-slate-50 transition">Batal</button>
                    <form :action="deleteUrl" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full py-3 bg-rose-600 text-white rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-rose-700 transition shadow-lg shadow-rose-100">Hapus</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
    </style>
</x-app-layout>