<x-app-layout>
    <x-slot name="header"> Manajemen Pengguna </x-slot>

    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-4" x-data="{ openAdd: false }">

        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-4">

            {{-- ALERT --}}
            @if (session('success'))
                <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl text-xs">
                    {{ session('success') }}
                </div>
            @endif

            {{-- HEADER --}}
            <div class="mt-4 bg-white px-6 py-4 rounded-2xl border shadow-sm">
                <div class="flex justify-between items-center">

                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Manajemen Pengguna
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Total {{ count($users) }} akun operator
                        </p>
                    </div>

                    <button @click="openAdd = true"
                        class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs hover:bg-blue-700 transition">
                        + Tambah
                    </button>

                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left text-sm border-collapse">

                    <thead class="bg-white border-b border-gray-100 font-bold uppercase text-[10px] text-slate-400">
                        <tr>
                            <th class="px-4 py-4 text-center border-r w-12">No</th>
                            <th class="px-6 py-4 border-r">Perangkat Daerah</th>
                            <th class="px-6 py-4 border-r text-center">Koordinat</th>
                            <th class="px-6 py-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $index => $user)
                            <tr x-data="{ openEdit: false }" class="hover:bg-slate-50 border-b border-gray-50">
                                <td class="px-4 py-4 text-center text-slate-400 border-r font-medium">
                                    {{ $index + 1 }}
                                </td>

                                {{-- NAMA --}}
                                <td class="px-4 py-4 border-r">
                                    <span class="text-slate-700 font-medium text-[14px] uppercase">
                                        {{ $user->perangkatDaerah->nama_opd ?? $user->name }}
                                    </span>

                                    <p class="text-[11px] text-blue-500 italic mt-1 lowercase">
                                        {{ $user->email }}
                                    </p>
                                </td>

                                {{-- KOORDINAT --}}
                                <td class="px-6 py-4 border-r text-center">
                                    <span
                                        class="text-[12px] font-mono bg-blue-50 text-blue-600 px-3 py-1 rounded-full border border-blue-100">
                                        {{ $user->latitude ?? '0' }}, {{ $user->longitude ?? '0' }}
                                    </span>
                                </td>

                                {{-- AKSI --}}
                                <td class="px-4 py-4 text-center">
                                    <div class="flex justify-center items-center space-x-2">

                                        {{-- EDIT --}}
                                        <button @click="openEdit = true"
                                            class="text-amber-500 hover:bg-amber-50 p-1.5 rounded-lg transition"
                                            title="Edit">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>

                                        </button>

                                        {{-- DELETE --}}
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus akun ini?')"
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

                                    {{-- MODAL EDIT --}}
                                    <div x-show="openEdit"
                                        class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                                        x-cloak x-transition>

                                        <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl"
                                            @click.away="openEdit = false">

                                            <div
                                                class="p-5 bg-blue-600 text-white text-sm font-semibold flex justify-between items-center">
                                                Edit Operator
                                                <button @click="openEdit = false">✕</button>
                                            </div>

                                            <form action="{{ route('users.update', $user->id) }}" method="POST"
                                                class="p-6 space-y-4">
                                                @csrf @method('PATCH')

                                                <select name="perangkat_daerah_id"
                                                    class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2">
                                                    @foreach ($all_opd as $opd)
                                                        <option value="{{ $opd->id }}"
                                                            {{ $user->perangkat_daerah_id == $opd->id ? 'selected' : '' }}>
                                                            {{ $opd->nama_opd }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <input type="email" name="email" value="{{ $user->email }}"
                                                    class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2">

                                                <div class="grid grid-cols-2 gap-3">
                                                    <input type="text" name="latitude" value="{{ $user->latitude }}"
                                                        class="bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2">
                                                    <input type="text" name="longitude"
                                                        value="{{ $user->longitude }}"
                                                        class="bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2">
                                                </div>

                                                <input type="password" name="password"
                                                    class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2"
                                                    placeholder="Password baru (opsional)">

                                                <button type="submit"
                                                    class="w-full bg-blue-600 text-white py-2.5 rounded-xl text-xs font-semibold hover:bg-blue-700 transition">
                                                    Simpan
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">
                                    Belum ada operator
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        {{-- MODAL TAMBAH --}}
        <div x-show="openAdd"
            class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak
            x-transition>

            <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl" @click.away="openAdd = false">

                <div class="p-5 bg-blue-600 text-white text-sm font-semibold flex justify-between items-center">
                    Tambah Operator
                    <button @click="openAdd = false">✕</button>
                </div>

                <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <select name="perangkat_daerah_id"
                        class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2">
                        <option value="">-- PILIH OPD --</option>
                        @foreach ($all_opd as $opd)
                            <option value="{{ $opd->id }}">
                                {{ $opd->kode_opd }} - {{ $opd->nama_opd }}
                            </option>
                        @endforeach
                    </select>

                    <input type="email" name="email"
                        class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2"
                        placeholder="operator@email.com">

                    <input type="password" name="password"
                        class="w-full bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2">

                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="latitude"
                            class="bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2"
                            placeholder="Latitude">
                        <input type="text" name="longitude"
                            class="bg-slate-100 border border-gray-200 rounded-lg text-sm px-3 py-2"
                            placeholder="Longitude">
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2.5 rounded-xl text-xs font-semibold hover:bg-blue-700 transition">
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
