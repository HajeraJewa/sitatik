<x-app-layout>
    <x-slot name="header"> Dashboard Monitoring </x-slot>

    {{-- Container Utama: Mengunci tinggi layar agar tidak scroll ke bawah pada body utama --}}
    <div class="h-[calc(100vh-64px)] bg-[#f8fafc] flex flex-col p-3 overflow-hidden">
        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-3 min-h-0">

            {{-- HEADER SECTION --}}
            <div class="bg-white px-5 py-3 rounded-2xl border border-slate-200 shadow-sm flex-shrink-0">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-lg font-extrabold text-slate-800 tracking-tight leading-tight uppercase">
                            Dashboard Monitoring Data Statistik
                        </h1>
                        <p class="text-[10px] text-slate-500 font-medium">
                            Provinsi Sulawesi Tengah — SITATIK
                        </p>
                    </div>

                    {{-- FILTER PERIODE --}}
                    <div class="relative" x-data="{ open: false, selected: '{{ request('tahun', date('Y')) }}' }">
                        <!-- Tombol Pemicu Dropdown -->
                        <button @click="open = !open" @click.away="open = false"
                            class="flex items-center gap-2.5 bg-slate-50 hover:bg-white px-3 py-1.5 rounded-xl border border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Periode</span>
                            
                            <span class="text-xs font-black text-blue-600 leading-none" x-text="selected"></span>
                            
                            <svg class="w-3 h-3 text-blue-600 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Daftar Pilihan Tahun (Dropdown) -->
                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="absolute top-full right-0 mt-2 w-24 bg-white border border-slate-100 rounded-xl shadow-xl z-[100] overflow-hidden"
                            style="display: none;">
                            
                            <div class="flex flex-col py-1">
                                @foreach (range(date('Y'), 2025) as $year)
                                <a href="{{ request()->fullUrlWithQuery(['tahun' => $year]) }}" 
                                class="px-3 py-2 text-xs font-bold block transition-all flex justify-between items-center"
                                :class="selected == '{{ $year }}' ? 'bg-blue-600 text-white' : 'text-blue-600 hover:bg-blue-50'">
                                    {{ $year }}
                                    <template x-if="selected == '{{ $year }}'">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                        </svg>
                                    </template>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STATISTIK RINGKASAN --}}
                <div class="grid grid-cols-4 gap-3 mt-3">
                    <div class="p-3 bg-white border border-slate-100 rounded-xl shadow-sm">
                        <p class="text-[9px] font-bold text-blue-500 uppercase tracking-wider mb-1">Rata-rata Progres</p>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xl font-black text-slate-800">{{ $rata_progres }}%</span>
                            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.4)]" style="width: {{ $rata_progres }}%"></div>
                            </div>
                        </div>
                    </div>

                    @foreach([
                        ['Selesai', $opd_selesai, 'emerald'],
                        ['Proses', $opd_proses, 'amber'],
                        ['Belum', $opd_belum, 'rose']
                    ] as $stat)
                    <div class="p-3 bg-white border border-slate-100 rounded-xl shadow-sm">
                        <p class="text-[9px] font-bold text-{{ $stat[2] }}-500 uppercase tracking-wider mb-1">{{ $stat[0] }}</p>
                        <p class="text-xl font-black text-slate-800">
                            {{ $stat[1] }} <span class="text-[10px] font-bold text-slate-400">OPD</span>
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- MAIN CONTENT: MAP & SIDEBAR --}}
            <div class="grid grid-cols-12 gap-3 flex-1 min-h-0 overflow-hidden">
                {{-- AREA PETA --}}
                <div class="col-span-12 lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col overflow-hidden relative">
                    <div id="map" class="absolute inset-0 w-full h-full"></div>
                </div>

                {{-- SIDEBAR AREA --}}
                <div class="col-span-12 lg:col-span-4 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col min-h-0 overflow-hidden">
                    {{-- Navigasi Sidebar --}}
                    <div class="px-4 py-3 border-b border-slate-50 flex-shrink-0 flex items-center justify-between bg-white z-10">
                        <h3 class="font-bold text-slate-800 text-[10px] uppercase tracking-tighter">Progress Perangkat Daerah</h3>
                        <select id="select-opd" class="w-32 bg-slate-50 border border-slate-200 text-[9px] font-medium px-2 py-1 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="">Cari OPD...</option>
                            @foreach ($opds as $opd)
                                <option value="{{ $opd->id }}">
                                    {{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative flex-1 min-h-0 overflow-hidden">
                        {{-- DAFTAR OPD UTAMA --}}
                        <div id="opd-list-container" class="absolute inset-0 overflow-y-auto p-4 space-y-3 custom-scrollbar transition-all duration-300">
                            @foreach ($opds as $opd)
                            @php
                                $color = $opd->persentase >= 100 ? 'bg-emerald-500' : ($opd->persentase > 0 ? 'bg-amber-400' : 'bg-rose-500');
                                $barBg = $opd->persentase >= 100 ? 'bg-emerald-50' : ($opd->persentase > 0 ? 'bg-amber-50' : 'bg-rose-50');
                            @endphp
                            <div class="group cursor-pointer p-4 rounded-xl bg-white border border-slate-100 hover:border-blue-200 hover:shadow-md transition-all duration-300"
                                 onclick="showOPDDetail({{ $opd->id }}); focusOPD({{ $opd->latitude ?? 0 }}, {{ $opd->longitude ?? 0 }}, '{{ $opd->id }}')">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[10px] font-bold text-slate-700 group-hover:text-blue-600 transition-colors truncate pr-2 uppercase">
                                        {{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}
                                    </span>
                                    <span class="text-[10px] font-black text-slate-800 tabular-nums">{{ $opd->persentase }}%</span>
                                </div>
                                <div class="w-full {{ $barBg }} h-1.5 rounded-full overflow-hidden">
                                    <div class="{{ $color }} h-full transition-all duration-700" style="width: {{ $opd->persentase }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- PANEL DETAIL: Fokus pada Area Scroll Rincian Tabel --}}
                        <div id="opd-detail" class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-all duration-300 z-30 flex flex-col translate-x-4">
                            <div id="detail-content" class="h-full flex flex-col min-h-0 overflow-hidden px-4">
                                {{-- Diisi otomatis via JavaScript --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER GLOBAL DATA --}}
            <div class="grid grid-cols-4 gap-3 flex-shrink-0">
                @foreach([
                    ['Total Data', number_format($total_rekomendasi), 'Tabel', 'blue'],
                    ['Data Terisi', number_format($tabel_aktif), 'Tabel', 'emerald'],
                    ['Sektor Data', $total_kategori, 'Bidang', 'purple'],
                    ['Total Akun', $opds->count(), 'Akun', 'orange']
                ] as $item)
                <div class="bg-white px-4 py-3 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between group hover:border-{{ $item[3] }}-200 transition-all">
                    <div class="flex items-center gap-3">
                        {{-- Ikon Bulat Dinamis --}}
                        <div class="w-9 h-9 rounded-xl bg-{{ $item[3] }}-50 flex items-center justify-center transition-colors">
                            {{-- Bulat Inti --}}
                            <div class="w-4 h-4 bg-{{ $item[3] }}-400 rounded-lg opacity-40 group-hover:scale-110 transition-transform shadow-sm"></div>
                        </div>
                        
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1.5">{{ $item[0] }}</p>
                            <p class="text-sm font-black text-slate-800 leading-none uppercase tracking-tighter">
                                {{ $item[1] }} <span class="text-[10px] font-bold text-slate-400 ml-0.5">{{ $item[2] }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

<style>
    /* Desain Scrollbar Tebal agar mudah diakses */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 1px solid #f1f5f9; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    .leaflet-container { font-family: inherit; background: #f1f5f9 !important; border-radius: 16px; }
    
    #opd-detail { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }

    /* Safelist Warna Footer (Menampilkan warna yang dipanggil secara dinamis) */
    .bg-blue-50 { background-color: #eff6ff; } 
    .bg-blue-400 { background-color: #60a5fa; }
    .hover\:border-blue-200:hover { border-color: #bfdbfe; }

    .bg-emerald-50 { background-color: #ecfdf5; }
    .bg-emerald-400 { background-color: #34d399; }
    .hover\:border-emerald-200:hover { border-color: #a7f3d0; }

    .bg-purple-50 { background-color: #f5f3ff; }
    .bg-purple-400 { background-color: #a78bfa; }
    .hover\:border-purple-200:hover { border-color: #ddd6fe; }

    .bg-orange-50 { background-color: #fff7ed; }
    .bg-orange-400 { background-color: #fb923c; }
    .hover\:border-orange-200:hover { border-color: #fed7aa; }

    @media (max-width: 1024px) {
        .h-\[calc\(100vh-64px\)\] { height: auto; overflow-y: auto; }
        .min-h-0 { min-height: 500px; }
    }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let opdData = @json($opds);

document.addEventListener('DOMContentLoaded', function () {
    window.map = L.map('map', { zoomControl:false, attributionControl:false }).setView([-0.8917, 119.8707], 10);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(map);
    
    window.markers = {};
    opdData.forEach(opd => {
        if (opd.latitude && opd.longitude) {
            const marker = L.marker([opd.latitude, opd.longitude]).addTo(map);
            
            // Nama OPD diambil dari relasi agar tidak tampil "OPERATOR OPD"
            const namaDinas = opd.perangkat_daerah?.nama_opd || opd.perangkat_daerah?.nama || opd.nama || opd.name;
            
            marker.bindPopup(`
                <div class="text-center font-bold text-[10px] uppercase">
                    ${namaDinas}<br>
                    <span class="text-blue-600">${opd.persentase}%</span>
                </div>
            `);
            window.markers[opd.id] = marker;
        }
    });
});

function showOPDDetail(id) {
    const opd = opdData.find(o => o.id == id);
    if (!opd) return;

    // Nama OPD diambil dari relasi agar tidak tampil "OPERATOR OPD"
    const namaDinasDetail = opd.perangkat_daerah?.nama_opd || opd.perangkat_daerah?.nama || opd.nama || opd.name;

    const detailPanel = document.getElementById('opd-detail');
    detailPanel.classList.remove('opacity-0', 'pointer-events-none', 'translate-x-4');
    document.getElementById('opd-list-container').classList.add('opacity-0', '-translate-x-4');

    let tabelHtml = '';
    if (opd.tabel_details && opd.tabel_details.length > 0) {
        opd.tabel_details.forEach(tabel => {
            let badge = tabel.status === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : (tabel.status === 'Proses' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500');
            tabelHtml += `
                <div class="p-3 mb-2 bg-white border border-slate-100 rounded-xl shadow-sm flex justify-between items-center transition-all hover:border-blue-200">
                    <p class="text-[10px] font-medium text-slate-700 line-clamp-2 pr-3 leading-snug uppercase tracking-tight">${tabel.table_name || 'Tanpa Nama'}</p>
                    <span class="text-[8px] px-2 py-0.5 rounded font-black uppercase tracking-wider ${badge} shadow-sm">${tabel.status}</span>
                </div>`;
        });
    } else {
        tabelHtml = `<div class="py-16 text-center text-[10px] text-slate-400 italic">Belum ada rincian tabel tersedia</div>`;
    }

    document.getElementById('detail-content').innerHTML = `
        <div class="flex flex-col h-full overflow-hidden">
            {{-- Header Detail (Sticky) --}}
            <div class="flex-shrink-0 pt-4 pb-3 border-b border-slate-50 mb-3">
                <button onclick="closeDetail()" class="text-[9px] text-blue-600 font-black flex items-center gap-1.5 hover:underline mb-2 uppercase tracking-widest group">
                    <svg class="w-3 h-3 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                    KEMBALI
                </button>
                <h3 class="font-black text-slate-800 text-[11px] leading-tight mb-2 uppercase tracking-tighter">
                    ${namaDinasDetail}
                </h3>
                
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-2.5 rounded-xl border border-blue-100 flex justify-between items-center shadow-sm">
                    <span class="text-[9px] font-bold text-blue-700 uppercase tracking-widest">Progress</span>
                    <span class="text-sm font-black text-blue-700 tabular-nums">${opd.persentase || 0}%</span>
                </div>
            </div>

            {{-- AREA SCROLL DETAIL --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar pr-1 pb-10">
                <p class="text-[8px] font-bold text-slate-400 mb-2 uppercase tracking-widest px-1">Daftar Rincian Tabel Statistik</p>
                ${tabelHtml}
            </div>
        </div>`;
}

function closeDetail() {
    document.getElementById('opd-detail').classList.add('opacity-0', 'pointer-events-none', 'translate-x-4');
    document.getElementById('opd-list-container').classList.remove('opacity-0', '-translate-x-4');
    window.map.flyTo([-0.8917, 119.8707], 13, { duration: 1.5 });
}

function focusOPD(lat, lng, id) {
    if (lat != 0) {
        window.map.flyTo([lat, lng], 15, { duration: 1.5 });
        if (window.markers[id]) window.markers[id].openPopup();
    }
}

document.getElementById('select-opd').addEventListener('change', function() {
    const id = this.value;
    if (!id) { closeDetail(); return; }
    fetch(`/dashboard/opd?opd_id=${id}&tahun={{ request('tahun', date('Y')) }}`)
        .then(res => res.json())
        .then(data => {
            if (data.length > 0) {
                const opd = data[0];
                const idx = opdData.findIndex(o => o.id == id);
                if (idx !== -1) opdData[idx] = opd;
                showOPDDetail(id);
                if (opd.latitude) focusOPD(opd.latitude, opd.longitude, id);
            }
        });
});
</script>
</x-app-layout>