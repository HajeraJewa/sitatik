<x-app-layout>
    <x-slot name="header"> Data Kategori </x-slot>

    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-4" x-data="{ openModal: false, catId: '', kode: '', nama: '' }">

        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-4">

            {{-- HEADER --}}
            <div class="mt-4 bg-white px-6 py-4 rounded-2xl border shadow-sm">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">

                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Data Kategori
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Total kategori:
                            <span class="font-semibold text-emerald-600">
                                {{ $totalKategori }}
                            </span>
                        </p>
                    </div>

                    <div class="flex items-center space-x-2">

                        {{-- SEARCH --}}
                        <form action="{{ route('categories.index') }}" method="GET">
                            <input type="text" name="search" placeholder="Cari kategori..."
                                value="{{ request('search') }}"
                                class="bg-slate-100 border border-gray-200 text-xs px-3 py-1.5 rounded-lg focus:ring-0">
                        </form>

                        {{-- BUTTON --}}
                        <button @click="openModal = true; catId=''; kode=''; nama='';"
                            class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-medium hover:bg-emerald-700 transition">
                            + Tambah
                        </button>

                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left text-sm border-collapse">

                    <thead class="bg-white border-b border-gray-100 font-bold uppercase text-[10px] text-slate-400">
                        <tr>
                            <th class="px-4 py-4 text-center border-r w-32">Kode</th>
                            <th class="px-6 py-4 border-r">Nama Kategori</th>
                            <th class="px-4 py-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-50 border-b border-gray-50">

                                <td class="px-4 py-4 text-center text-slate-600 border-r font-medium">
                                    {{ $category->kode_kategori }}
                                </td>

                                <td class="px-6 py-4 border-r text-slate-700 font-medium text-[11px] uppercase">
                                    {{ $category->nama_kategori }}
                                </td>

                                <td class="px-4 py-4 text-center">
                                    <div class="flex justify-center items-center space-x-2">

                                        {{-- EDIT --}}
                                        <button
                                            @click="
                openModal = true;
                catId = '{{ $category->id }}';
                kode = '{{ $category->kode_kategori }}';
                nama = '{{ $category->nama_kategori }}';
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
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus kategori?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="text-rose-500 hover:bg-rose-50 p-1.5 rounded-lg transition"
                                                title="Hapus">

                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9z"
                                                        clip-rule="evenodd" />
                                                </svg>

                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">
                                    Data tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        {{-- MODAL --}}
        <div x-show="openModal"
            class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak
            x-transition>

            <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl"
                @click.away="openModal = false">

                <div class="p-5 bg-emerald-600 text-white text-sm font-semibold flex justify-between items-center">
                    <span x-text="catId ? 'Edit Kategori' : 'Tambah Kategori'"></span>
                    <button @click="openModal = false">✕</button>
                </div>

                <form action="{{ route('categories.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <input type="hidden" name="category_id" x-model="catId">

                    <div>
                        <label class="text-xs text-slate-500">Kode</label>
                        <input type="text" name="kode_kategori" x-model="kode"
                            class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2" required>
                    </div>

                    <div>
                        <label class="text-xs text-slate-500">Nama Kategori</label>
                        <input type="text" name="nama_kategori" x-model="nama"
                            class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2" required>
                    </div>

                    <button type="submit"
                        class="w-full bg-emerald-600 text-white py-2.5 rounded-xl text-xs font-semibold hover:bg-emerald-700 transition">
                        Simpan
                    </button>
                </form>

            </div>
        </div>

    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</x-app-layout>
