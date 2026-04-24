<x-app-layout>
    <x-slot name="header"> Dashboard Monitoring </x-slot>

    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-2 md:px-4 pb-2 overflow-hidden">
        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-3 py-2 min-h-0">

            {{-- 1. HEADER & RINGKASAN (Dibuat lebih padat) --}}
            <div class="bg-white px-5 py-3 rounded-2xl border shadow-sm flex-shrink-0">
                <div class="flex justify-between items-center gap-4">
                    <div>
                        <h1 class="text-lg font-bold text-slate-800 tracking-tight leading-none">Dashboard Monitoring Data Statistik</h1>
                        <p class="text-[10px] text-slate-500 mt-1 font-medium uppercase tracking-tighter">Provinsi Sulawesi Tengah — SITATIK</p>
                    </div>

                    <div class="bg-slate-50 px-3 py-1 rounded-xl border border-slate-100 shadow-sm flex items-center gap-2">
                        <span class="font-black text-[9px] uppercase text-slate-400">Periode</span>
                        <form method="GET">
                            <select name="tahun" onchange="this.form.submit()"
                                class="appearance-none bg-transparent border-none text-xs font-bold p-0 pr-6 focus:ring-0 cursor-pointer">
                                @foreach (range(date('Y'), 2025) as $year)
                                    <option value="{{ $year }}" @selected(request('tahun', date('Y')) == $year)>{{ $year }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <div class="my-2 border-t border-slate-50"></div>

                <div class="grid grid-cols-4 gap-4">
                    <div class="p-1">
                        <p class="text-[8px] font-black text-blue-500 uppercase tracking-widest">Rata-rata</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-base font-black text-slate-700 tabular-nums">{{ $rata_progres }}%</span>
                            <div class="flex-1 h-1 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500" style="width: {{ $rata_progres }}%"></div>
                            </div>
                        </div>
                    </div>
                    @foreach([['Selesai', $opd_selesai, 'emerald'], ['Proses', $opd_proses, 'amber'], ['Belum', $opd_belum, 'rose']] as $stat)
                    <div class="p-1 pl-4 border-l border-slate-100">
                        <p class="text-[8px] font-black text-{{ $stat[2] }}-500 uppercase tracking-widest">{{ $stat[0] }}</p>
                        <p class="text-base font-black text-slate-700 leading-none mt-1">{{ $stat[1] }} <span class="text-[9px] text-slate-300 font-normal uppercase">OPD</span></p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- 2. MAIN CONTENT (MAP & SIDEBAR) --}}
            <div class="grid grid-cols-12 gap-4 flex-1 min-h-0 overflow-hidden">
                
                {{-- MAP (KIRI) --}}
                <div class="col-span-12 lg:col-span-8 bg-white rounded-3xl border shadow-sm flex flex-col overflow-hidden relative">
                    <div id="map" class="absolute inset-0 w-full h-full z-10"></div>
                </div>

                {{-- SIDEBAR (KANAN) --}}
                <div class="col-span-12 lg:col-span-4 bg-white rounded-3xl border shadow-sm flex flex-col min-h-0 overflow-hidden relative transition-all">
                    <div class="p-4 flex-shrink-0 bg-white z-20 border-b border-slate-50">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-black text-slate-800 uppercase text-[11px] tracking-widest">INFORMASI PROGRES OPD</h3>
                            <select id="select-opd" class="w-36 bg-slate-50 border-none text-[10px] font-bold rounded-xl focus:ring-0 cursor-pointer shadow-sm">
                                <option value="">Cari OPD...</option>
                                @foreach ($opds as $opd)
                                    <option value="{{ $opd->id }}">{{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="relative flex-1 min-h-0">
                        {{-- LIST UTAMA --}}
                        <div id="opd-list-container" class="absolute inset-0 overflow-y-auto space-y-2 px-4 py-0 custom-scrollbar transition-all duration-300">
                            @foreach ($opds as $opd)
                                @php $color = $opd->persentase >= 100 ? 'bg-emerald-500' : ($opd->persentase > 0 ? 'bg-amber-400' : 'bg-rose-500'); @endphp
                                <div class="cursor-pointer p-3 rounded-2xl bg-slate-50 hover:bg-white hover:shadow-md border border-transparent hover:border-blue-100 transition-all group"
                                    onclick="showOPDDetail({{ $opd->id }}); focusOPD({{ $opd->latitude ?? 0 }}, {{ $opd->longitude ?? 0 }}, '{{ $opd->id }}')">
                                    <div class="flex justify-between text-[10px] mb-2 font-black text-slate-600 uppercase tracking-tight">
                                        <span class="truncate w-40 group-hover:text-blue-600 transition-colors">{{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}</span>
                                        <span class="tabular-nums font-black">{{ $opd->persentase }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
                                        <div class="{{ $color }} h-full transition-all duration-700" style="width: {{ $opd->persentase }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- PANEL DETAIL --}}
                        <div id="opd-detail" class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-all duration-300 z-30 flex flex-col px-4 pt-0">
                            <div id="detail-content" class="h-full flex flex-col min-h-0 overflow-hidden"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. FOOTER (Diperkecil agar tidak memakan layar) --}}
            <div class="grid grid-cols-4 gap-3 flex-shrink-0">
                @foreach([
                    ['Σ', 'Total', number_format($total_rekomendasi).' Tabel', 'blue'],
                    ['✓', 'Terisi', number_format($tabel_aktif).' Tabel', 'emerald'],
                    ['G', 'Sektor', $total_kategori.' Sektor', 'purple'],
                    ['@', 'Operator', $opds->count().' Akun', 'orange']
                ] as $item)
                <div class="bg-white p-2.5 rounded-2xl border shadow-sm flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-{{ $item[3] }}-50 flex items-center justify-center text-{{ $item[3] }}-500 font-bold shadow-inner italic text-xs">{{ $item[0] }}</div>
                    <div>
                        <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">{{ $item[1] }}</p>
                        <p class="text-[10px] font-bold text-slate-700 leading-none">{{ $item[2] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- STYLE: Memperbaiki Scrollbar & Peta --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        #map { border-radius: 20px; }
        .leaflet-popup-content { font-weight: 800; text-transform: uppercase; font-size: 10px; }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        let opdData = @json($opds);

        function showOPDDetail(id) {
            const opd = opdData.find(o => o.id == id);
            if (!opd) return;

            document.getElementById('opd-detail').classList.remove('opacity-0', 'pointer-events-none');
            document.getElementById('opd-list-container').classList.add('opacity-0');

            let tabelHtml = '';
            if (opd.tabel_details && opd.tabel_details.length > 0) {
                opd.tabel_details.forEach(tabel => {
                    let badgeColor = tabel.status === 'Selesai' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 
                                    (tabel.status === 'Proses' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-slate-50 text-slate-400 border-slate-100');
                    
                    tabelHtml += `
                        <div class="flex items-start justify-between p-3 mb-2 bg-white border border-slate-100 rounded-xl shadow-sm group">
                            <div class="w-3/4">
                                <p class="text-[10px] font-black text-slate-700 uppercase leading-tight group-hover:text-blue-600 transition-colors">
                                    ` + (tabel.table_name || 'Tanpa Nama') + `
                                </p>
                            </div>
                            <span class="flex-shrink-0 px-2 py-0.5 rounded-lg text-[7px] font-black uppercase border ` + badgeColor + `">` + tabel.status + `</span>
                        </div>`;
                });
            } else {
                tabelHtml = `<div class="py-10 text-center opacity-30 text-[10px] font-black uppercase">Belum ada tugas</div>`;
            }

            document.getElementById('detail-content').innerHTML = `
                <div class="flex flex-col h-full overflow-hidden">
                    <div class="flex-shrink-0 mb-3">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <button onclick="closeDetail()" class="text-[9px] text-blue-600 font-black uppercase flex items-center bg-blue-50 px-2 py-1.5 rounded-lg hover:bg-blue-100 transition-all shadow-sm">
                                ← Kembali
                            </button>
                            <div class="text-right overflow-hidden">
                                <h3 class="font-black text-slate-800 text-[11px] uppercase leading-tight truncate">` + (opd.nama || opd.name) + `</h3>
                                <p class="text-[9px] text-slate-400 font-bold lowercase truncate">` + (opd.email || '-') + `</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl border border-slate-100">
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Progres</p>
                            <span class="text-[10px] font-black text-blue-600 bg-white px-2 py-0.5 rounded-md shadow-sm">` + (opd.persentase || 0) + `%</span>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto custom-scrollbar pr-1 pb-4">
                        ` + tabelHtml + `
                    </div>
                </div>`;
        }

        function closeDetail() {
            document.getElementById('opd-detail').classList.add('opacity-0', 'pointer-events-none');
            document.getElementById('opd-list-container').classList.remove('opacity-0');
            document.getElementById('select-opd').value = "";
            window.map.flyTo([-0.8917, 119.8707], 11);
        }

        document.addEventListener('DOMContentLoaded', function () {
            window.map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-0.8917, 119.8707], 11);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(map);
            window.markers = {};
            opdData.forEach(opd => {
                if (opd.latitude && opd.longitude) {
                    const marker = L.marker([opd.latitude, opd.longitude]).addTo(map);
                    
                    // Menggunakan class Tailwind langsung di dalam tag HTML
                    const popupName = opd.perangkat_daerah?.nama_opd || opd.nama || opd.name;
                    
                    // text-[8px] untuk mengecilkan teks secara ekstrem
                    // leading-none agar tidak ada jarak baris berlebih
                    const popupContent = '<p class="text-[8px] font-black uppercase text-slate-800 leading-none text-center">' + popupName + '</p>';
                    
                    marker.bindPopup(popupContent, {
                        maxWidth: 150,
                        closeButton: false // Menghilangkan tombol 'x' agar kotak lebih padat
                    });
                    
                    window.markers[opd.id] = marker;
                }
            });
            setTimeout(() => map.invalidateSize(), 500);
        });

        function focusOPD(lat, lng, id) {
            if(lat != 0) {
                window.map.flyTo([lat, lng], 16, { duration: 1.5 });
                if(window.markers[id]) window.markers[id].openPopup();
            }
        }

        document.getElementById('select-opd').addEventListener('change', function () {
            const id = this.value;
            if(!id) { closeDetail(); return; }
            fetch(`/dashboard/opd?opd_id=` + id + `&tahun={{ request('tahun', date('Y')) }}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        const opd = data[0];
                        const idx = opdData.findIndex(o => o.id == id);
                        if(idx !== -1) opdData[idx] = opd;
                        showOPDDetail(id);
                        if (opd.latitude) focusOPD(opd.latitude, opd.longitude, id);
                    }
                });
        });
    </script>
</x-app-layout>