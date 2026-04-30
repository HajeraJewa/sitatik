<x-app-layout>
    <x-slot name="header"> Data Kategori </x-slot>

    <div 
        class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-4"
        x-data="{
            openModal:false,
            openDelete:false,
            deleteUrl:'',
            catId:'',
            kode:'',
            nama:'',
            isEdit:false
        }"
    >

        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-4 mt-4">

            {{-- HEADER --}}
            <div class="bg-white px-6 py-4 rounded-2xl border shadow-sm">
                <div class="flex flex-col lg:flex-row justify-between items-center gap-4">

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

                    <div class="flex items-center gap-2">

                        <form action="{{ route('categories.index') }}" method="GET">
                            <input
                                type="text"
                                name="search"
                                placeholder="Cari kategori..."
                                value="{{ request('search') }}"
                                class="bg-slate-100 border border-gray-200 text-xs px-3 py-1.5 rounded-lg focus:outline-none"
                            >
                        </form>

                        <button
                            @click="
                                openModal=true;
                                isEdit=false;
                                catId='';
                                kode='';
                                nama='';
                            "
                            class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-blue-700 transition"
                        >
                            + Tambah
                        </button>

                    </div>
                </div>
            </div>


            {{-- TABLE --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
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

                            <td class="px-4 py-4 text-center text-slate-500 border-r font-medium">
                                {{ $category->kode_kategori }}
                            </td>

                            <td class="px-6 py-4 border-r text-slate-700 font-medium text-sm uppercase">
                                {{ $category->nama_kategori }}
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex justify-center items-center space-x-2">

                                    {{-- EDIT --}}
                                    <button
                                        @click="
                                            openModal=true;
                                            isEdit=true;
                                            catId='{{ $category->id }}';
                                            kode='{{ $category->kode_kategori }}';
                                            nama='{{ $category->nama_kategori }}';
                                        "
                                        class="text-amber-500 hover:bg-amber-50 p-1.5 rounded-lg transition"
                                    >
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
                                    <button
                                        @click="
                                            openDelete=true;
                                            deleteUrl='{{ route('categories.destroy', $category->id) }}';
                                        "
                                        class="text-rose-500 hover:bg-rose-50 p-1.5 rounded-lg transition"
                                    >
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

                                </div>
                            </td>

                        </tr>

                        @empty
                        <tr>
                            <td colspan="3"
                                class="px-6 py-12 text-center text-slate-400 italic">
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
            class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
            x-cloak>

            <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl"
                @click.away="openModal=false">

                <div class="p-6 bg-blue-600 text-white font-semibold text-sm flex justify-between">
                    <span x-text="isEdit ? 'Edit Kategori' : 'Tambah Kategori'"></span>
                    <button @click="openModal=false">✕</button>
                </div>

                <form action="{{ route('categories.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <input type="hidden" name="category_id" x-model="catId">

                    <div>
                        <label class="text-xs text-slate-500">
                            Kode
                        </label>

                        <input
                            type="text"
                            name="kode_kategori"
                            x-model="kode"
                            class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2"
                            required
                        >
                    </div>

                    <div>
                        <label class="text-xs text-slate-500">
                            Nama Kategori
                        </label>

                        <input
                            type="text"
                            name="nama_kategori"
                            x-model="nama"
                            class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2"
                            required
                        >
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
                    Hapus Kategori?
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