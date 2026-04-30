<x-app-layout>
    <x-slot name="header"> Manajemen Pengguna </x-slot>

    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-4" x-data="{
        openModal: false,
        openDelete: false,
        deleteUrl: '',
        isEdit: false,
        userId: '',
        opd: '',
        email: '',
        lat: '',
        lng: '',
        actionUrl: '{{ route('users.store') }}'
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
                <div class="flex justify-between items-center gap-4">

                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Manajemen Pengguna
                        </h1>

                        <p class="text-xs text-slate-500 mt-1">
                            Total {{ count($users) }} akun operator
                        </p>
                    </div>

                    <button
                        @click="
openModal=true;
isEdit=false;
userId='';
opd='';
email='';
lat='';
lng='';
actionUrl='{{ route('users.store') }}';
"
                        class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-emerald-700 transition">
                        + Tambah
                    </button>

                </div>
            </div>


            {{-- TABLE --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm border-collapse">

                    <thead class="bg-white border-b border-gray-100 font-bold uppercase text-[10px] text-slate-400">
                        <tr>
                            <th class="px-4 py-4 text-center border-r w-12">No</th>
                            <th class="px-6 py-4 border-r">Perangkat Daerah</th>
                            <th class="px-6 py-4 border-r text-center">Koordinat</th>
                            <th class="px-4 py-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $index => $user)
                            <tr class="hover:bg-slate-50 border-b border-gray-50">

                                <td class="px-4 py-4 text-center text-slate-500 border-r font-medium">
                                    {{ $index + 1 }}
                                </td>

                                <td class="px-6 py-4 border-r text-slate-700 font-medium text-sm uppercase">

                                    <div class="flex flex-col">

                                        <span>
                                            {{ $user->perangkatDaerah->nama_opd ?? $user->name }}
                                        </span>

                                        <span class="text-xs text-slate-400 lowercase">
                                            {{ $user->email }}
                                        </span>

                                    </div>

                                </td>

                                <td class="px-6 py-4 border-r text-center">
                                    <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-lg font-medium">
                                        {{ $user->latitude ?? '0' }}, {{ $user->longitude ?? '0' }}
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex justify-center items-center space-x-2">

                                        {{-- EDIT --}}
                                        <button
                                            @click="
openModal=true;
isEdit=true;
userId='{{ $user->id }}';
opd='{{ $user->perangkat_daerah_id }}';
email='{{ $user->email }}';
lat='{{ $user->latitude }}';
lng='{{ $user->longitude }}';
actionUrl='{{ route('users.update', $user->id) }}';
"
                                            class="text-amber-500 hover:bg-amber-50 p-1.5 rounded-lg transition"
                                            title="Edit">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5
2.5 0 113.536 3.536L6.5 21.036H3v-3.572
L16.732 3.732z" />
                                            </svg>

                                        </button>


                                        {{-- DELETE --}}
                                        <button
                                            @click="
openDelete=true;
deleteUrl='{{ route('users.destroy', $user->id) }}';
"
                                            class="text-rose-500 hover:bg-rose-50 p-1.5 rounded-lg transition"
                                            title="Hapus">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0
0116.138 21H7.862a2 2 0
01-1.995-1.858L5 7m5
4v6m4-6v6m1-10V4a1
1 0 00-1-1h-4a1 1 0
00-1 1v3M4 7h16" />
                                            </svg>

                                        </button>

                                    </div>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">
                                    Belum ada operator
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>


        {{-- MODAL FORM --}}
        <div x-show="openModal"
            class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak>

            <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl" @click.away="openModal=false">

                <div class="p-6 bg-emerald-600 text-white font-semibold text-sm flex justify-between">

                    <span x-text="isEdit ? 'Edit Pengguna' : 'Tambah Pengguna'"></span>

                    <button @click="openModal=false">✕</button>
                </div>

                <form :action="actionUrl" method="POST" class="p-6 space-y-4">
                    @csrf

                    <template x-if="isEdit">
                        @method('PUT')
                    </template>

                    <select name="perangkat_daerah_id" x-model="opd"
                        class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2">

                        <option value="">Pilih OPD</option>

                        @foreach ($all_opd as $opd)
                            <option value="{{ $opd->id }}">
                                {{ $opd->kode_opd }} - {{ $opd->nama_opd }}
                            </option>
                        @endforeach

                    </select>

                    <input type="email" name="email" x-model="email"
                        class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2"
                        placeholder="Email">

                    <input type="password" name="password"
                        class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2"
                        placeholder="Password baru (opsional)">

                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="latitude" x-model="lat"
                            class="bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2"
                            placeholder="Latitude">

                        <input type="text" name="longitude" x-model="lng"
                            class="bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2"
                            placeholder="Longitude">
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
            class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak>

            <div class="bg-white rounded-3xl w-full max-w-sm p-6 text-center shadow-xl" @click.away="openDelete=false">

                <h3 class="text-lg font-semibold text-slate-800 mb-2">
                    Hapus Pengguna?
                </h3>

                <p class="text-xs text-slate-500 mb-6">
                    Data yang dihapus tidak dapat dikembalikan
                </p>

                <div class="flex gap-2">

                    <button @click="openDelete=false" class="flex-1 py-2 border rounded-xl text-xs">
                        Batal
                    </button>

                    <form :action="deleteUrl" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="w-full py-2 bg-rose-600 text-white rounded-xl text-xs">
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
