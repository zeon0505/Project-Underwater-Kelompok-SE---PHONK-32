<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - SE-PHONK 32</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap');
        
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
            transition: background-color 0.3s, border-color 0.3s, box-shadow 0.3s;
        }
        
        /* Light mode specific overrides */
        [data-theme="light"] .main-header h1, 
        [data-theme="light"] .main-header p { color: white !important; }
        [data-theme="light"] .main-header { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="min-h-screen flex flex-col selection:bg-blue-500 selection:text-white">

    <!-- Header Panel -->
    <header class="main-header px-6 py-4 flex justify-between items-center shadow-lg border-b border-slate-800">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-md border border-white/10 hover:bg-white/20 transition">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-black tracking-tight uppercase leading-none text-shadow-sm" style="color: var(--text-color);">{{ $title }}</h1>
                <p class="text-[9px] font-bold tracking-widest uppercase mt-1.5 opacity-80" style="color: var(--text-muted);">Detail Analisis & Riwayat Data</p>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <button id="theme-toggle" class="p-2 bg-white/10 rounded-lg text-white hover:bg-white/20 transition backdrop-blur-sm">
                <svg id="moon-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                <svg id="sun-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </button>
        </div>
    </header>

    <main class="flex-1 p-8 max-w-7xl mx-auto w-full">
        
        <!-- Current Status Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div class="instrument-card p-8 md:col-span-1 flex flex-col justify-center items-center text-center">
                <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color: var(--text-muted);">Pembacaan Terkini</p>
                <div class="text-6xl font-black mb-2" style="color: var(--text-color);">
                    {{ number_format($latest->$type, 1) }}<span class="text-2xl font-bold ml-1 text-slate-500">{{ $unit }}</span>
                </div>
                
                @php
                    $status = 'NORMAL';
                    $color = 'text-blue-500';
                    $bgColor = 'bg-blue-500/10 border-blue-500/20';
                    
                    if ($type == 'ph') {
                        if ($latest->ph < 6.5 || $latest->ph > 8.5) {
                            $status = 'PERLU PERHATIAN';
                            $color = 'text-red-500';
                            $bgColor = 'bg-red-500/10 border-red-500/20';
                        } else {
                            $status = 'OPTIMAL';
                            $color = 'text-emerald-500';
                            $bgColor = 'bg-emerald-500/10 border-emerald-500/20';
                        }
                    } elseif ($type == 'temperature') {
                        if ($latest->temperature > 30 || $latest->temperature < 20) {
                             $status = 'EKSTRIM';
                             $color = 'text-red-500';
                             $bgColor = 'bg-red-500/10 border-red-500/20';
                        } else {
                             $status = 'STABIL';
                             $color = 'text-blue-500';
                             $bgColor = 'bg-blue-500/10 border-blue-500/20';
                        }
                    } elseif ($type == 'turbidity') {
                        if ($latest->turbidity > 5) {
                            $status = 'KERUH';
                            $color = 'text-orange-500';
                            $bgColor = 'bg-orange-500/10 border-orange-500/20';
                        } else {
                            $status = 'JERNIH';
                            $color = 'text-indigo-500';
                            $bgColor = 'bg-indigo-500/10 border-indigo-500/20';
                        }
                    }
                @endphp

                <div class="px-4 py-1.5 rounded-full border {{ $bgColor }} {{ $color }} text-xs font-black tracking-widest uppercase">
                    {{ $status }}
                </div>
                <p class="text-[10px] mt-4 opacity-60" style="color: var(--text-muted);">Diperbarui: {{ $latest->created_at->format('H:i:s d M Y') }}</p>
            </div>

            <!-- Chart Section -->
            <div class="instrument-card p-6 md:col-span-2">
                <h3 class="text-sm font-bold uppercase mb-6 flex items-center gap-2" style="color: var(--text-color);">
                    <span class="w-1 h-4 bg-blue-500 rounded-full shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span> Grafik Tren (100 Data Terakhir)
                </h3>
                <div id="detail-chart" class="h-80 w-full"></div>
            </div>
        </div>

    </main>

    <script>
        // Init Theme
        if (localStorage.theme === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
            document.getElementById('moon-icon').classList.add('hidden');
            document.getElementById('sun-icon').classList.remove('hidden');
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
        }

        // Toggle Theme
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
                });
            } else {
                document.getElementById('sun-icon').classList.add('hidden');
                document.getElementById('moon-icon').classList.remove('hidden');
                chart.updateOptions({ 
                    theme: { mode: 'dark' },
                    grid: { borderColor: '#334155' },
                    xaxis: { labels: { style: { colors: '#94a3b8' } } },
                    yaxis: { labels: { style: { colors: '#94a3b8' } } },
                });
            }
        });

        // Chart Data
        const chartData = {!! json_encode($chartData) !!};
        const type = '{{ $type }}';
        
        // Colors based on type
        let color = '#3b82f6'; // Blue default
        if (type === 'ph') color = '#10b981'; // Emerald
        if (type === 'turbidity') color = '#f97316'; // Orange

        var options = {
            series: [{
                name: '{{ $title }}',
                data: chartData
            }],
            chart: { 
                height: 320, 
                type: 'area', 
                toolbar: { show: false }, 
                zoom: { enabled: false }, 
                fontFamily: 'Inter, sans-serif', 
                background: 'transparent' 
            },
            theme: { mode: localStorage.theme === 'light' ? 'light' : 'dark' },
            colors: [color],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            grid: {
                borderColor: localStorage.theme === 'light' ? '#e2e8f0' : '#334155',
                strokeDashArray: 3,
            },
            xaxis: {
                type: 'category',
                labels: { 
                    style: { 
                        colors: localStorage.theme === 'light' ? '#64748b' : '#94a3b8', 
                        fontSize: '10px', 
                        fontWeight: 600 
                    },
                    formatter: function(val) {
                        return val; // Simple format
                    }
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
                tickAmount: 10
            },
            yaxis: {
                labels: { 
                    style: { 
                        colors: localStorage.theme === 'light' ? '#64748b' : '#94a3b8', 
                        fontSize: '10px', 
                        fontWeight: 600 
                    } 
                }
            },
            tooltip: { theme: localStorage.theme === 'light' ? 'light' : 'dark' }
        };

        var chart = new ApexCharts(document.querySelector("#detail-chart"), options);
        chart.render();
    </script>
</body>
</html>
