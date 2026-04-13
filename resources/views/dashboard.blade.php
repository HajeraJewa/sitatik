<x-app-layout>

    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-4">
        <div class="flex-1 max-w-[1600px] mx-auto w-full flex flex-col space-y-4">

            {{-- HEADER --}}
            <div class="mt-4 bg-white px-6 py-4 rounded-2xl border shadow-sm">

                <div class="flex justify-between items-center">

                    {{-- LEFT --}}
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Dashboard Monitoring Data Statistik
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Provinsi Sulawesi Tengah
                        </p>
                    </div>

                    {{-- RIGHT --}}
                    <div class="flex items-center gap-3">

                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <span class="font-medium">Periode</span>

                            <form method="GET">
                                <select name="tahun" onchange="this.form.submit()"
                                    class="appearance-none bg-slate-100 border border-gray-200 text-sm px-3 py-1.5 pr-8 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">

                                    @foreach (range(date('Y'), 2025) as $year)
                                        <option value="{{ $year }}" @selected(request('tahun', date('Y')) == $year)>
                                            {{ $year }}
                                        </option>
                                    @endforeach

                                </select>
                            </form>
                        </div>

                    </div>

                </div>

                <div class="my-4 border-t"></div>

                @php
                    $total_opd = $opd_selesai + $opd_proses + $opd_belum;
                @endphp

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

                    <div>
                        <p class="text-xs text-gray-500">Progres</p>
                        <div class="flex items-center gap-3 mt-1">
                            <span>{{ $rata_progres }}%</span>
                            <div class="flex-1 h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-green-500 rounded-full" style="width: {{ $rata_progres }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Selesai</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span>{{ $opd_selesai }}</span>
                            <div class="flex-1 h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-green-500 rounded-full"
                                    style="width: {{ $total_opd ? ($opd_selesai / $total_opd) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Proses</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span>{{ $opd_proses }}</span>
                            <div class="flex-1 h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-yellow-400 rounded-full"
                                    style="width: {{ $total_opd ? ($opd_proses / $total_opd) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Belum</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span>{{ $opd_belum }}</span>
                            <div class="flex-1 h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-red-500 rounded-full"
                                    style="width: {{ $total_opd ? ($opd_belum / $total_opd) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- MAIN --}}
            <div class="grid grid-cols-12 gap-4 flex-1 min-h-0">

                {{-- MAP --}}
                <div class="col-span-12 lg:col-span-8 bg-white rounded-2xl border flex flex-col overflow-hidden">
                    <div class="p-4 font-semibold border-b">
                        Peta Monitoring
                    </div>

                    <div id="map" class="flex-1 min-h-[400px]"></div>
                </div>

                {{-- SIDEBAR --}}
                <div class="col-span-12 lg:col-span-4 bg-white rounded-2xl border p-5 flex flex-col min-h-0">

                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold">Informasi OPD</h3>

                        <div class="ml-4">
                            <select id="select-opd"
                                class="w-52 appearance-none bg-slate-100 border border-gray-200 text-xs px-3 py-1.5 pr-8 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">Semua</option>
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
                        <div id="opd-list-container" class="absolute inset-0 overflow-y-auto space-y-3">

                            @foreach ($opds as $opd)
                                @php
                                    $color =
                                        $opd->persentase >= 75
                                            ? 'bg-green-500'
                                            : ($opd->persentase >= 50
                                                ? 'bg-yellow-400'
                                                : 'bg-red-500');
                                @endphp

                                <div class="cursor-pointer p-3 rounded-xl hover:bg-slate-50"
                                    onclick="showOPDDetail({{ $opd->id }}); focusOPD({{ $opd->latitude ?? 0 }}, {{ $opd->longitude ?? 0 }}, '{{ $opd->id }}')">

                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="truncate w-40">
                                            {{ $opd->perangkatDaerah->nama_opd ?? $opd->name }}
                                        </span>
                                        <span>{{ $opd->persentase }}%</span>
                                    </div>

                                    <div class="w-full bg-gray-200 h-2 rounded-full">
                                        <div class="{{ $color }} h-2 rounded-full"
                                            style="width: {{ $opd->persentase }}%"></div>
                                    </div>

                                </div>
                            @endforeach
                        </div>

                        {{-- DETAIL --}}
                        <div id="opd-detail"
                            class="absolute inset-0 bg-white p-4 opacity-0 pointer-events-none transition">

                            <div id="detail-content"></div>

                        </div>

                    </div>

                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

                <div class="bg-white p-4 rounded-xl shadow-sm">
                    <p>Total Data</p>
                    <p class="text-xl font-semibold">{{ number_format($total_rekomendasi) }}</p>
                </div>

                <div class="bg-white p-4 rounded-xl shadow-sm">
                    <p>Kategori</p>
                    <p class="text-xl font-semibold">{{ $total_kategori }}</p>
                </div>

                <div class="bg-white p-4 rounded-xl shadow-sm">
                    <p>Tabel</p>
                    <p class="text-xl font-semibold">{{ number_format($tabel_aktif) }}</p>
                </div>

                <div class="bg-white p-4 rounded-xl shadow-sm">
                    <p>Operator</p>
                    <p class="text-xl font-semibold">{{ $opds->count() }}</p>
                </div>

            </div>

        </div>
    </div>

    {{-- MAP SCRIPT --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            delete L.Icon.Default.prototype._getIconUrl;
            L.Icon.Default.mergeOptions({
                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            });

            window.map = L.map('map', {
                zoomControl: false
            }).setView([-0.8917, 119.8707], 13);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(map);

            const markersData = @json($opds_map);
            window.markers = {};

            markersData.forEach(opd => {
                const lat = parseFloat(opd.latitude);
                const lng = parseFloat(opd.longitude);

                if (!isNaN(lat) && !isNaN(lng)) {
                    const marker = L.marker([lat, lng]).addTo(map);
                    window.markers[opd.id] = marker;
                }
            });

            setTimeout(() => map.invalidateSize(), 500);
        });

        function focusOPD(lat, lng, id) {
            window.map.flyTo([lat, lng], 17);
        }
    </script>

    {{-- DETAIL --}}
    <script>
        const opdData = @json($opds);

        function showOPDDetail(id) {
            const opd = opdData.find(o => o.id == id);
            if (!opd) return;

            document.getElementById('opd-detail').classList.remove('opacity-0', 'pointer-events-none');

            document.getElementById('detail-content').innerHTML = `
        <button onclick="closeDetail()" class="text-xs text-blue-500 mb-2">← Kembali</button>
        <h3 class="font-semibold">${opd.name}</h3>
        <p class="text-sm text-gray-500 mt-2">Belum ada data tabel.</p>
    `;
        }
    </script>

    <script>
        function closeDetail() {
            // Tutup panel detail
            document.getElementById('opd-detail').classList.add('opacity-0', 'pointer-events-none');

            // 🔥 Reset dropdown ke "Semua"
            const select = document.getElementById('select-opd');
            select.value = "";

            // 🔥 Ambil ulang semua OPD
            fetch(`/dashboard/opd`)
                .then(res => res.json())
                .then(data => {

                    let html = '';

                    data.forEach(opd => {

                        let color = opd.persentase >= 75 ?
                            'bg-green-500' :
                            (opd.persentase >= 50 ? 'bg-yellow-400' : 'bg-red-500');

                        html += `
                    <div class="cursor-pointer p-3 rounded-xl hover:bg-slate-50"
                        onclick="showOPDDetail(${opd.id}); focusOPD(${opd.latitude ?? 0}, ${opd.longitude ?? 0}, '${opd.id}')">

                        <div class="flex justify-between text-xs mb-1">
                            <span class="truncate w-40">
                                ${opd.nama}
                            </span>
                            <span>${opd.persentase}%</span>
                        </div>

                        <div class="w-full bg-gray-200 h-2 rounded-full">
                            <div class="${color} h-2 rounded-full"
                                style="width: ${opd.persentase}%"></div>
                        </div>
                    </div>
                `;
                    });

                    document.getElementById('opd-list-container').innerHTML = html;
                });

            // 🔥 Reset map ke Palu (posisi awal kamu)
            window.map.flyTo([-0.8917, 119.8707], 13);
        }
    </script>

    {{-- 🔥 TAMBAHAN SCRIPT FILTER OPD (INI SAJA YANG BARU) --}}
    <script>
        document.getElementById('select-opd').addEventListener('change', function() {
            const opdId = this.value;

            fetch(`/dashboard/opd?opd_id=${opdId}`)
                .then(res => res.json())
                .then(data => {

                    let html = '';

                    data.forEach(opd => {

                        let color = opd.persentase >= 75 ?
                            'bg-green-500' :
                            (opd.persentase >= 50 ? 'bg-yellow-400' : 'bg-red-500');

                        html += `
                    <div class="cursor-pointer p-3 rounded-xl hover:bg-slate-50"
                        onclick="showOPDDetail(${opd.id}); focusOPD(${opd.latitude ?? 0}, ${opd.longitude ?? 0}, '${opd.id}')">

                        <div class="flex justify-between text-xs mb-1">
                            <span class="truncate w-40">
                                ${opd.nama}
                            </span>
                            <span>${opd.persentase}%</span>
                        </div>

                        <div class="w-full bg-gray-200 h-2 rounded-full">
                            <div class="${color} h-2 rounded-full"
                                style="width: ${opd.persentase}%"></div>
                        </div>
                    </div>
                `;
                    });

                    document.getElementById('opd-list-container').innerHTML = html;

                    // 🔥 INI YANG BARU (AUTO BUKA DETAIL)
                    if (data.length === 1) {
                        const opd = data[0];

                        showOPDDetail(opd.id);

                        if (opd.latitude && opd.longitude) {
                            focusOPD(opd.latitude, opd.longitude, opd.id);
                        }
                    }

                });
        });
    </script>

</x-app-layout>
