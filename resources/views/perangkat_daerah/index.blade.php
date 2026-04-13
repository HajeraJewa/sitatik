<x-app-layout>
  <x-slot name="header"> Perangkat Daerah </x-slot>  
  <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-4" x-data="{ openModal: false, id: '', kode: '', nama: '', alias: '' }">

        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-4">

            {{-- HEADER --}}
            <div class="mt-4 bg-white px-6 py-4 rounded-2xl border shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">Perangkat Daerah</h1>
                        <p class="text-xs text-slate-500 mt-1">Pengelolaan data OPD SITATIK</p>
                    </div>

                    <button @click="openModal = true; id=''; kode=''; nama=''; alias=''"
                        class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase shadow-lg hover:bg-emerald-700 active:scale-95 transition-all">
                        + Tambah Instansi
                    </button>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left text-sm border-collapse">

                    <thead class="bg-white border-b border-gray-100 font-bold uppercase text-[10px] text-slate-400">
                        <tr>
                            <th class="px-6 py-4 text-center border-r w-40">KODE</th>
                            <th class="px-6 py-4 border-r">NAMA PERANGKAT DAERAH</th>
                            <th class="px-4 py-4 text-center w-32">AKSI</th>
                        </tr>
                    </thead>

                    <tbody class="text-[11px] text-slate-600">
                        @forelse($opds as $opd)
                            <tr class="hover:bg-slate-50 border-b border-gray-50">

                                {{-- KODE --}}
                                <td class="px-6 py-4 text-center font-bold text-slate-700 text-slate-800 font-medium text-[14px] border-r">
                                    {{ $opd->kode_opd }}
                                </td>

                                {{-- NAMA --}}
                                <td class="px-6 py-4 border-r">
                                    <p class="uppercase font-semibold text-slate-800 font-medium text-[14px] leading-tight">
                                        {{ $opd->nama_opd }}
                                    </p>

                                    @if ($opd->alias_opd)
                                        <p class="text-[10px] text-blue-600 font-bold uppercase italic mt-1">
                                            {{ $opd->alias_opd }}
                                        </p>
                                    @else
                                        <p class="text-[10px] text-slate-300 italic mt-1">
                                            Tanpa Alias
                                        </p>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td class="px-4 py-4 text-center">
                                    <div class="flex justify-center items-center space-x-2">

                                        {{-- EDIT --}}
                                        <button
                                            @click="openModal = true;
                          id='{{ $opd->id }}';
                          kode='{{ $opd->kode_opd }}';
                          nama='{{ $opd->nama_opd }}';
                          alias='{{ $opd->alias_opd }}';"
                                            class="text-amber-500 hover:bg-amber-50 p-1.5 rounded-lg transition"
                                            title="Edit">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>

                                        {{-- DELETE --}}
                                        <form action="{{ route('perangkat-daerah.destroy', $opd->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus data instansi ini? Semua akun operator terkait akan ikut terhapus.')">
                                            @csrf @method('DELETE')

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
                                    Data tidak tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        {{-- MODAL --}}
        <div x-show="openModal"
            class="fixed inset-0 z-[150] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak
            x-transition>

            <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl"
                @click.away="openModal = false">

                <div
                    class="p-6 bg-emerald-600 text-white font-black uppercase text-xs flex justify-between items-center">
                    <span x-text="id ? 'Edit Instansi' : 'Tambah Instansi Baru'"></span>
                    <button @click="openModal = false">✕</button>
                </div>

                <form action="{{ route('perangkat-daerah.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="id" x-model="id">

                    <input type="text" name="kode_opd" x-model="kode" placeholder="Kode OPD"
                        class="w-full rounded-xl border-gray-200 text-xs font-medium" required>

                    <input type="text" name="nama_opd" x-model="nama" placeholder="Nama OPD"
                        class="w-full rounded-xl border-gray-200 text-xs font-medium uppercase" required>

                    <input type="text" name="alias_opd" x-model="alias" placeholder="Alias"
                        class="w-full rounded-xl border-gray-200 text-xs font-medium uppercase">

                    <button type="submit"
                        class="w-full bg-emerald-600 text-white py-3 rounded-xl text-xs font-bold uppercase hover:bg-emerald-700 transition">
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