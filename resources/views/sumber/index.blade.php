<x-app-layout>
    <x-slot name="header"> Sumber Data </x-slot>

    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-4" x-data="{ openModal: false, openDelete: false, deleteUrl: '', selectedRec: '', sourceName: '' }">

        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-4">

            {{-- ALERT --}}
            @if (session('success'))
                <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl text-xs">
                    {{ session('success') }}
                </div>
            @endif

            {{-- HEADER --}}
            <div class="mt-4 bg-white px-6 py-4 rounded-2xl border shadow-sm">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">

                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Sumber Data
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Pengaturan sumber data statistik
                        </p>
                    </div>

                    <div class="flex items-center space-x-2">

                        {{-- FILTER --}}
                        <form action="{{ route('sources.index') }}" method="GET" class="flex items-center space-x-2">

                            {{-- OPD --}}
                            <select name="opd_id"
                                class="bg-slate-100 border border-gray-200 text-xs px-3 py-1.5 rounded-lg">
                                <option value="">OPD</option>
                                @foreach ($allOpd as $opd)
                                    <option value="{{ $opd->id }}"
                                        {{ request('opd_id') == $opd->id ? 'selected' : '' }}>
                                        {{ $opd->name }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- TABEL --}}
                            <select name="table_name"
                                class="bg-slate-100 border border-gray-200 text-xs px-3 py-1.5 rounded-lg">
                                <option value="">Tabel</option>
                                @foreach ($sources->unique('table_name') as $src)
                                    <option value="{{ $src->table_name }}"
                                        {{ request('table_name') == $src->table_name ? 'selected' : '' }}>
                                        {{ $src->table_name }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- KATEGORI --}}
                            <select name="kategori"
                                class="bg-slate-100 border border-gray-200 text-xs px-3 py-1.5 rounded-lg">
                                <option value="">Kategori</option>
                                @foreach (\App\Models\Category::all() as $cat)
                                    <option value="{{ $cat->nama_kategori }}"
                                        {{ request('kategori') == $cat->nama_kategori ? 'selected' : '' }}>
                                        {{ $cat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- BUTTON --}}
                            <button type="submit"
                                class="bg-slate-800 text-white px-4 py-1.5 rounded-lg text-xs font-medium hover:bg-black transition">
                                Filter
                            </button>

                            {{-- RESET --}}
                            @if (request()->anyFilled(['opd_id', 'table_name', 'kategori']))
                                <a href="{{ route('sources.index') }}"
                                    class="text-rose-500 text-xs font-medium px-2 hover:underline">
                                    Reset
                                </a>
                            @endif

                        </form>

                        @if (auth()->user()->role == 'admin')
                            <button @click="openModal = true; selectedRec=''; sourceName='';"
                                class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs hover:bg-emerald-700 transition">
                                + Tambah
                            </button>
                        @endif

                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left text-sm border-collapse">

                    <thead class="bg-white border-b border-gray-100 font-bold uppercase text-[10px] text-slate-400">
                        <tr>
                            <th class="px-4 py-4 text-center border-r w-12">No</th>
                            <th class="px-6 py-4 border-r">Tabel</th>
                            <th class="px-6 py-4 border-r text-center">Sumber</th>
                            <th class="px-6 py-4 border-r text-center">Tanggal</th>
                            <th class="px-4 py-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($sources as $index => $source)
                            <tr class="hover:bg-slate-50 border-b border-gray-50">

                                <td class="px-4 py-4 text-center text-slate-400 border-r">
                                    {{ $index + 1 }}
                                </td>

                                <td class="px-6 py-4 border-r">
                                    <span class="text-slate-700 font-medium text-[14px]">
                                        {{ $source->table_name }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 border-r text-center">
                                    <span class="text-blue-600 font-medium text-[14px]">
                                        {{ $source->data_source_name ?? $source->user->name }}
                                    </span>

                                    @if ($source->data_source_name)
                                        <p class="text-[10px] text-slate-400 mt-1">
                                            Ref: {{ $source->user->name }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-6 py-4 border-r text-center">
                                    <div class="flex flex-col font-medium text-[14px]">
                                        <span class="text-slate-700">
                                            {{ $source->updated_at->translatedFormat('d F Y') }}
                                        </span>
                                        <span class="text-slate-400 text-xs">
                                            {{ $source->updated_at->format('H:i') }} WITA
                                        </span>
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-center">
                                    <div class="flex justify-center items-center space-x-2">

                                        {{-- EDIT --}}
                                        <button
                                            @click="
                openModal = true;
                selectedRec = '{{ $source->id }}';
                sourceName = '{{ $source->data_source_name }}';
            "
                                            class="text-amber-500 hover:bg-amber-50 p-1.5 rounded-lg transition"
                                            title="Edit">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>

                                        </button>

                                        {{-- DELETE --}}
                                        @if (auth()->user()->role == 'admin')
                                            <button
                                                @click="
                openDelete = true;
                deleteUrl = '{{ route('sources.destroy', $source->id) }}';
            "
                                                class="text-rose-500 hover:bg-rose-50 p-1.5 rounded-lg transition"
                                                title="Hapus">

                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9z"
                                                        clip-rule="evenodd" />
                                                </svg>

                                            </button>
                                        @endif

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                                    Data tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        {{-- MODAL FORM --}}
        <div x-show="openModal"
            class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak
            x-transition>

            <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl"
                @click.away="openModal = false">

                <div class="p-5 bg-emerald-600 text-white text-sm font-semibold flex justify-between items-center">
                    <span x-text="selectedRec ? 'Edit Sumber' : 'Tambah Sumber'"></span>
                    <button @click="openModal = false">✕</button>
                </div>

                <form action="{{ route('sources.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label class="text-xs text-slate-500">Tabel Referensi</label>
                        <select name="recommendation_id" x-model="selectedRec"
                            class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($sources as $source)
                                <option value="{{ $source->id }}">
                                    {{ $source->table_name }} ({{ $source->user->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs text-slate-500">Nama Sumber</label>
                        <input type="text" name="nama_sumber" x-model="sourceName"
                            class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2" required>
                    </div>

                    <button type="submit"
                        class="w-full bg-emerald-600 text-white py-2.5 rounded-xl text-xs font-semibold hover:bg-emerald-700 transition">
                        Simpan
                    </button>

                </form>
            </div>
        </div>

        {{-- MODAL DELETE --}}
        <div x-show="openDelete"
            class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak
            x-transition>

            <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center shadow-xl"
                @click.away="openDelete = false">

                <h3 class="text-sm font-semibold text-slate-800 mb-2">
                    Hapus data?
                </h3>

                <p class="text-xs text-slate-500 mb-4">
                    Data tidak dapat dikembalikan.
                </p>

                <div class="flex space-x-2">
                    <button @click="openDelete = false" class="flex-1 py-2 border rounded-lg text-xs text-slate-500">
                        Batal
                    </button>

                    <form :action="deleteUrl" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2 bg-rose-600 text-white rounded-lg text-xs">
                            Hapus
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</x-app-layout>
