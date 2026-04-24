<x-app-layout>
    <x-slot name="header"> Data Statistik </x-slot>

    {{-- Alpine.js: Tambahkan fungsi loadEditData untuk memetakan data ke form --}}
    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-4" 
        x-data="{ 
            openInput: false, 
            openView: false, 
            selectedTable: null, 
            columns: [], 
            content: [], 
            recommendation_id: null, 
            selectedYear: new Date().getFullYear(), 
            rows: [{}],

            loadEditData(stat, data) {
                this.selectedTable = stat.table_name;
                this.recommendation_id = stat.id;
                this.columns = stat.table_structure.split(',').map(c => c.trim());
                // Deep clone data agar perubahan di form tidak merusak tampilan preview sebelum di-save
                this.rows = JSON.parse(JSON.stringify(data)); 
                this.openInput = true;
            }
        }">
        
        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-4">

            {{-- HEADER & FILTER --}}
            <div class="mt-4 bg-white px-6 py-4 rounded-2xl border shadow-sm">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800"> Data Statistik </h1>
                        <p class="text-xs text-slate-500 mt-1"> Monitoring dan pengelolaan tabel statistik OPD </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <form action="{{ route('statistics.index') }}" method="GET" class="flex items-center space-x-2">
                            <select name="opd_id" onchange="this.form.submit()" class="appearance-none bg-slate-100 border border-gray-200 text-xs px-3 py-1.5 pr-8 rounded-lg focus:outline-none">
                                <option value="">OPD</option>
                                @foreach ($allOpd as $opd)
                                    <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>
                                        {{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- ... Filter Tahun & Kategori tetap sama ... --}}
                            <a href="{{ route('statistics.index') }}" class="bg-rose-50 text-rose-600 px-4 py-1.5 rounded-lg text-xs font-medium transition"> Reset </a>
                        </form>

                        @if (auth()->user()->role == 'operator')
                            <button type="button"
                                @click="openInput = true; recommendation_id = null; columns = []; rows = [{}];"
                                class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase shadow-lg hover:bg-blue-700 active:scale-95 transition-all ml-2">
                                + Input Baru
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- TABEL UTAMA --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-white border-b border-gray-100 font-bold uppercase text-[10px] text-slate-400">
                        <tr>
                            <th class="px-4 py-4 text-center border-r w-12">NO</th>
                            <th class="px-6 py-4 border-r w-56">KATEGORI</th>
                            <th class="px-6 py-4 border-r">KODE DAN NAMA TABEL</th>
                            <th class="px-4 py-4 text-center w-24 border-r">STATUS</th>
                            <th class="px-4 py-4 text-center w-36">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $groupedStats = $statistics->groupBy('user_id'); @endphp
                        @forelse($groupedStats as $userId => $stats)
                            @php $pd = $stats->first()->user->perangkatDaerah; @endphp
                            <tr class="bg-gray-100/80 border-b border-gray-200">
                                <td colspan="5" class="px-6 py-2.5 text-sm font-black text-slate-800 uppercase tracking-tight">
                                    {{ $pd->kode_opd ?? '0.0.0.0' }} - {{ $pd->nama_opd ?? 'PERANGKAT DAERAH' }}
                                </td>
                            </tr>

                            @foreach ($stats as $stat)
                                @php 
                                    $dataEntry = $stat->statisticData->first(); 
                                    $isFinal = $dataEntry ? $dataEntry->is_final : false;
                                @endphp
                                <tr class="hover:bg-slate-50 border-b border-gray-50 group">
                                    <td class="px-4 py-4 text-center text-slate-500 border-r font-medium">{{ $loop->iteration }}.</td>
                                    <td class="px-6 py-4 border-r text-slate-700 font-medium text-sm">{{ $stat->category ?? 'Sektoral' }}</td>
                                    <td class="px-6 py-4 border-r">
                                        <span class="text-slate-700 font-medium uppercase text-sm leading-relaxed">
                                            {{ $stat->table_code ?? '0.0.0.0' }} {{ $stat->table_name }}
                                        </span>
                                    </td>

                                    {{-- KOLOM STATUS --}}
                                    <td class="px-4 py-4 text-center border-r">
                                        @if($isFinal)
                                            <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm">FIXED</span>
                                        @elseif($dataEntry)
                                            <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm">DRAFT</span>
                                        @else
                                            <span class="text-slate-300 text-[9px] font-bold italic tracking-tighter">BELUM ISI</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex justify-center items-center space-x-2">
                                            @php 
                                                $dataEntry = $stat->statisticData->first(); 
                                                $isFinal = $dataEntry ? $dataEntry->is_final : false;
                                            @endphp

                                            @if ($dataEntry)
                                                {{-- 1. Tombol LIHAT (Selalu muncul jika ada data) --}}
                                                <button type="button" @click="
                                                        selectedTable = '{{ $stat->table_name }}'; 
                                                        recommendation_id = '{{ $stat->id }}';
                                                        columns = '{{ $stat->table_structure }}'.split(',').map(c => c.trim());
                                                        content = {{ json_encode($dataEntry->isi_data) }};
                                                        openView = true;
                                                    " class="text-emerald-600 hover:bg-emerald-50 p-1.5 rounded-lg transition" title="Lihat Data">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                </button>

                                                @if (auth()->user()->role == 'operator' && !$isFinal)
                                                    {{-- 2. Tombol EDIT (Hanya jika belum Final) --}}
                                                    <button type="button" @click="loadEditData({{ json_encode($stat) }}, {{ json_encode($dataEntry->isi_data) }})" 
                                                        class="text-amber-500 hover:bg-amber-50 p-1.5 rounded-lg transition" title="Edit/Lanjutkan Isi">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                    </button>

                                                    {{-- 3. Tombol KUNCI DATA (Hanya jika belum Final) --}}
                                                    <form action="{{ route('statistics.finalize', $dataEntry->id) }}" method="POST" onsubmit="return confirm('Kunci data ini?')">
                                                        @csrf
                                                        <button type="submit" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded-lg transition" title="Nyatakan Selesai">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- 4. Tombol EKSPOR (TETAP ADA meskipun sudah final) --}}
                                                <div x-data="{ openExport: false }" class="relative inline-block">
                                                    <button type="button" @click="openExport = !openExport" @click.away="openExport = false"
                                                        class="text-blue-700 p-1.5 hover:bg-blue-50 rounded-lg transition flex items-center focus:outline-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M12 16L16 12H13V4H11V12H8L12 16ZM19 18H5V11H3V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V11H19V18Z" />
                                                        </svg>
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>

                                                    <div x-show="openExport" x-transition 
                                                        class="absolute right-0 mt-2 min-w-[130px] bg-white rounded-xl shadow-2xl border border-slate-200 z-[100] overflow-hidden" 
                                                        x-cloak>
                                                        <div class="py-1">
                                                            <a href="{{ route('statistics.export-excel', $dataEntry->id) }}" 
                                                                class="flex items-center px-4 py-2 text-[11px] font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition">
                                                                EXCEL (.xlsx)
                                                            </a>
                                                            <hr class="border-slate-100">
                                                            <a href="{{ route('statistics.export-pdf', $dataEntry->id) }}" 
                                                                class="flex items-center px-4 py-2 text-[11px] font-bold text-slate-600 hover:bg-rose-50 hover:text-rose-700 transition">
                                                                PDF (.pdf)
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                            @else
                                                {{-- Tombol Isi Data Baru (Jika belum pernah diisi) --}}
                                                @if (auth()->user()->role == 'operator')
                                                    <button type="button" @click="recommendation_id = '{{ $stat->id }}'; columns = '{{ $stat->table_structure }}'.split(',').map(c => c.trim()); rows = [{}]; openInput = true;" 
                                                        class="text-blue-600 text-[10px] font-black uppercase hover:underline">
                                                        Isi Data Baru
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Data tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODAL INPUT & EDIT --}}
        <div x-show="openInput" class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak x-transition>
            <div class="bg-white rounded-3xl w-full max-w-5xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]" @click.away="openInput = false">
                <div class="p-6 bg-blue-600 text-white font-black uppercase text-xs flex justify-between items-center">
                    <span x-text="recommendation_id ? 'Edit Data: ' + selectedTable : 'Form Input Data Baru'"></span>
                    <button @click="openInput = false">✕</button>
                </div>
                <form action="{{ route('statistics.store') }}" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-6">
                    @csrf
                    <input type="hidden" name="recommendation_id" :value="recommendation_id">
                    <input type="hidden" name="tahun" :value="selectedYear">
                    
                    <div class="p-4 border-2 border-dashed border-blue-100 rounded-2xl bg-blue-50/50">
                        <label class="text-[10px] font-black text-blue-600 uppercase block mb-2 italic">Unggah Excel (Opsional - Akan menimpa data manual)</label>
                        <input type="file" name="excel_file" class="text-xs text-slate-500 file:bg-blue-600 file:text-white file:rounded-full file:border-0 file:px-4 file:py-1">
                    </div>

                    <div class="space-y-4" x-show="columns.length > 0">
                        <div class="overflow-x-auto border border-slate-100 rounded-2xl shadow-sm">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-slate-50 font-black text-slate-600 uppercase">
                                    <tr>
                                        <th class="px-4 py-3 border-b w-12 text-center">NO</th>
                                        <template x-for="col in columns" :key="col">
                                            <th class="px-4 py-3 border-b border-l border-slate-200" x-text="col"></th>
                                        </template>
                                        <th class="px-4 py-3 border-b border-l w-10"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in rows" :key="index">
                                        <tr class="hover:bg-blue-50/30 transition-colors">
                                            <td class="px-4 py-3 border-b text-center font-bold text-slate-300" x-text="index + 1"></td>
                                            <template x-for="col in columns" :key="col">
                                                <td class="px-4 py-3 border-b border-l border-slate-100">
                                                    <input type="text" :name="'data_content[' + index + '][' + col + ']'" 
                                                        x-model="row[col]" {{-- x-model mengisi input dengan data lama secara otomatis --}}
                                                        class="w-full border-transparent focus:ring-0 text-sm p-1">
                                                </td>
                                            </template>
                                            <td class="px-4 py-3 border-b border-l text-center">
                                                <button type="button" @click="rows.splice(index, 1)" class="text-rose-400 hover:text-rose-600 font-bold transition">✕</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" @click="rows.push({})" class="text-blue-600 font-bold text-[10px] uppercase hover:bg-blue-50 px-3 py-1.5 rounded-lg transition">+ Tambah Baris Baru</button>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-black py-4 rounded-2xl uppercase text-[10px] shadow-lg active:scale-95 transition-all">Simpan Perubahan Data</button>
                </form>
            </div>
        </div>

        {{-- MODAL LIHAT DATA (PREVIEW) --}}
        <div x-show="openView" class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak x-transition>
            <div class="bg-white rounded-3xl w-full max-w-6xl max-h-[90vh] overflow-hidden shadow-2xl flex flex-col" @click.away="openView = false">
                <div class="p-6 bg-emerald-600 text-white font-black uppercase text-xs flex justify-between items-center shadow-md">
                    <span x-text="'Preview Data: ' + selectedTable"></span>
                    <button @click="openView = false">✕</button>
                </div>
                <div class="p-6 overflow-y-auto flex-grow bg-slate-50/50">
                    <div class="border rounded-2xl overflow-hidden bg-white shadow-sm overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-50 font-black uppercase border-b text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 border-r w-12 text-center">No</th>
                                    <template x-for="col in columns" :key="col">
                                        <th class="px-4 py-3 border-r" x-text="col"></th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in content" :key="index">
                                    <tr class="hover:bg-slate-50 border-b last:border-0 text-slate-600">
                                        <td class="px-4 py-3 border-r text-center font-bold text-slate-300" x-text="index + 1"></td>
                                        <template x-for="col in columns" :key="col">
                                            <td class="px-4 py-3 border-r" x-text="row[col] || '-'"></td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        [x-cloak] { display: none !important; }
        .border-r { border-right-width: 1px; border-color: #f1f5f9; }
    </style>
</x-app-layout>