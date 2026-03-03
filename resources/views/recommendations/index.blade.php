<x-app-layout>
    <div class="py-6" x-data="{ openAdd: false }">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- HEADER MENU --}}
            <div class="flex justify-between items-center bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div>
                    <h3 class="font-black text-slate-800 uppercase tracking-tight">Manajemen Rekomendasi</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Satu Data SITATIK</p>
                </div>
                @if(auth()->user()->role == 'operator')
                    <button @click="openAdd = true"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase shadow-md transition active:scale-95">
                        Minta Rekomendasi Baru
                    </button>
                @endif
            </div>

            {{-- TABEL DATA --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-gray-100 font-bold uppercase text-[10px] text-gray-400">
                        <tr>
                            <th class="px-6 py-4 w-32">ID Tabel</th>
                            <th class="px-6 py-4">OPD & Nama Tabel</th>
                            @if(auth()->user()->role == 'admin')
                                <th class="px-6 py-4">Kategori Resmi</th>
                            @endif
                            <th class="px-6 py-4">Jadwal Pengisian</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach($recommendations as $rec)
                            <tr x-data="{ openDetail: false, openApprove: false, openCorrect: false, openReject: false, openEdit: false }"
                                class="hover:bg-slate-50/50 transition-colors">

                                {{-- TAMPILAN KODE --}}
                                <td class="px-6 py-4">
                                    @if(!empty($rec->table_code))
                                        <span
                                            class="bg-slate-800 text-white text-[10px] font-black px-2 py-1 rounded shadow-sm">
                                            {{ $rec->table_code }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 italic text-[9px] uppercase tracking-tighter">Pending</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="bg-blue-50 text-blue-600 text-[9px] font-black px-2 py-0.5 rounded uppercase mb-1 inline-block italic">
                                        {{ $rec->user->perangkatDaerah->nama_opd ?? $rec->user->name }}
                                    </span>
                                    <p class="font-bold text-slate-700 leading-tight uppercase">{{ $rec->table_name }}</p>
                                    <button @click="openDetail = true"
                                        class="mt-1 text-blue-600 font-bold text-[10px] uppercase hover:underline">Lihat
                                        Preview Struktur</button>

                                    @if($rec->admin_note && ($rec->status == 'corrected' || $rec->status == 'rejected'))
                                        <div class="mt-3 p-3 bg-amber-50 border-l-4 border-amber-400 rounded-r-xl shadow-sm">
                                            <p class="text-[11px] text-amber-900 leading-relaxed font-medium italic">
                                                "{{ $rec->admin_note }}"</p>
                                        </div>
                                    @endif
                                </td>

                                @if(auth()->user()->role == 'admin')
                                    <td class="px-6 py-4 uppercase font-bold text-slate-500 text-[10px]">
                                        {{ $rec->category ?? 'Belum Diklasifikasi' }}
                                    </td>
                                @endif

                                <td class="px-6 py-4 text-[10px]">
                                    @if($rec->status == 'approved')
                                        <p class="text-slate-500 font-bold uppercase italic">
                                            {{ \Carbon\Carbon::parse($rec->start_date)->format('d/m/Y') }} -
                                            {{ \Carbon\Carbon::parse($rec->end_date)->format('d/m/Y') }}
                                        </p>
                                    @else
                                        <span class="text-gray-400 italic font-medium uppercase text-[9px]">Menunggu
                                            Verifikasi</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase 
                                                {{ $rec->status == 'approved' ? 'bg-emerald-100 text-emerald-700' : ($rec->status == 'corrected' ? 'bg-amber-100 text-amber-700' : ($rec->status == 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-500')) }}">
                                        {{ $rec->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center space-y-2">
                                        @if(auth()->user()->role == 'admin' && ($rec->status == 'pending' || $rec->status == 'corrected'))
                                            <div class="flex space-x-2 pt-1 text-[9px] font-black uppercase">
                                                <button @click="openApprove = true"
                                                    class="text-emerald-600 hover:underline">Setuju</button>
                                                <button @click="openCorrect = true"
                                                    class="text-amber-600 hover:underline">Koreksi</button>
                                                <button @click="openReject = true"
                                                    class="text-rose-600 hover:underline">Tolak</button>
                                            </div>
                                        @elseif(auth()->user()->role == 'operator' && ($rec->status == 'corrected' || $rec->status == 'rejected'))
                                            <button @click="openEdit = true"
                                                class="bg-blue-600 text-white px-3 py-1 rounded-lg text-[8px] font-black uppercase shadow-md active:scale-95 transition">
                                                Perbaiki Data
                                            </button>
                                        @endif
                                        @if($rec->status == 'approved')
                                            <a href="{{ route('recommendations.pdf', $rec->id) }}"
                                                class="text-rose-600 font-black text-[9px] uppercase hover:underline">Unduh
                                                PDF</a>
                                        @endif
                                    </div>

                                    {{-- MODAL PERBAIKI DATA (OPERATOR) --}}
                                    <div x-show="openEdit"
                                        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                                        x-transition x-cloak>
                                        <div
                                            class="bg-white rounded-3xl w-full max-w-md overflow-hidden text-left shadow-2xl">
                                            <div
                                                class="p-6 bg-blue-600 text-white font-black uppercase text-sm flex justify-between items-center">
                                                <span>Perbaiki Pengajuan</span>
                                                <button @click="openEdit = false"><svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                                    </svg></button>
                                            </div>
                                            <form action="{{ route('recommendations.update', $rec->id) }}" method="POST"
                                                class="p-6 space-y-4">
                                                @csrf @method('PATCH')
                                                <div>
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase">Nama
                                                        Tabel</label>
                                                    <input type="text" name="table_name" value="{{ $rec->table_name }}"
                                                        class="w-full rounded-xl border-gray-100 text-sm mt-1" required>
                                                </div>
                                                <div>
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase">Susunan
                                                        Kolom</label>
                                                    <textarea name="table_structure" rows="3"
                                                        class="w-full rounded-xl border-gray-100 text-sm mt-1"
                                                        required>{{ $rec->table_structure }}</textarea>
                                                </div>
                                                <button type="submit"
                                                    class="w-full bg-blue-600 text-white font-black py-4 rounded-xl uppercase text-[10px] shadow-xl">Kirim
                                                    Perbaikan Ulang</button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- MODAL APPROVE (ADMIN) --}}
                                    <div x-show="openApprove"
                                        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                                        x-transition x-cloak>
                                        <div
                                            class="bg-white rounded-3xl w-full max-w-md overflow-hidden text-left shadow-2xl">
                                            <div
                                                class="p-6 bg-emerald-600 text-white font-black uppercase text-sm flex justify-between items-center">
                                                <span>Setujui & Beri Kode</span>
                                                <button @click="openApprove = false"><svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                                    </svg></button>
                                            </div>
                                            <form action="{{ route('recommendations.status', [$rec->id, 'approved']) }}"
                                                method="POST" class="p-6 space-y-4">
                                                @csrf @method('PATCH')
                                                <div>
                                                    <label
                                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kode
                                                        Tabel Resmi</label>
                                                    <input type="text" name="table_code" placeholder="Contoh: 1.01.01"
                                                        class="w-full rounded-xl border-gray-200 text-xs font-bold mt-1 uppercase"
                                                        required>
                                                </div>
                                                <div>
                                                    <label class="text-[10px] font-black text-slate-400 uppercase">Pilih
                                                        Kategori Statistik</label>
                                                    <select name="category"
                                                        class="w-full rounded-xl border-gray-200 text-xs font-bold mt-1"
                                                        required>
                                                        <option value="">-- PILIH KATEGORI --</option>
                                                        @foreach($categories as $cat) <option
                                                            value="{{ $cat->nama_kategori }}">{{ $cat->nama_kategori }}
                                                        </option> @endforeach
                                                    </select>
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Tgl
                                                            Mulai</label><input type="date" name="start_date"
                                                            class="w-full rounded-lg border-gray-200 text-sm mt-1" required>
                                                    </div>
                                                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Tgl
                                                            Selesai</label><input type="date" name="end_date"
                                                            class="w-full rounded-lg border-gray-200 text-sm mt-1" required>
                                                    </div>
                                                </div>
                                                <button type="submit"
                                                    class="w-full bg-emerald-600 text-white font-black py-4 rounded-xl uppercase text-[10px] shadow-lg">Setujui
                                                    Permohonan</button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- MODAL KOREKSI (ADMIN) --}}
                                    <div x-show="openCorrect"
                                        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                                        x-transition x-cloak>
                                        <div
                                            class="bg-white rounded-3xl w-full max-w-md overflow-hidden text-left shadow-2xl border-t-4 border-amber-500">
                                            <div
                                                class="p-6 bg-amber-500 text-white font-black uppercase text-sm flex justify-between items-center">
                                                <span>Catatan Koreksi</span>
                                                <button @click="openCorrect = false"><svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                                    </svg></button>
                                            </div>
                                            <form action="{{ route('recommendations.status', [$rec->id, 'corrected']) }}"
                                                method="POST" class="p-6 space-y-4">
                                                @csrf @method('PATCH')
                                                <div>
                                                    <label class="text-[10px] font-black text-slate-400 uppercase">Pesan
                                                        untuk Operator</label>
                                                    <textarea name="admin_note" rows="4"
                                                        class="w-full rounded-xl border-gray-200 text-sm mt-1"
                                                        placeholder="Tulis alasan koreksi..." required></textarea>
                                                </div>
                                                <button type="submit"
                                                    class="w-full bg-amber-500 text-white font-black py-4 rounded-xl uppercase text-[10px] shadow-lg">Kirim
                                                    Koreksi</button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- MODAL TOLAK (ADMIN) --}}
                                    <div x-show="openReject"
                                        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                                        x-transition x-cloak>
                                        <div
                                            class="bg-white rounded-3xl w-full max-w-md overflow-hidden text-left shadow-2xl border-t-4 border-rose-600">
                                            <div
                                                class="p-6 bg-rose-600 text-white font-black uppercase text-sm flex justify-between items-center">
                                                <span>Tolak Pengajuan</span>
                                                <button @click="openReject = false"><svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                                    </svg></button>
                                            </div>
                                            <form action="{{ route('recommendations.status', [$rec->id, 'rejected']) }}"
                                                method="POST" class="p-6 space-y-4">
                                                @csrf @method('PATCH')
                                                <p class="text-xs text-slate-500 italic">Mohon berikan alasan penolakan
                                                    permanen.</p>
                                                <textarea name="admin_note" rows="3"
                                                    class="w-full rounded-xl border-gray-200 text-sm mt-1"
                                                    placeholder="Alasan penolakan..." required></textarea>
                                                <button type="submit"
                                                    class="w-full bg-rose-600 text-white font-black py-4 rounded-xl uppercase text-[10px] shadow-lg">Ya,
                                                    Tolak Permanen</button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- MODAL PREVIEW STRUKTUR --}}
                                    <div x-show="openDetail"
                                        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                                        x-transition x-cloak>
                                        <div class="bg-white rounded-2xl w-full max-w-4xl overflow-hidden shadow-2xl text-left"
                                            @click.away="openDetail = false">
                                            <div
                                                class="p-4 bg-slate-800 text-white font-black uppercase text-xs flex justify-between items-center">
                                                <span>Preview Struktur Tabel</span>
                                                <button @click="openDetail = false">✕</button>
                                            </div>
                                            <div class="p-6 overflow-x-auto">
                                                <table class="w-full border-collapse border border-slate-200">
                                                    <thead class="bg-slate-100 text-[10px] font-black uppercase">
                                                        <tr>
                                                            @foreach(explode(',', $rec->table_structure) as $kolom)
                                                                <th class="px-4 py-3 border border-slate-200">{{ trim($kolom) }}
                                                                </th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>@foreach(explode(',', $rec->table_structure) as $k) <td
                                                            class="px-4 py-8 border border-slate-100 shadow-inner"></td>
                                                        @endforeach</tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODAL TAMBAH (OPERATOR) --}}
        <div x-show="openAdd"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
            x-transition x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl" @click.away="openAdd = false">
                <div class="p-6 bg-blue-600 text-white font-black uppercase text-sm flex justify-between items-center">
                    <span>Ajukan Struktur Baru</span>
                    <button @click="openAdd = false" class="hover:text-blue-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('recommendations.store') }}" method="POST" class="p-6 space-y-4 text-left">
                    @csrf
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nama
                            Tabel</label><input type="text" name="table_name" placeholder="Contoh: Produksi Padi 2024"
                            class="w-full rounded-xl border-gray-100 text-sm mt-1 focus:ring-blue-500" required></div>
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Susunan Kolom
                            (Pemisah Koma)</label><textarea name="table_structure" rows="3"
                            placeholder="No, Komoditas, Satuan, Nilai"
                            class="w-full rounded-xl border-gray-100 text-sm mt-1 focus:ring-blue-500"
                            required></textarea></div>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white font-black py-4 rounded-xl uppercase text-[10px] shadow-xl hover:bg-blue-700 transition active:scale-95">Kirim
                        Pengajuan</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>