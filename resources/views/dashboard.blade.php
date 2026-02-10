@php
    $phData = $history->pluck('ph')->toArray();
    $tempData = $history->pluck('temperature')->toArray();
    $tdsData = $history->pluck('tds')->toArray();
    $turbidityData = $history->pluck('turbidity')->toArray();
    $ecData = $history->pluck('ec')->toArray();
    $doData = $history->pluck('do')->toArray();
    $labels = $history->map(fn($item) => $item->created_at ? $item->created_at->format('H:i') : '--:--')->toArray();

    if (empty($labels)) {
        $labels = ['--:--'];
        $phData = [0]; $tempData = [0]; $tdsData = [0];
        $turbidityData = [0]; $ecData = [0]; $doData = [0];
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelompok Acakadul - Scientific Monitor</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .main-header { background-color: #000000; } /* Pure black for premium high-tech feel */
        
        .instrument-card { 
            background: white; 
            border: 1px solid #e2e8f0; 
            border-radius: 8px; 
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        .instrument-card:hover { border-color: #2563eb; transform: translateY(-1px); }
        
        .param-label { font-size: 0.70rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.025em; }
        .param-value { font-size: 2rem; font-weight: 800; color: #1e293b; }
        .param-unit { font-size: 0.875rem; font-weight: 600; color: #94a3b8; margin-left: 0.125rem; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 flex flex-col">

    <!-- Header Panel -->
    <header class="main-header px-6 py-4 flex justify-between items-center shadow-lg border-b border-slate-800">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-md border border-white/10">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-white tracking-tight uppercase leading-none">SE - PHONK 32</h1>
                <p class="text-[9px] text-blue-400 font-bold tracking-widest uppercase mt-1.5 opacity-80">Submersible Environmental - Precision Hydro Observation Network Kit 32</p>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="text-right">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Waktu Sistem</p>
                <p id="live-clock" class="text-sm font-bold text-white">00:00:00</p>
            </div>
            <div class="h-8 w-px bg-slate-700"></div>
            <div class="text-right">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Update Terakhir</p>
                <p id="update-time" class="text-sm font-bold text-white">{{ $latest ? $latest->created_at->format('H:i:s') : '00:00' }}</p>
            </div>
        </div>
    </header>

    <main class="flex-1 p-8 max-w-7xl mx-auto w-full">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Dashboard Monitoring <span class="text-blue-600">Real-Time</span></h2>
                <p class="text-sm text-slate-500 font-medium">Status kesehatan drone dan parameter kualitas air terkini</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('report') }}" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-lg text-xs font-black uppercase hover:bg-blue-700 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Lihat Laporan
                </a>
                <button onclick="handleRefresh(this)" class="flex items-center gap-2 px-5 py-2.5 border border-slate-300 bg-white text-slate-600 rounded-lg text-xs font-black uppercase hover:bg-slate-50 transition shadow-sm group">
                    <svg class="w-4 h-4 group-active:animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> 
                    Refresh Panel
                </button>
            </div>
        </div>

        <!-- Sensor Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- pH Area -->
            <div class="instrument-card p-6 border-l-4 border-l-blue-600">
                <div class="flex items-center justify-between mb-4">
                    <span class="param-label">Tingkat Keasaman (pH)</span>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">OPTIMAL</span>
                </div>
                <div class="flex items-baseline"><span class="param-value" id="ph-val">{{ $latest ? number_format($latest->ph, 1) : '--' }}</span><span class="param-unit">pH</span></div>
            </div>

            <!-- Suhu Area -->
            <div class="instrument-card p-6 border-l-4 border-l-orange-500">
                <div class="flex items-center justify-between mb-4">
                    <span class="param-label">Suhu Cairan</span>
                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">STABIL</span>
                </div>
                <div class="flex items-baseline"><span class="param-value" id="temp-val">{{ $latest ? number_format($latest->temperature, 1) : '--' }}</span><span class="param-unit">°C</span></div>
            </div>

            <!-- TDS Area -->
            <div class="instrument-card p-6 border-l-4 border-l-emerald-500">
                <div class="flex items-center justify-between mb-4">
                    <span class="param-label">TDS (Kemurnian)</span>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">BERSIH</span>
                </div>
                <div class="flex items-baseline"><span class="param-value" id="tds-val">{{ $latest ? $latest->tds : '--' }}</span><span class="param-unit">ppm</span></div>
            </div>

            <!-- Turbidity Area -->
            <div class="instrument-card p-6 border-l-4 border-l-amber-500">
                <div class="flex items-center justify-between mb-4">
                    <span class="param-label">Kekeruhan (Turbidity)</span>
                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">JERNIH</span>
                </div>
                <div class="flex items-baseline"><span class="param-value" id="turbidity-val">{{ $latest ? number_format($latest->turbidity, 1) : '--' }}</span><span class="param-unit">NTU</span></div>
            </div>

            <!-- EC Area -->
            <div class="instrument-card p-6 border-l-4 border-l-purple-500">
                <div class="flex items-center justify-between mb-4">
                    <span class="param-label">Konduktivitas Air (EC)</span>
                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">NORMAL</span>
                </div>
                <div class="flex items-baseline"><span class="param-value" id="ec-val">{{ $latest ? number_format($latest->ec, 0) : '--' }}</span><span class="param-unit">µS/cm</span></div>
            </div>

            <!-- DO Area -->
            <div class="instrument-card p-6 border-l-4 border-l-cyan-500">
                <div class="flex items-center justify-between mb-4">
                    <span class="param-label">Oksigen Terlarut (DO)</span>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">KAYA</span>
                </div>
                <div class="flex items-baseline"><span class="param-value" id="do-val">{{ $latest ? number_format($latest->do, 1) : '--' }}</span><span class="param-unit">mg/L</span></div>
            </div>
        </div>

        <!-- Historical Chart -->
        <div class="instrument-card p-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase mb-6 flex items-center gap-2">
                <span class="w-1 h-4 bg-blue-600 rounded-full"></span> Analisis Historis Parameter
            </h3>
            <div id="main-chart" class="h-80 w-full"></div>
        </div>
    </main>


    <script>
        // Live Clock
        setInterval(() => {
            const now = new Date();
            document.getElementById('live-clock').innerText = now.toLocaleTimeString('id-ID', { hour12: false });
        }, 1000);

        function handleRefresh(btn) {
            btn.querySelector('svg').classList.add('animate-spin');
            setTimeout(() => window.location.reload(), 500);
        }

        var options = {
            series: [
                { name: 'pH', data: {!! json_encode($phData) !!} },
                { name: 'Suhu', data: {!! json_encode($tempData) !!} }
            ],
            chart: { height: 350, type: 'area', toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, sans-serif' },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            colors: ['#2563eb', '#ea580c'],
            stroke: { curve: 'smooth', width: 3 },
            xaxis: { categories: {!! json_encode($labels) !!}, labels: { style: { fontSize: '10px', fontWeight: 600 } } },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '10px', fontWeight: 700 },
            tooltip: { theme: 'light' }
        };

        var chart = new ApexCharts(document.querySelector("#main-chart"), options);
        chart.render();

        setInterval(() => {
            fetch('/api/dashboard-data').then(r => r.json()).then(data => {
                if (data.latest) {
                    document.getElementById('ph-val').innerText = parseFloat(data.latest.ph).toFixed(1);
                    document.getElementById('temp-val').innerText = parseFloat(data.latest.temperature).toFixed(1);
                    document.getElementById('tds-val').innerText = data.latest.tds;
                    document.getElementById('turbidity-val').innerText = parseFloat(data.latest.turbidity).toFixed(1);
                    document.getElementById('ec-val').innerText = Math.round(data.latest.ec);
                    document.getElementById('do-val').innerText = parseFloat(data.latest.do).toFixed(1);
                    document.getElementById('update-time').innerText = new Date(data.latest.created_at).toLocaleTimeString('id-ID', { hour12: false });
                }
            });
        }, 5000);
    </script>
</body>
</html>
