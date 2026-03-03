<x-app-layout>
  <div class="py-6 px-4" x-data="{ openModal: false, id: '', kode: '', nama: '', alias: '' }">

    <div class="bg-white p-6 rounded-t-3xl border-b border-gray-100 flex justify-between items-center shadow-sm">
      <div>
        <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Master Perangkat Daerah</h2>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Satu Data SITATIK</p>
      </div>
      <button @click="openModal = true; id=''; kode=''; nama=''; alias=''"
        class="bg-emerald-600 text-white px-6 py-2.5 rounded-2xl text-[10px] font-black uppercase shadow-lg hover:bg-emerald-700 transition-all active:scale-95">
        + Tambah Instansi
      </button>
    </div>

    <div class="bg-white border border-gray-100 rounded-b-3xl overflow-hidden shadow-sm">
      <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 text-[10px] font-black uppercase text-slate-400">
          <tr>
            <th class="px-6 py-5 border-b text-center" width="150">KODE</th>
            <th class="px-6 py-5 border-b">NAMA PERANGKAT DAERAH</th>
            <th class="px-6 py-5 border-b text-center" width="150">AKSI KONTROL</th>
          </tr>
        </thead>
        <tbody class="text-xs font-bold text-slate-600 divide-y divide-gray-50">
          @foreach($opds as $opd)
            <tr class="hover:bg-blue-50/30 transition-colors">
              <td class="px-6 py-5 text-center font-black text-slate-800 border-r border-gray-50">
                {{ $opd->kode_opd }}
              </td>
              <td class="px-6 py-5 border-r border-gray-50">
                <p class="uppercase font-black text-slate-700 leading-tight">{{ $opd->nama_opd }}</p>
                {{-- Alias diletakkan di bawah nama dengan format italic biru --}}
                @if($opd->alias_opd)
                  <p class="text-[10px] text-blue-600 font-black uppercase italic mt-1">
                    {{ $opd->alias_opd }}
                  </p>
                @else
                  <p class="text-[9px] text-slate-300 italic mt-1 font-medium italic">Tanpa Alias</p>
                @endif
              </td>
              <td class="px-6 py-5">
                <div class="flex justify-center items-center space-x-4">
                  <button
                    @click="openModal = true; id='{{ $opd->id }}'; kode='{{ $opd->kode_opd }}'; nama='{{ $opd->nama_opd }}'; alias='{{ $opd->alias_opd }}'"
                    class="text-amber-400 hover:scale-125 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path
                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                  </button>

                  <form action="{{ route('perangkat-daerah.destroy', $opd->id) }}" method="POST"
                    onsubmit="return confirm('Hapus data instansi ini? Semua akun operator terkait akan ikut terhapus.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-rose-500 hover:scale-125 transition-transform">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                          d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                          clip-rule="evenodd" />
                      </svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div x-show="openModal"
      class="fixed inset-0 z-[150] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak
      x-transition>
      <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl" @click.away="openModal = false">
        <div
          class="p-6 bg-emerald-600 text-white font-black uppercase text-xs flex justify-between items-center shadow-lg">
          <span x-text="id ? 'Edit Instansi' : 'Tambah Instansi Baru'"></span>
          <button @click="openModal = false" class="text-xl">✕</button>
        </div>
        <form action="{{ route('perangkat-daerah.store') }}" method="POST" class="p-8 space-y-5">
          @csrf
          <input type="hidden" name="id" x-model="id">
          <div>
            <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">Kode OPD</label>
            <input type="text" name="kode_opd" x-model="kode"
              class="w-full rounded-2xl border-gray-100 text-xs font-bold" required>
          </div>
          <div>
            <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">Nama OPD</label>
            <input type="text" name="nama_opd" x-model="nama"
              class="w-full rounded-2xl border-gray-100 text-xs font-bold uppercase" required>
          </div>
          <div>
            <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">Alias (Singkatan)</label>
            <input type="text" name="alias_opd" x-model="alias"
              class="w-full rounded-2xl border-gray-100 text-xs font-bold uppercase">
          </div>
          <button type="submit"
            class="w-full bg-emerald-600 text-white font-black py-4 rounded-2xl uppercase text-[10px] shadow-xl hover:bg-emerald-700 transition-all tracking-widest">
            Simpan Master Data
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