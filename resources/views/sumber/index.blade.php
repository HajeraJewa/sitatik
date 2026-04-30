<x-app-layout>
    <x-slot name="header"> Sumber Data </x-slot>

    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-4"
        x-data="{
            openModal:false,
            openDelete:false,
            deleteUrl:'',
            selectedRec:'',
            sourceName:'',
            isEdit:false,
            actionUrl:'{{ route('sources.store') }}'
        }">

        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-4 mt-4">

            {{-- ALERT --}}
            @if (session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl text-xs">
                {{ session('success') }}
            </div>
            @endif


            {{-- HEADER --}}
            <div class="bg-white px-6 py-4 rounded-2xl border shadow-sm">
                <div class="flex flex-col lg:flex-row justify-between items-center gap-4">

                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Sumber Data
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Pengaturan sumber data statistik per tabel
                        </p>
                    </div>

                    <form action="{{ route('sources.index') }}" method="GET"
                        class="flex flex-wrap items-center gap-2">

                        {{-- OPD --}}
                        <select name="opd_id"
                            class="bg-slate-100 border border-gray-200 text-xs px-3 py-1.5 rounded-lg focus:outline-none">
                            <option value="">OPD</option>
                            @foreach ($allOpd as $opd)
                            <option value="{{ $opd->id }}"
                                {{ request('opd_id') == $opd->id ? 'selected' : '' }}>
                                {{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}
                            </option>
                            @endforeach
                        </select>

                        {{-- KATEGORI --}}
                        <select name="kategori"
                            class="bg-slate-100 border border-gray-200 text-xs px-3 py-1.5 rounded-lg focus:outline-none w-44">
                            <option value="">Kategori</option>
                            @foreach ($allCategories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ request('kategori') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                            @endforeach
                        </select>

                        {{-- TABEL --}}
                        <select name="table_name"
                            class="bg-slate-100 border border-gray-200 text-xs px-3 py-1.5 rounded-lg focus:outline-none w-44">
                            <option value="">Tabel</option>
                            @foreach ($listTables as $t)
                            <option value="{{ $t->table_name }}"
                                {{ request('table_name') == $t->table_name ? 'selected' : '' }}>
                                {{ Str::limit($t->table_name, 30) }}
                            </option>
                            @endforeach
                        </select>

                        <button type="submit"
                            class="bg-slate-800 text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-black transition">
                            Filter
                        </button>

                        @if (request()->anyFilled(['opd_id', 'table_name', 'kategori']))
                        <a href="{{ route('sources.index') }}"
                            class="bg-rose-50 text-rose-600 px-3 py-1.5 rounded-lg text-xs font-medium">
                            Reset
                        </a>
                        @endif

                    </form>

                </div>
            </div>


            {{-- TABLE --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm border-collapse">

                    <thead class="bg-white border-b border-gray-100 font-bold uppercase text-[10px] text-slate-400">
                        <tr>
                            <th class="px-4 py-4 text-center border-r w-12">No</th>
                            <th class="px-6 py-4 border-r">Nama Tabel Statistik</th>
                            <th class="px-6 py-4 border-r text-center">Sumber (OPD)</th>
                            <th class="px-6 py-4 border-r text-center">Update Terakhir</th>
                            <th class="px-4 py-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($sources as $index => $source)

                        <tr class="hover:bg-slate-50 border-b border-gray-50">

                            <td class="px-4 py-4 text-center text-slate-500 border-r font-medium">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-6 py-4 border-r text-slate-700 font-medium text-sm uppercase">
                                {{ $source->table_name }}
                            </td>

                            <td class="px-6 py-4 border-r text-center">
                                <div class="flex flex-col items-center gap-1">

                                    <span
                                        class="bg-blue-50 text-blue-600 px-2 py-1 rounded-lg text-[10px] font-semibold">
                                        {{ $source->user->perangkatDaerah->nama_opd ?? $source->user->name }}
                                    </span>

                                    @if ($source->data_source_name)
                                    <span class="text-xs text-slate-400 italic">
                                        {{ $source->data_source_name }}
                                    </span>
                                    @endif

                                </div>
                            </td>

                            <td class="px-6 py-4 border-r text-center text-sm text-slate-600">
                                {{ $source->updated_at->translatedFormat('d M Y') }}
                                <span class="text-slate-400 text-xs">
                                    {{ $source->updated_at->format('H:i') }}
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex justify-center items-center space-x-2">

                                    {{-- EDIT --}}
                                    <button
                                        @click="
                                        openModal = true;
                                        isEdit = true;
                                        selectedRec = '{{ $source->recommendation_id }}';
                                        sourceName = '{{ $source->data_source_name }}';
                                        actionUrl = '{{ route('sources.update', $source->id) }}';
                                    "
                                        class="text-amber-500 hover:bg-amber-50 p-1.5 rounded-lg transition">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 
                                                2.5 0 113.536 3.536L6.5 21.036H3v-3.572
                                                L16.732 3.732z"/>
                                        </svg>

                                    </button>

                                    {{-- DELETE --}}
                                    @if(auth()->user()->role == 'admin')
                                    <button
                                        @click="
                                        openDelete = true;
                                        deleteUrl = '{{ route('sources.destroy', $source->id) }}';
                                    "
                                        class="text-rose-500 hover:bg-rose-50 p-1.5 rounded-lg transition">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 
                                                0116.138 21H7.862a2 2 0 
                                                01-1.995-1.858L5 7m5 
                                                4v6m4-6v6m1-10V4a1 
                                                1 0 00-1-1h-4a1 1 0 
                                                00-1 1v3M4 7h16"/>
                                        </svg>

                                    </button>
                                    @endif

                                </div>
                            </td>

                        </tr>

                        @empty
                        <tr>
                            <td colspan="5"
                                class="px-6 py-12 text-center text-slate-400 italic">
                                Belum ada data sumber
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>


        {{-- MODAL FORM --}}
        <div x-show="openModal"
            class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
            x-cloak>

            <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl"
                @click.away="openModal=false">

                <div class="p-6 bg-blue-600 text-white font-semibold text-sm flex justify-between">
                    <span x-text="isEdit ? 'Edit Sumber Data' : 'Tambah Sumber Data'"></span>
                    <button @click="openModal=false">✕</button>
                </div>

                <form :action="actionUrl" method="POST" class="p-6 space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        @method('PUT')
                    </template>

                    <div>
                        <label class="text-xs text-slate-500">
                            Pilih Tabel
                        </label>

                        <select name="recommendation_id"
                            x-model="selectedRec"
                            class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2">
                            <option value="">-- Pilih --</option>

                            @foreach ($allRecommendations ?? [] as $rec)
                            <option value="{{ $rec->id }}">
                                {{ $rec->table_name }}
                                ({{ $rec->user->perangkatDaerah->nama_opd ?? $rec->user->name }})
                            </option>
                            @endforeach

                        </select>
                    </div>

                    <div>
                        <label class="text-xs text-slate-500">
                            Nama Sumber (Opsional)
                        </label>

                        <input
                            type="text"
                            name="data_source_name"
                            x-model="sourceName"
                            class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2">
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-blue-600 text-white py-2.5 rounded-xl text-xs font-semibold hover:bg-blue-700 transition">
                        Simpan
                    </button>

                </form>

            </div>
        </div>


        {{-- MODAL DELETE --}}
        <div x-show="openDelete"
            class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
            x-cloak>

            <div class="bg-white rounded-3xl w-full max-w-sm p-6 text-center shadow-xl"
                @click.away="openDelete=false">

                <h3 class="text-lg font-semibold text-slate-800 mb-2">
                    Hapus Sumber Data?
                </h3>

                <p class="text-xs text-slate-500 mb-6">
                    Data yang dihapus tidak dapat dikembalikan
                </p>

                <div class="flex gap-2">
                    <button
                        @click="openDelete=false"
                        class="flex-1 py-2 border rounded-xl text-xs">
                        Batal
                    </button>

                    <form :action="deleteUrl" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="w-full py-2 bg-rose-600 text-white rounded-xl text-xs">
                            Hapus
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>

<style>
[x-cloak]{display:none!important;}
</style>

</x-app-layout>