<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Capaian Kualitas Air - Kelompok Acakadul</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #fff; padding: 40px; }
        .print-only { display: none; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .print-only { display: block; }
        }
        .report-card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; margin-bottom: 24px; }
        .badge-optimal { background-color: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 9999px; font-size: 10px; font-weight: 700; }
        .badge-proses { background-color: #fef9c3; color: #854d0e; padding: 4px 12px; border-radius: 9999px; font-size: 10px; font-weight: 700; }
        .badge-kurang { background-color: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 9999px; font-size: 10px; font-weight: 700; }
    </style>
</head>
<body class="max-w-4xl mx-auto">

    <!-- Actions (Top) -->
    <div class="flex justify-end gap-3 mb-8 no-print">
        <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-md font-bold text-xs hover:bg-slate-50 transition">Kembali ke Dashboard</a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-5 py-2 rounded-md font-bold text-xs shadow-sm hover:bg-blue-700 transition">Cetak Laporan</button>
    </div>

    <!-- Header Section -->
    <div class="flex justify-between items-start mb-10 border-b-2 border-slate-900 pb-8">
        <div>
            <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-2">• Official System Report •</p>
            <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">SE - PHONK 32</h1>
            <p class="text-[9px] text-slate-500 font-bold uppercase mt-1 tracking-widest leading-relaxed max-w-md">Submersible Environmental - Precision Hydro Observation Network Kit 32</p>
            <p class="text-[10px] text-blue-600 font-bold mt-4 uppercase">PERIODE LOG: {{ $summary['start_date']->format('d M Y') }} s.d {{ $summary['end_date']->format('d M Y') }}</p>
        </div>
        <div class="text-right">
            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Status Kualitas</span>
            @php
                $overallStatus = ($summary['avg_ph'] >= 6.5 && $summary['avg_ph'] <= 8.5) ? 'OPTIMAL' : 'PERLU TINDAKAN';
                $statusColor = $overallStatus == 'OPTIMAL' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700';
            @endphp
            <span class="px-4 py-1.5 {{ $statusColor }} font-black rounded text-xs tracking-tighter">{{ $overallStatus }}</span>
        </div>
    </div>

    <!-- Summary Chart -->
    <div class="report-card mb-10">
        <h3 class="text-sm font-bold text-slate-800 uppercase mb-6 flex items-center gap-2">
            <span class="w-1 h-4 bg-blue-600 rounded"></span> III. GRAFIK RINGKASAN PARAMETER
        </h3>
        <div id="chart"></div>
    </div>

    <!-- Details Table -->
    <div class="mb-10">
        <h3 class="text-sm font-bold text-slate-800 uppercase mb-4 flex items-center gap-2">
            <span class="w-1 h-4 bg-blue-600 rounded"></span> IV. RINCIAN CAPAIAN PARAMETER
        </h3>
        <div class="overflow-hidden border border-slate-200 rounded-lg">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="border-b border-slate-200 p-3 text-left">NO</th>
                        <th class="border-b border-slate-200 p-3 text-left">ASPEK PENILAIAN</th>
                        <th class="border-b border-slate-200 p-3">TARGET</th>
                        <th class="border-b border-slate-200 p-3">AKTUAL (AVG)</th>
                        <th class="border-b border-slate-200 p-3">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-slate-100">
                        <td class="p-3 text-center">1</td>
                        <td class="p-3 font-bold">TINGKAT KEASAMAN (pH)</td>
                        <td class="p-3 text-center">6.5 - 8.5</td>
                        <td class="p-3 text-center font-bold">{{ number_format($summary['avg_ph'], 1) }}</td>
                        <td class="p-3 text-center">
                            @if($summary['avg_ph'] >= 6.5 && $summary['avg_ph'] <= 8.5)
                                <span class="badge-optimal">STABIL</span>
                            @else
                                <span class="badge-kurang">ANOMALI</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="p-3 text-center">2</td>
                        <td class="p-3 font-bold">DISSOLVED OXYGEN (DO)</td>
                        <td class="p-3 text-center">> 5 mg/L</td>
                        <td class="p-3 text-center font-bold">{{ number_format($summary['avg_do'], 1) }}</td>
                        <td class="p-3 text-center">
                            @if($summary['avg_do'] >= 5)
                                <span class="badge-optimal">OPTIMAL</span>
                            @else
                                <span class="badge-kurang">KURANG</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="p-3 text-center">3</td>
                        <td class="p-3 font-bold">KEKERUHAN (TURBIDITY)</td>
                        <td class="p-3 text-center">< 5 NTU</td>
                        <td class="p-3 text-center font-bold">{{ number_format($summary['avg_turbidity'], 1) }}</td>
                        <td class="p-3 text-center">
                            @if($summary['avg_turbidity'] <= 5)
                                <span class="badge-optimal">JERNIH</span>
                            @else
                                <span class="badge-kurang">KERUH</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- History Table -->
    <div class="mb-12">
        <h3 class="text-sm font-bold text-slate-800 uppercase mb-4 flex items-center gap-2">
            <span class="w-1 h-4 bg-blue-600 rounded"></span> V. RINCIAN LOG DATA (10 Terakhir)
        </h3>
        <div class="overflow-hidden border border-slate-200 rounded-lg">
            <table class="w-full text-[10px]">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="p-2 text-left uppercase tracking-wider">WAKTU</th>
                        <th class="p-2 text-center uppercase tracking-wider">pH</th>
                        <th class="p-2 text-center uppercase tracking-wider">TEMP</th>
                        <th class="p-2 text-center uppercase tracking-wider">DO</th>
                        <th class="p-2 text-right uppercase tracking-wider">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($logs->take(10) as $index => $log)
                    <tr>
                        <td class="p-2 text-slate-500">{{ $log->created_at->format('H:i:s') }}</td>
                        <td class="p-2 text-center font-bold">{{ $log->ph }}</td>
                        <td class="p-2 text-center">{{ $log->temperature }}°C</td>
                        <td class="p-2 text-center font-bold text-blue-600">{{ $log->do }}</td>
                        <td class="p-2 text-right">
                            @if($log->ph >= 6.5 && $log->ph <= 8.5)
                                <span class="text-emerald-600 font-bold">BERSIH</span>
                            @else
                                <span class="text-red-500 font-bold">WASPADAI</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer / Signature -->
    <div class="flex justify-between items-end mt-20 pt-8 border-t border-slate-100">
        <div class="text-slate-400 text-[10px] font-bold uppercase italic tracking-wider">
            Sistem Pelaporan Otomatis AquaScan Pro
        </div>
        <div class="text-center">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-12">Bogor, {{ date('d F Y') }}</p>
            <p class="text-sm font-bold text-slate-800 border-t-2 border-slate-800 pt-2 uppercase">MENGETAHUI, KURATOR DATA</p>
        </div>
    </div>

    <script>
        var options = {
            series: [{
                name: 'Capaian Aktual',
                data: [
                    {{ number_format($summary['avg_ph'], 1) }}, 
                    {{ number_format($summary['avg_do'], 1) }}, 
                    {{ number_format($summary['avg_turbidity'], 1) }}
                ]
            }],
            chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: ['#3b82f6'],
                    inverseColors: true,
                    opacityFrom: 0.9,
                    opacityTo: 0.7,
                    stops: [0, 100]
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 2,
                    horizontal: false,
                    columnWidth: '45%',
                }
            },
            dataLabels: { 
                enabled: true,
                style: { fontSize: '11px', fontWeight: 700 }
            },
            colors: ['#2563eb'],
            xaxis: {
                categories: ['Tingkat pH', 'Oksigen (DO)', 'Kekeruhan'],
                labels: { style: { fontWeight: 600 } }
            },
            legend: { show: false }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
</body>
</html>
