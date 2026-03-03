<x-app-layout>
  <div class="py-6 px-4 sm:px-6 lg:px-8"
    x-data="{ openModal: false, openDelete: false, deleteUrl: '', selectedRec: '', sourceName: '' }">

    @if (session('success'))
      <div
        class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-xl text-xs font-bold uppercase shadow-sm">
        {{ session('success') }}
      </div>
    @endif

    <div class="bg-white p-6 rounded-t-3xl border-b border-gray-100 flex justify-between items-center shadow-sm">
      <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Sumber Data</h2>

      <div class="flex items-center space-x-3">
        <form action="{{ route('sources.index') }}" method="GET" class="flex space-x-2">
          <select name="opd_id" class="rounded-xl border-gray-200 text-[10px] font-black uppercase focus:ring-blue-500">
            <option value="">Pilih OPD</option>
            @foreach($allOpd as $opd)
              <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>{{ $opd->name }}</option>
            @endforeach
          </select>
          <button type="submit"
            class="bg-slate-800 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-black transition">Filter</button>
        </form>

        @if(auth()->user()->role == 'admin')
          <button @click="openModal = true; selectedRec = ''; sourceName = ''"
            class="bg-emerald-600 text-white px-6 py-2.5 rounded-2xl text-[10px] font-black uppercase shadow-lg hover:bg-emerald-700 transition-all">
            + Tambah Data
          </button>
        @endif
      </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-b-3xl">
      <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 text-[10px] font-black uppercase text-slate-400">
          <tr>
            <th class="px-6 py-5 border-b text-center" width="80">NO</th>
            <th class="px-6 py-5 border-b">TABEL</th>
            <th class="px-6 py-5 border-b text-center">OPD / SUMBER DATA</th>
            <th class="px-6 py-5 border-b text-center">TANGGAL DIATUR</th>
            <th class="px-6 py-5 border-b text-center">AKSI</th>
          </tr>
        </thead>
        <tbody class="text-xs font-bold text-slate-600 divide-y divide-gray-50">
          @forelse($sources as $index => $source)
            <tr class="hover:bg-blue-50/30 transition-colors">
              <td class="px-6 py-5 text-center border-r border-gray-50 text-slate-400">{{ $index + 1 }}</td>
              <td class="px-6 py-5 border-r border-gray-50 uppercase">
                <p class="font-black text-slate-800 leading-tight">{{ $source->table_name }}</p>
              </td>
              <td class="px-6 py-5 border-r border-gray-50 text-center">
                {{-- Menampilkan Nama Sumber Baru jika ada, jika tidak tampilkan nama OPD Asli --}}
                <span class="text-blue-600 font-black uppercase italic">
                  {{ $source->data_source_name ?? $source->user->name }}
                </span>
                @if($source->data_source_name)
                  <p class="text-[8px] text-slate-400 font-medium uppercase mt-1 tracking-tighter italic">Ref:
                    {{ $source->user->name }}
                  </p>
                @endif
              </td>
              <td class="px-6 py-5 border-r border-gray-50 text-center">
                <div class="flex flex-col">
                  <span
                    class="uppercase font-black text-slate-700">{{ $source->updated_at->translatedFormat('d F Y') }}</span>
                  <span class="text-[9px] text-slate-400 font-medium uppercase tracking-widest">Pukul
                    {{ $source->updated_at->format('H:i') }} WITA</span>
                </div>
              </td>
              <td class="px-6 py-5">
                <div class="flex justify-center space-x-4">
                  <button
                    @click="openModal = true; selectedRec = '{{ $source->id }}'; sourceName = '{{ $source->data_source_name }}'"
                    class="text-amber-400 hover:scale-125 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path
                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                  </button>

                  @if(auth()->user()->role == 'admin')
                    <button @click="openDelete = true; deleteUrl = '{{ route('sources.destroy', $source->id) }}'"
                      class="text-rose-400 hover:scale-125 transition-transform">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                          d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                          clip-rule="evenodd" />
                      </svg>
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-6 py-12 text-center text-slate-300 italic font-black uppercase tracking-widest">
                Belum ada data yang diatur</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div x-show="openModal"
      class="fixed inset-0 z-[150] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak
      x-transition>
      <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl" @click.away="openModal = false">
        <div
          class="p-6 bg-emerald-600 text-white font-black uppercase text-xs flex justify-between items-center shadow-lg">
          <span x-text="selectedRec ? 'Edit Sumber Data' : 'Tambah Sumber Data Baru'"></span>
          <button @click="openModal = false" class="text-xl">✕</button>
        </div>
        <form action="{{ route('sources.store') }}" method="POST" class="p-8 space-y-6">
          @csrf
          <div>
            <label class="text-[10px] font-black text-slate-400 uppercase block mb-2 tracking-widest">Pilih Tabel
              Referensi</label>
            <select name="recommendation_id" x-model="selectedRec"
              class="w-full rounded-2xl border-gray-100 text-xs font-bold uppercase focus:ring-emerald-500" required>
              <option value="">-- PILIH TABEL --</option>
              @foreach($sources as $source)
                <option value="{{ $source->id }}">{{ $source->table_name }} ({{ $source->user->name }})</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="text-[10px] font-black text-slate-400 uppercase block mb-2 tracking-widest">Nama Sumber Data
              (OPD)</label>
            <input type="text" name="nama_sumber" x-model="sourceName"
              class="w-full rounded-2xl border-gray-100 text-xs font-bold" placeholder="Ketik nama OPD/Bidang..."
              required>
          </div>
          <button type="submit"
            class="w-full bg-emerald-600 text-white font-black py-4 rounded-2xl uppercase text-[10px] shadow-xl hover:bg-emerald-700 transition-all tracking-widest">
            Simpan Perubahan
          </button>
        </form>
      </div>
    </div>

    <div x-show="openDelete"
      class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak
      x-transition>
      <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl p-8 text-center"
        @click.away="openDelete = false">
        <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </div>
        <h3 class="text-lg font-black text-slate-800 uppercase mb-2">Hapus Data?</h3>
        <p class="text-[10px] text-slate-400 font-bold uppercase leading-relaxed mb-6">Data yang dihapus tidak dapat
          dikembalikan lagi.</p>
        <div class="flex space-x-3">
          <button @click="openDelete = false"
            class="flex-1 py-3 border border-slate-100 rounded-xl text-[10px] font-black uppercase text-slate-400 hover:bg-slate-50 transition">Batal</button>
          <form :action="deleteUrl" method="POST" class="flex-1">
            @csrf
            @method('DELETE')
            <button type="submit"
              class="w-full py-3 bg-rose-600 text-white rounded-xl text-[10px] font-black uppercase shadow-lg shadow-rose-200 hover:bg-rose-700 transition">Hapus</button>
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