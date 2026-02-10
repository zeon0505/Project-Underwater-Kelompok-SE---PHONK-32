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
        
        :root {
            --bg-color: #050505;
            --text-color: #f8fafc;
            --card-bg: #121212;
            --card-border: #262626;
            --header-bg: #0a0a0a;
            --shadow: 0 10px 15px -3px rgba(255, 255, 255, 0.05), 0 4px 6px -2px rgba(255, 255, 255, 0.03);
            --text-muted: #94a3b8;
            --chart-grid: #334155;
        }

        [data-theme="light"] {
            --bg-color: #f8fafc;
            --text-color: #1e293b;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --header-bg: #2563eb;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --text-muted: #64748b;
            --chart-grid: #e2e8f0;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
        }

        .main-header { 
            background-color: var(--header-bg); 
            border-bottom: 1px solid var(--card-border); 
            box-shadow: var(--shadow);
            transition: background-color 0.3s, border-color 0.3s, box-shadow 0.3s;
        }
        
        .instrument-card { 
            background: var(--card-bg); 
            border: 1px solid var(--card-border); 
            border-radius: 12px; 
            box-shadow: var(--shadow);
            transition: background-color 0.3s, border-color 0.3s, box-shadow 0.3s, transform 0.3s;
        }
        .instrument-card:hover { 
            border-color: #3b82f6; 
            transform: translateY(-2px); 
        }
        
        .param-label { font-size: 0.70rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        .param-value { font-size: 2rem; font-weight: 800; color: var(--text-color); text-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .param-unit { font-size: 0.875rem; font-weight: 600; color: var(--text-muted); margin-left: 0.125rem; }

        /* Light mode specific overrides */
        [data-theme="light"] .main-header h1, 
        [data-theme="light"] .main-header p,
        [data-theme="light"] .main-header .text-xs,
        [data-theme="light"] #clock { color: white !important; }
        [data-theme="light"] .main-header { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="min-h-screen flex flex-col selection:bg-blue-500 selection:text-white">

    <!-- Header Panel -->
    <header class="main-header px-6 py-4 flex justify-between items-center shadow-lg border-b border-slate-800">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-md border border-white/10">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <h1 class="text-xl font-black tracking-tight uppercase leading-none text-shadow-sm" style="color: var(--text-color);">SE - PHONK 32</h1>
                <p class="text-[9px] font-bold tracking-widest uppercase mt-1.5 opacity-80" style="color: var(--text-muted);">Submersible Environmental - Precision Hydro Observation Network Kit 32</p>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex items-center gap-4">
                <button id="theme-toggle" class="p-2 bg-white/10 rounded-lg text-white hover:bg-white/20 transition backdrop-blur-sm">
                    <svg id="moon-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg id="sun-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color: var(--text-muted);">Live System Clock</div>
                    <div id="clock" class="text-xl font-black leading-none font-mono tracking-widest" style="color: var(--text-color);">00:00:00</div>
                </div>
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
                <h2 class="text-2xl font-black tracking-tight" style="color: var(--text-color);">Dashboard Monitoring <span class="text-blue-500">Real-Time</span></h2>
                <p class="text-sm font-medium" style="color: var(--text-muted);">Status kesehatan drone dan parameter kualitas air terkini</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('report') }}" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-lg text-xs font-black uppercase hover:bg-blue-700 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Lihat Laporan
                </a>
                <button onclick="handleRefresh(this)" class="flex items-center gap-2 px-5 py-2.5 border border-slate-700 bg-[#1e1e1e] text-slate-300 rounded-lg text-xs font-black uppercase hover:bg-slate-800 hover:text-white transition shadow-lg group">
                    <svg class="w-4 h-4 group-active:animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> 
                    Refresh Panel
                </button>
            </div>
        </div>

        <!-- Main Instruments Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- pH Sensor -->
            <a href="{{ route('sensor.details', 'ph') }}" class="instrument-card p-6 border-l-4 border-l-emerald-500 cursor-pointer block hover:scale-[1.02]">
                <div class="flex items-center justify-between mb-4">
                    <span class="param-label">Tingkat Keasaman (pH)</span>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.2)]">OPTIMAL</span>
                </div>
                <div class="flex items-baseline"><span class="param-value" id="ph-val">{{ $latest ? $latest->ph : '--' }}</span><span class="param-unit">pH</span></div>
            </a>

            <!-- Temperature Sensor -->
            <a href="{{ route('sensor.details', 'temperature') }}" class="instrument-card p-6 border-l-4 border-l-blue-500 cursor-pointer block hover:scale-[1.02]">
                <div class="flex items-center justify-between mb-4">
                    <span class="param-label">Suhu Cairan</span>
                    <span class="text-[10px] font-bold text-blue-400 bg-blue-500/10 border border-blue-500/20 px-2 py-0.5 rounded-full shadow-[0_0_10px_rgba(59,130,246,0.2)]">STABIL</span>
                </div>
                <div class="flex items-baseline"><span class="param-value" id="temp-val">{{ $latest ? $latest->temperature : '--' }}</span><span class="param-unit">°C</span></div>
            </a>
            
            <!-- Turbidity Sensor -->
            <a href="{{ route('sensor.details', 'turbidity') }}" class="instrument-card p-6 border-l-4 border-l-indigo-500 cursor-pointer block hover:scale-[1.02]">
                <div class="flex items-center justify-between mb-4">
                    <span class="param-label">Kekeruhan (Turbidity)</span>
                    <span class="text-[10px] font-bold text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded-full shadow-[0_0_10px_rgba(99,102,241,0.2)]">JERNIH</span>
                </div>
                <div class="flex items-baseline"><span class="param-value" id="turbidity-val">{{ $latest ? $latest->turbidity : '--' }}</span><span class="param-unit">NTU</span></div>
            </a>
        </div>

        <!-- Historical Chart -->
        <div class="instrument-card p-6">
            <h3 class="text-sm font-bold uppercase mb-6 flex items-center gap-2" style="color: var(--text-color);">
                <span class="w-1 h-4 bg-blue-500 rounded-full shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span> Analisis Historis Parameter
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
            chart: { height: 350, type: 'area', toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, sans-serif', background: 'transparent' },
            theme: { mode: 'dark' },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    shadeIntensity: 1,
                    opacityFrom: 0.5,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            colors: ['#3b82f6', '#f97316'],
            stroke: { curve: 'smooth', width: 3 },
            grid: {
                borderColor: '#334155',
                strokeDashArray: 3,
            },
            xaxis: { 
                categories: {!! json_encode($labels) !!}, 
                labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 } }
            },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '10px', fontWeight: 700, labels: { colors: '#cbd5e1' } },
            tooltip: { theme: 'dark' }
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

        function updateLiveClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-GB', { hour12: false });
            document.getElementById('clock').innerText = timeString;
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();
        // Theme Toggle Logic
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
                chart.updateOptions({ 
                    theme: { mode: 'light' },
                    grid: { borderColor: '#e2e8f0' },
                    xaxis: { labels: { style: { colors: '#64748b' } } },
                    yaxis: { labels: { style: { colors: '#64748b' } } },
                    legend: { labels: { colors: '#1e293b' } }
                });
            } else {
                document.getElementById('sun-icon').classList.add('hidden');
                document.getElementById('moon-icon').classList.remove('hidden');
                chart.updateOptions({ 
                    theme: { mode: 'dark' },
                    grid: { borderColor: '#334155' },
                    xaxis: { labels: { style: { colors: '#94a3b8' } } },
                    yaxis: { labels: { style: { colors: '#94a3b8' } } },
                    legend: { labels: { colors: '#cbd5e1' } }
                });
            }
        });
    </script>
</body>
</html>
