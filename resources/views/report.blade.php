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

        :root {
            --bg-color: #050505;
            --text-color: #f8fafc;
            --card-bg: #121212;
            --card-border: #262626;
            --shadow: 0 10px 15px -3px rgba(255, 255, 255, 0.05), 0 4px 6px -2px rgba(255, 255, 255, 0.03);
            --header-bg: #0a0a0a;
            --table-header-bg: #1e1e1e;
            --table-row-border: #262626;
            --text-muted: #94a3b8;
        }

        [data-theme="light"] {
            --bg-color: #ffffff;
            --text-color: #1e293b;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --header-bg: #f8fafc;
            --table-header-bg: #f1f5f9;
            --table-row-border: #e2e8f0;
            --text-muted: #64748b;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-color); 
            color: var(--text-color); 
            padding: 40px; 
            transition: background-color 0.3s, color 0.3s;
        }
        .print-only { display: none; }
        @media print {
            body { padding: 0; background-color: #fff !important; color: #000 !important; }
            .report-card, .table-container { border: 1px solid #ddd !important; box-shadow: none !important; background: #fff !important; }
            .no-print { display: none; }
            .print-only { display: block; }
            .main-header p, .main-header h1 { color: #000 !important; }
        }
        .report-card { 
            background: var(--card-bg); 
            border: 1px solid var(--card-border); 
            border-radius: 12px; 
            padding: 24px; 
            margin-bottom: 24px; 
            box-shadow: var(--shadow);
            transition: background-color 0.3s, border-color 0.3s, box-shadow 0.3s;
        }
        .badge-optimal { background-color: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); padding: 4px 12px; border-radius: 9999px; font-size: 10px; font-weight: 700; box-shadow: 0 0 10px rgba(16, 185, 129, 0.2); }
        .badge-proses { background-color: rgba(234, 179, 8, 0.1); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.2); padding: 4px 12px; border-radius: 9999px; font-size: 10px; font-weight: 700; }
        .badge-kurang { background-color: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); padding: 4px 12px; border-radius: 9999px; font-size: 10px; font-weight: 700; box-shadow: 0 0 10px rgba(239, 68, 68, 0.2); }
        h1, h3 { color: var(--text-color) !important; transition: color 0.3s; }
        table th { background-color: var(--table-header-bg) !important; color: var(--text-muted) !important; border-bottom: 1px solid var(--card-border) !important; transition: background-color 0.3s, color 0.3s; }
        table td { border-bottom: 1px solid var(--table-row-border) !important; color: var(--text-color); transition: border-color 0.3s, color 0.3s; }
        .border-slate-200 { border-color: var(--card-border) !important; transition: border-color 0.3s; }
        .bg-slate-50 { background-color: var(--table-header-bg) !important; transition: background-color 0.3s; }
    </style>
</head>
<body class="max-w-4xl mx-auto">

    <!-- Actions (Top) -->
    <!-- Actions (Top) -->
    <div class="flex justify-end gap-3 mb-8 no-print">
        <button id="theme-toggle" class="p-2 bg-slate-800 text-white rounded-md hover:bg-slate-700 transition">
            <svg id="moon-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            <svg id="sun-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </button>
        <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-slate-500 text-slate-400 rounded-md font-bold text-xs hover:bg-slate-800 transition">Kembali ke Dashboard</a>
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
                        <td class="p-3 font-bold">SUHU CAIRAN (TEMP)</td>
                        <td class="p-3 text-center">20°C - 30°C</td>
                        <td class="p-3 text-center font-bold">{{ number_format($summary['avg_temp'], 1) }} °C</td>
                        <td class="p-3 text-center">
                            @if($summary['avg_temp'] >= 20 && $summary['avg_temp'] <= 30)
                                <span class="badge-optimal">STABIL</span>
                            @else
                                <span class="badge-kurang">EKSTRIM</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="p-3 text-center">3</td>
                        <td class="p-3 font-bold">KEKERUHAN (TURBIDITY)</td>
                        <td class="p-3 text-center">< 5 NTU</td>
                        <td class="p-3 text-center font-bold">{{ number_format($summary['avg_turbidity'], 1) }} NTU</td>
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
                        <th class="p-2 text-center uppercase tracking-wider">TURB</th>
                        <th class="p-2 text-right uppercase tracking-wider">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($logs->take(10) as $index => $log)
                    <tr>
                        <td class="p-2 text-slate-500">{{ $log->created_at->format('H:i:s') }}</td>
                        <td class="p-2 text-center font-bold">{{ $log->ph }}</td>
                        <td class="p-2 text-center">{{ $log->temperature }}°C</td>
                        <td class="p-2 text-center font-bold text-blue-500">{{ $log->turbidity }}</td>
                        <td class="p-2 text-right">
                            @if($log->ph >= 6.5 && $log->ph <= 8.5)
                                <span class="text-emerald-500 font-bold">BERSIH</span>
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
        // Initialize Theme
        if (localStorage.theme === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
            document.getElementById('moon-icon').classList.add('hidden');
            document.getElementById('sun-icon').classList.remove('hidden');
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
        }

        document.getElementById('theme-toggle').addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.theme = newTheme;

            if (newTheme === 'light') {
                document.getElementById('moon-icon').classList.add('hidden');
                document.getElementById('sun-icon').classList.remove('hidden');
            } else {
                document.getElementById('sun-icon').classList.add('hidden');
                document.getElementById('moon-icon').classList.remove('hidden');
            }
        });

        var options = {
            series: [{
                name: 'Capaian Aktual',
                data: [
                    {{ number_format($summary['avg_ph'], 1) }}, 
                    {{ number_format($summary['avg_temp'], 1) }}, 
                    {{ number_format($summary['avg_turbidity'], 1) }}
                ]
            }],
            chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Inter, sans-serif', background: 'transparent' },
            theme: { mode: 'dark' }, /* ApexCharts supports theme, but we handle colors manually */
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
                categories: ['Tingkat pH', 'Suhu (°C)', 'Kekeruhan'],
                labels: { style: { fontWeight: 600, colors: '#94a3b8' } }
            },
            yaxis: {
                labels: { style: { colors: '#94a3b8' } }
            },
            legend: { show: false }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
</body>
</html>
