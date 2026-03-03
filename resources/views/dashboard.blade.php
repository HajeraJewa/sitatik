<x-app-layout>
    {{-- Container Utama: Mengunci layar agar tidak ada scroll luar --}}
    <div class="h-[calc(100vh-64px)] bg-slate-50 overflow-hidden flex flex-col p-3">
        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-3">

            {{-- 1. HEADER & GLOBAL PROGRESS --}}
            <div
                class="flex flex-col xl:flex-row xl:items-center justify-between bg-white p-4 rounded-[1.5rem] shadow-sm border border-gray-100 flex-shrink-0">
                <div class="mb-2 xl:mb-0">
                    <h2 class="text-xl font-black text-slate-800 tracking-tighter uppercase leading-none">Dashboard
                        Monitoring</h2>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Provinsi Sulawesi
                        Tengah • Periode 2026</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 xl:gap-8">
                    <div class="flex flex-col items-center xl:items-start border-l border-gray-50 pl-4">
                        <p class="text-[8px] font-black text-slate-400 uppercase mb-1 tracking-wider">Progres Global</p>
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-black text-slate-800">{{ $rata_progres }}%</span>
                            <div class="w-16 bg-gray-100 h-1 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full transition-all duration-1000"
                                    style="width: {{ $rata_progres }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="border-l border-gray-100 pl-4 text-emerald-600">
                        <p class="text-[8px] font-black text-slate-400 uppercase mb-0.5">Selesai</p>
                        <p class="text-xl font-black leading-none">{{ $opd_selesai }}</p>
                    </div>
                    <div class="border-l border-gray-100 pl-4 text-orange-500">
                        <p class="text-[8px] font-black text-slate-400 uppercase mb-0.5">Proses</p>
                        <p class="text-xl font-black leading-none">{{ $opd_proses }}</p>
                    </div>
                    <div class="border-l border-gray-100 pl-4 text-rose-500">
                        <p class="text-[8px] font-black text-slate-400 uppercase mb-0.5">Belum</p>
                        <p class="text-xl font-black leading-none">{{ $opd_belum }}</p>
                    </div>
                </div>
            </div>

            {{-- 2. VISUALISASI UTAMA & SIDEBAR --}}
            <div class="grid grid-cols-12 gap-3 flex-1 min-h-0">
                {{-- LEFT: MAP --}}
                <div
                    class="col-span-12 lg:col-span-8 bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col relative">
                    <div
                        class="p-3 border-b border-gray-50 flex justify-between items-center bg-white/80 backdrop-blur-md z-[1000] absolute top-0 left-0 right-0">
                        <div class="flex items-center space-x-2">
                            <div class="p-1.5 bg-blue-600 rounded-lg text-white">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="font-black text-slate-800 uppercase text-[10px] tracking-tight italic">Digital
                                Twin Sulteng</h3>
                        </div>
                    </div>
                    <div id="map" class="flex-1 z-0"></div>
                </div>

                {{-- RIGHT: MONITORING LIST --}}
                <div
                    class="col-span-12 lg:col-span-4 bg-white rounded-[2rem] shadow-sm border border-gray-100 p-5 flex flex-col min-h-0">
                    <div class="mb-4 flex justify-between items-center border-b border-slate-50 pb-3">
                        <h3 class="font-black text-slate-800 uppercase text-xs tracking-tight">Informasi OPD</h3>
                        <select id="select-opd"
                            class="text-[9px] font-bold uppercase border-gray-200 bg-slate-50 rounded-lg py-1 shadow-sm cursor-pointer">
                            <option value="">Semua Instansi</option>
                            @foreach($opds as $opd)
                                <option value="{{ $opd->id }}">{{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="opd-list-container" class="flex-1 overflow-y-auto pr-1 custom-scrollbar space-y-4">
                        @foreach($opds as $opd)
                            <div class="opd-item group cursor-pointer border-l-4 border-transparent hover:border-blue-500 hover:bg-slate-50 p-2 rounded-r-xl transition-all"
                                onclick="focusOPD({{ $opd->latitude ?? 0 }}, {{ $opd->longitude ?? 0 }}, '{{ $opd->id }}')">
                                <div class="flex justify-between items-center mb-1.5">
                                    <span class="text-[10px] font-bold text-slate-700 uppercase truncate w-48"
                                        title="{{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}">
                                        {{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}
                                    </span>
                                    <span class="text-[10px] font-black text-blue-600">{{ $opd->persentase }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden shadow-inner">
                                    <div class="bg-gradient-to-r from-blue-500 to-blue-800 h-full transition-all duration-1000"
                                        style="width: {{ $opd->persentase }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. SUMMARY CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 pb-2 flex-shrink-0 text-left">
                <div class="bg-white p-4 rounded-[1.5rem] shadow-sm border-b-4 border-b-blue-600">
                    <p class="text-[8px] font-black text-slate-400 uppercase mb-1 italic">Total Data -</p>
                    <p class="text-xl font-black text-slate-800 leading-none">{{ number_format($total_rekomendasi) }}
                    </p>
                </div>
                <div class="bg-white p-4 rounded-[1.5rem] shadow-sm border border-gray-100">
                    <p class="text-[8px] font-black text-slate-400 uppercase mb-1 italic">Kategori -</p>
                    <p class="text-xl font-black text-slate-800 leading-none">{{ $total_kategori }}</p>
                </div>
                <div class="bg-white p-4 rounded-[1.5rem] shadow-sm border border-gray-100">
                    <p class="text-[8px] font-black text-slate-400 uppercase mb-1 italic">Tabel Aktif -</p>
                    <p class="text-xl font-black text-slate-800 leading-none">{{ number_format($tabel_aktif) }}</p>
                </div>
                <div
                    class="bg-blue-600 p-4 rounded-[1.5rem] shadow-lg border border-blue-500 text-white shadow-blue-100">
                    <p class="text-[8px] font-black text-blue-100 uppercase mb-1 italic">Operator -</p>
                    {{-- Hanya menghitung role operator --}}
                    <p class="text-xl font-black leading-none">{{ $opds->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-tooltip {
            background: #1e293b;
            color: white;
            border: none;
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            border-radius: 4px;
            padding: 3px 8px;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            padding: 0;
            overflow: hidden;
        }
    </style>

    {{-- SCRIPTS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Fix Marker Icon Path
            delete L.Icon.Default.prototype._getIconUrl;
            L.Icon.Default.mergeOptions({
                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            });

            // Init Map (Palu)
            window.map = L.map('map', { zoomControl: false }).setView([-0.8917, 119.8707], 13);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(map);

            const markersData = @json($opds_map);
            window.markers = {};

            if (markersData && markersData.length > 0) {
                markersData.forEach(opd => {
                    const lat = parseFloat(opd.latitude);
                    const lng = parseFloat(opd.longitude);

                    if (!isNaN(lat) && !isNaN(lng) && lat !== 0) {
                        const marker = L.marker([lat, lng]).addTo(map);
                        const nama = opd.perangkat_daerah ? opd.perangkat_daerah.nama_opd : opd.name;

                        const popupHTML = `
                            <div class="p-2 min-w-[150px] font-sans">
                                <h4 class="font-black text-blue-700 text-[11px] mb-2 leading-tight uppercase">${nama}</h4>
                                <div class="space-y-1 border-t pt-2 border-slate-100 text-[10px]">
                                    <div class="flex justify-between">
                                        <span class="text-slate-400 font-bold uppercase tracking-widest">Progres</span>
                                        <span class="text-slate-800 font-black">${opd.persentase}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-1 rounded-full overflow-hidden">
                                        <div class="bg-blue-600 h-full" style="width: ${opd.persentase}%"></div>
                                    </div>
                                </div>
                            </div>
                        `;

                        marker.bindPopup(popupHTML);
                        marker.bindTooltip(nama, { direction: 'top', className: 'custom-tooltip' });
                        window.markers[opd.id] = marker;
                    }
                });
            }

            setTimeout(() => { window.map.invalidateSize(); }, 500);

            // Dropdown Listener
            document.getElementById('select-opd').addEventListener('change', function () {
                const selectedId = this.value;
                if (selectedId === "") {
                    window.map.flyTo([-0.8917, 119.8707], 13);
                } else {
                    const opd = markersData.find(item => item.id == selectedId);
                    if (opd) focusOPD(opd.latitude, opd.longitude, selectedId);
                }
            });
        });

        function focusOPD(lat, lng, id) {
            const latitude = parseFloat(lat);
            const longitude = parseFloat(lng);
            if (!isNaN(latitude) && !isNaN(longitude) && latitude !== 0) {
                window.map.flyTo([latitude, longitude], 17, { animate: true, duration: 1.5 });
                setTimeout(() => {
                    if (window.markers[id]) window.markers[id].openPopup();
                }, 1600);
            } else {
                alert("Instansi ini belum mengatur titik koordinat.");
            }
        }
    </script>
</x-app-layout>