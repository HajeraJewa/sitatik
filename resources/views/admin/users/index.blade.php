<x-app-layout>
  <x-slot name="header">
    <div class="flex justify-between items-center">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Manajemen Pengguna OPD') }}
      </h2>
    </div>
  </x-slot>

  <div class="py-6" x-data="{ openAdd: false }">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-4 text-sm">

      @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
          class="flex items-center p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm mb-4">
          <p class="text-xs font-black text-emerald-800 uppercase tracking-wide">{{ session('success') }}</p>
        </div>
      @endif

      <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <div>
          <h3 class="font-black text-slate-800 uppercase tracking-tight">Daftar Akun Operator</h3>
          <p class="text-[10px] text-slate-400 uppercase font-bold">Total: {{ count($users) }} Akun Terdaftar</p>
        </div>
        <button @click="openAdd = true"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-black uppercase shadow-md transition transform active:scale-95 flex items-center">
          + Tambah Operator
        </button>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left">
          <thead class="bg-slate-50 border-b border-gray-100">
            <tr class="text-[10px] text-gray-400 uppercase font-bold">
              <th class="px-6 py-4">Perangkat Daerah / Email</th>
              <th class="px-6 py-4">Koordinat Digital Twin</th>
              <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            @forelse($users as $user)
              <tr x-data="{ openEdit: false }" class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                  {{-- Menampilkan Nama Instansi dari Relasi Perangkat Daerah --}}
                  <p class="font-black text-slate-700 leading-tight text-base uppercase">
                    {{ $user->perangkatDaerah->nama_opd ?? $user->name }}
                  </p>
                  <p class="text-xs text-blue-500 italic font-medium lowercase">{{ $user->email }}</p>
                </td>
                <td class="px-6 py-4">
                  <span class="font-mono text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full border border-blue-100">
                    {{ $user->latitude ?? '0' }}, {{ $user->longitude ?? '0' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-center space-x-3">
                  <button @click="openEdit = true"
                    class="text-amber-500 font-black text-[10px] uppercase hover:underline">Edit</button>
                  <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-rose-500 font-black text-[10px] uppercase hover:underline"
                      onclick="return confirm('Hapus akun ini?')">Hapus</button>
                  </form>

                  <div x-show="openEdit"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                    x-cloak x-transition>
                    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative"
                      @click.away="openEdit = false">
                      <div class="p-4 bg-slate-800 text-white font-black uppercase text-xs text-center">Edit Akun Operator
                      </div>
                      <form action="{{ route('users.update', $user->id) }}" method="POST" class="p-6 space-y-4 text-left">
                        @csrf @method('PATCH')

                        <div>
                          <label class="text-[10px] font-bold text-slate-400 uppercase">Pilih Perangkat Daerah</label>
                          <select name="perangkat_daerah_id"
                            class="w-full rounded-lg border-slate-200 text-sm focus:ring-blue-500 font-bold uppercase"
                            required>
                            @foreach($all_opd as $opd)
                              <option value="{{ $opd->id }}" {{ $user->perangkat_daerah_id == $opd->id ? 'selected' : '' }}>
                                {{ $opd->nama_opd }}
                              </option>
                            @endforeach
                          </select>
                        </div>

                        <div>
                          <label class="text-[10px] font-bold text-slate-400 uppercase">Email Login</label>
                          <input type="email" name="email" value="{{ $user->email }}"
                            class="w-full rounded-lg border-slate-200 text-sm" required>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                          <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Latitude</label>
                            <input type="text" name="latitude" value="{{ $user->latitude }}"
                              class="w-full rounded-lg border-slate-200 text-sm">
                          </div>
                          <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Longitude</label>
                            <input type="text" name="longitude" value="{{ $user->longitude }}"
                              class="w-full rounded-lg border-slate-200 text-sm">
                          </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 mt-4">
                          <label class="text-[10px] font-black text-blue-600 uppercase tracking-widest leading-none">
                            Ganti Password (Opsional)
                          </label>
                          <input type="password" name="password"
                            class="w-full rounded-xl border-slate-200 text-xs font-bold focus:ring-blue-500 mt-2"
                            placeholder="Masukkan password baru">
                          <p class="text-[9px] text-slate-400 mt-1 italic">
                            * Kosongkan jika tidak ingin mengubah password operator.
                          </p>
                        </div>

                        <button type="submit"
                          class="w-full bg-blue-600 text-white font-black py-3 rounded-xl uppercase text-[10px] shadow-lg">Simpan
                          Perubahan</button>
                      </form>
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-6 py-20 text-center text-slate-400 italic">Belum ada operator terdaftar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div x-show="openAdd"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak
        x-transition>
        <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative"
          @click.away="openAdd = false">
          <div class="p-4 bg-blue-600 text-white font-black uppercase text-xs text-center tracking-widest">Tambah
            Operator Baru</div>
          <form action="{{ route('users.store') }}" method="POST" class="p-8 space-y-4 text-left">
            @csrf

            <div>
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Instansi (Perangkat
                Daerah)</label>
              {{-- Dropdown mengambil data dari Master Perangkat Daerah --}}
              <select name="perangkat_daerah_id"
                class="w-full rounded-xl border-slate-200 text-xs font-black uppercase focus:ring-blue-500 mt-1"
                required>
                <option value="">-- PILIH OPD --</option>
                @foreach($all_opd as $opd)
                  <option value="{{ $opd->id }}">{{ $opd->kode_opd }} - {{ $opd->nama_opd }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Email Login</label>
              <input type="email" name="email" class="w-full rounded-xl border-slate-200 text-xs font-bold"
                placeholder="operator@sulteng.go.id" required>
            </div>

            <div>
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Password Default</label>
              <input type="password" name="password" class="w-full rounded-xl border-slate-200 text-xs font-bold"
                required>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
              <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Latitude</label>
                <input type="text" name="latitude" class="w-full rounded-xl border-slate-200 text-xs font-bold"
                  placeholder="-0.8938">
              </div>
              <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Longitude</label>
                <input type="text" name="longitude" class="w-full rounded-xl border-slate-200 text-xs font-bold"
                  placeholder="119.8781">
              </div>
            </div>

            <button type="submit"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl uppercase text-[10px] shadow-xl transition-all tracking-widest">
              Simpan & Hubungkan Akun
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</x-app-layout>