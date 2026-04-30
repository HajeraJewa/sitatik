<x-app-layout>
    <x-slot name="header"> Dashboard Monitoring </x-slot>

    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-4 overflow-hidden">
        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-4 mt-4 min-h-0">

            {{-- HEADER --}}
            <div class="bg-white px-6 py-4 rounded-2xl border shadow-sm flex-shrink-0">
                <div class="flex justify-between items-center gap-4">
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Dashboard Monitoring Data Statistik
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Provinsi Sulawesi Tengah — SITATIK
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-slate-500">Periode</span>
                        <form method="GET">
                            <select name="tahun" onchange="this.form.submit()"
                                class="appearance-none bg-slate-100 border border-gray-200 text-xs px-3 py-1.5 pr-8 rounded-lg focus:outline-none">
                                @foreach (range(date('Y'), 2025) as $year)
                                    <option value="{{ $year }}" @selected(request('tahun', date('Y')) == $year)>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <div class="my-4 border-t border-slate-100"></div>

                <div class="grid grid-cols-4 gap-4">

                    {{-- RATA RATA --}}
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <p class="text-[10px] font-semibold text-blue-600 uppercase mb-1">
                            Rata-rata
                        </p>

                        <div class="flex items-center gap-3">
                            <span class="text-lg font-semibold text-slate-800 tabular-nums">
                                {{ $rata_progres }}%
                            </span>

                            <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500"
                                    style="width: {{ $rata_progres }}%">
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach([
                        ['Selesai', $opd_selesai, 'emerald'],
                        ['Proses', $opd_proses, 'amber'],
                        ['Belum', $opd_belum, 'rose']
                    ] as $stat)

                    <div class="p-3 bg-slate-50 rounded-xl">
                        <p class="text-[10px] font-semibold text-{{ $stat[2] }}-600 uppercase mb-1">
                            {{ $stat[0] }}
                        </p>

                        <p class="text-lg font-semibold text-slate-800">
                            {{ $stat[1] }}
                            <span class="text-xs text-slate-400 font-normal">OPD</span>
                        </p>
                    </div>

                    @endforeach
                </div>
            </div>

            {{-- MAIN --}}
            <div class="grid grid-cols-12 gap-4 flex-1 min-h-0 overflow-hidden">

                {{-- MAP --}}
                <div class="col-span-12 lg:col-span-8 bg-white rounded-2xl border shadow-sm flex flex-col overflow-hidden relative">
                    <div id="map" class="absolute inset-0 w-full h-full"></div>
                </div>

                {{-- SIDEBAR --}}
                <div class="col-span-12 lg:col-span-4 bg-white rounded-2xl border shadow-sm flex flex-col min-h-0 overflow-hidden">

                    {{-- SIDEBAR HEADER --}}
                    <div class="p-4 border-b border-slate-100">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-semibold text-slate-800 text-sm">
                                Informasi Progres OPD
                            </h3>

                            <select id="select-opd"
                                class="w-40 bg-slate-100 border border-gray-200 text-xs px-3 py-1.5 rounded-lg focus:outline-none">
                                <option value="">Cari OPD...</option>
                                @foreach ($opds as $opd)
                                    <option value="{{ $opd->id }}">
                                        {{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="relative flex-1 min-h-0">

                        {{-- LIST --}}
                        <div id="opd-list-container"
                            class="absolute inset-0 overflow-y-auto space-y-2 px-4 py-3 custom-scrollbar">

                            @foreach ($opds as $opd)

                            @php
                                $color = $opd->persentase >= 100
                                    ? 'bg-emerald-500'
                                    : ($opd->persentase > 0
                                        ? 'bg-amber-400'
                                        : 'bg-rose-500');
                            @endphp

                            <div
                                class="cursor-pointer px-4 py-3 rounded-xl bg-slate-50 hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-200 transition"
                                onclick="showOPDDetail({{ $opd->id }}); focusOPD({{ $opd->latitude ?? 0 }}, {{ $opd->longitude ?? 0 }}, '{{ $opd->id }}')">

                                <div class="flex justify-between text-xs mb-2 font-semibold text-slate-700">
                                    <span class="truncate w-44">
                                        {{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}
                                    </span>

                                    <span class="tabular-nums">
                                        {{ $opd->persentase }}%
                                    </span>
                                </div>

                                <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                                    <div class="{{ $color }} h-full"
                                        style="width: {{ $opd->persentase }}%">
                                    </div>
                                </div>

                            </div>

                            @endforeach
                        </div>

                        {{-- DETAIL --}}
                        <div id="opd-detail"
                            class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-all duration-300 z-30 flex flex-col px-4 pt-3">

                            <div id="detail-content"
                                class="h-full flex flex-col min-h-0 overflow-hidden">
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="grid grid-cols-4 gap-4 flex-shrink-0">

                @foreach([
                    ['Total', number_format($total_rekomendasi).' Tabel', 'blue'],
                    ['Terisi', number_format($tabel_aktif).' Tabel', 'emerald'],
                    ['Sektor', $total_kategori.' Sektor', 'purple'],
                    ['Operator', $opds->count().' Akun', 'orange']
                ] as $item)

                <div class="bg-white p-4 rounded-2xl border shadow-sm">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase">
                        {{ $item[0] }}
                    </p>

                    <p class="text-sm font-semibold text-slate-800 mt-1">
                        {{ $item[1] }}
                    </p>
                </div>

                @endforeach

            </div>

        </div>
    </div>

<style>
.custom-scrollbar::-webkit-scrollbar { width:6px; }
.custom-scrollbar::-webkit-scrollbar-track { background:#f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background:#94a3b8; }
#map { border-radius: 16px; }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let opdData = @json($opds);

function showOPDDetail(id) {
    const opd = opdData.find(o => o.id == id);
    if (!opd) return;

    document.getElementById('opd-detail')
        .classList.remove('opacity-0', 'pointer-events-none');

    document.getElementById('opd-list-container')
        .classList.add('opacity-0');

    let tabelHtml = '';

    if (opd.tabel_details && opd.tabel_details.length > 0) {
        opd.tabel_details.forEach(tabel => {

            let badgeColor =
                tabel.status === 'Selesai'
                ? 'bg-emerald-100 text-emerald-700'
                : (tabel.status === 'Proses'
                    ? 'bg-amber-100 text-amber-700'
                    : 'bg-slate-100 text-slate-500');

            tabelHtml += `
                <div class="p-3 mb-2 bg-white border rounded-xl">
                    <div class="flex justify-between items-start">
                        <p class="text-xs font-semibold text-slate-700">
                            ${tabel.table_name || 'Tanpa Nama'}
                        </p>

                        <span class="text-[10px] px-2 py-0.5 rounded-lg font-semibold ${badgeColor}">
                            ${tabel.status}
                        </span>
                    </div>
                </div>
            `;
        });
    } else {
        tabelHtml = `
            <div class="py-10 text-center text-xs text-slate-400">
                Belum ada tugas
            </div>`;
    }

    document.getElementById('detail-content').innerHTML = `
        <div class="flex flex-col h-full">

            <div class="mb-3">
                <button onclick="closeDetail()"
                    class="text-xs text-blue-600 font-medium mb-2">
                    ← Kembali
                </button>

                <h3 class="font-semibold text-slate-800">
                    ${opd.nama || opd.name}
                </h3>

                <p class="text-xs text-slate-400">
                    ${opd.email || '-'}
                </p>

                <div class="mt-3 bg-slate-50 p-3 rounded-xl">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Progres</span>
                        <span class="font-semibold text-blue-600">
                            ${opd.persentase || 0}%
                        </span>
                    </div>
                </div>

            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar pr-1">
                ${tabelHtml}
            </div>

        </div>
    `;
}

function closeDetail() {
    document.getElementById('opd-detail')
        .classList.add('opacity-0', 'pointer-events-none');

    document.getElementById('opd-list-container')
        .classList.remove('opacity-0');

    document.getElementById('select-opd').value = "";
}

document.addEventListener('DOMContentLoaded', function () {

    window.map = L.map('map', {
        zoomControl:false,
        attributionControl:false
    }).setView([-0.8917, 119.8707], 11);

    L.tileLayer(
        'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png'
    ).addTo(map);

    window.markers = {};

    opdData.forEach(opd => {
        if (opd.latitude && opd.longitude) {

            const marker = L.marker([
                opd.latitude,
                opd.longitude
            ]).addTo(map);

            marker.bindPopup(
                '<p class="text-xs font-semibold">'
                + (opd.nama || opd.name)
                + '</p>'
            );

            window.markers[opd.id] = marker;
        }
    });

});

function focusOPD(lat,lng,id){
    if(lat!=0){
        window.map.flyTo([lat,lng],16);
        if(window.markers[id]) window.markers[id].openPopup();
    }
}

document.getElementById('select-opd')
.addEventListener('change',function(){

    const id = this.value;

    if(!id){
        closeDetail();
        return;
    }

    fetch(`/dashboard/opd?opd_id=`+id+`&tahun={{ request('tahun', date('Y')) }}`)
    .then(res=>res.json())
    .then(data=>{
        if(data.length>0){
            const opd=data[0];

            const idx=opdData.findIndex(o=>o.id==id);
            if(idx!==-1) opdData[idx]=opd;

            showOPDDetail(id);

            if(opd.latitude)
                focusOPD(opd.latitude,opd.longitude,id);
        }
    });

});
</script>

</x-app-layout>