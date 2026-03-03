<x-app-layout>
  <div class="py-6 px-4 sm:px-6 lg:px-8" x-data="{ openModal: false, catId: '', kode: '', nama: '' }">

    <div class="bg-white p-6 rounded-t-3xl border-b border-gray-100 flex justify-between items-center shadow-sm">
      <div>
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Kategori</h2>
        <p class="text-[10px] font-black text-slate-400 mt-1 uppercase tracking-widest">Total: <span
            class="text-emerald-600">{{ $totalKategori }}</span></p>
      </div>

      <div class="flex items-center space-x-3">
        <form action="{{ route('categories.index') }}" method="GET" class="relative">
          <input type="text" name="search" placeholder="CARI KATEGORI..." value="{{ request('search') }}"
            class="rounded-xl border-gray-100 text-[10px] font-black w-64 focus:ring-emerald-500 pr-10">
        </form>
        <button @click="openModal = true; catId = ''; kode = ''; nama = ''"
          class="bg-emerald-600 text-white px-6 py-2.5 rounded-2xl text-[10px] font-black uppercase shadow-lg hover:bg-emerald-700 transition-all">
          + Tambah Kategori
        </button>
      </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-b-3xl">
      <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 text-[10px] font-black uppercase text-slate-400">
          <tr>
            <th class="px-6 py-5 border-b text-center" width="150">KODE</th>
            <th class="px-6 py-5 border-b">NAMA KATEGORI</th>
            <th class="px-6 py-5 border-b text-center" width="150">AKSI</th>
          </tr>
        </thead>
        <tbody class="text-xs font-bold text-slate-600 divide-y divide-gray-50">
          @forelse($categories as $category)
            <tr class="hover:bg-blue-50/30 transition-colors">
              <td class="px-6 py-5 text-center border-r border-gray-50">{{ $category->kode_kategori }}</td>
              <td class="px-6 py-5 border-r border-gray-50 uppercase tracking-tight">{{ $category->nama_kategori }}</td>
              <td class="px-6 py-5">
                <div class="flex justify-center space-x-4">
                  <button
                    @click="openModal = true; catId = '{{ $category->id }}'; kode = '{{ $category->kode_kategori }}'; nama = '{{ $category->nama_kategori }}'"
                    class="text-amber-400 hover:scale-125 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path
                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                  </button>
                  <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                    onsubmit="return confirm('Hapus kategori?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-rose-400 hover:scale-125 transition-transform">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
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
              <td colspan="3" class="px-6 py-12 text-center text-slate-300 italic font-black uppercase">Data Kosong</td>
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
          <span x-text="catId ? 'Edit Kategori' : 'Tambah Kategori Baru'"></span>
          <button @click="openModal = false" class="text-xl">✕</button>
        </div>
        <form action="{{ route('categories.store') }}" method="POST" class="p-8 space-y-6">
          @csrf
          <input type="hidden" name="category_id" x-model="catId">
          <div>
            <label class="text-[10px] font-black text-slate-400 uppercase block mb-2 tracking-widest">Kode
              Kategori</label>
            <input type="text" name="kode_kategori" x-model="kode"
              class="w-full rounded-2xl border-gray-100 text-xs font-bold" placeholder="Misal: 1.1" required>
          </div>
          <div>
            <label class="text-[10px] font-black text-slate-400 uppercase block mb-2 tracking-widest">Nama
              Kategori</label>
            <input type="text" name="nama_kategori" x-model="nama"
              class="w-full rounded-2xl border-gray-100 text-xs font-bold" placeholder="Misal: Perkebunan" required>
          </div>
          <button type="submit"
            class="w-full bg-emerald-600 text-white font-black py-4 rounded-2xl uppercase text-[10px] shadow-xl hover:bg-emerald-700 transition-all tracking-widest">Simpan</button>
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