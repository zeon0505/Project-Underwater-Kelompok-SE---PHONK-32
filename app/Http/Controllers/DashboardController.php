<?php

namespace App\Http\Controllers;

use App\Models\WaterQualityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $latest = WaterQualityLog::latest()->first();
        $history = WaterQualityLog::latest()->take(20)->get()->reverse();

        // Jika data kosong, gunakan data dummy untuk tampilan awal
        if (!$latest) {
            $latest = new WaterQualityLog([
                'ph' => 7.2,
                'temperature' => 26.5,
                'tds' => 450,
                'turbidity' => 5.2,
                'ec' => 1200,
                'do' => 6.8,
                'drone_id' => 'Drone-Simulasi'
            ]);
            $latest->created_at = now();
            
            // Buat history dummy
            $history = collect();
            for ($i = 10; $i >= 0; $i--) {
                $log = new WaterQualityLog([
                    'ph' => 7.0 + (rand(0, 4) / 10.0),
                    'temperature' => 25.0 + (rand(0, 30) / 10.0),
                    'tds' => 400 + rand(0, 100),
                    'turbidity' => 4.0 + (rand(0, 20) / 10.0),
                    'ec' => 1100 + rand(0, 200),
                    'do' => 6.0 + (rand(0, 15) / 10.0),
                ]);
                $log->created_at = now()->subMinutes($i);
                $history->push($log);
            }
        }

        return view('dashboard', compact('latest', 'history'));
    }

    public function store(Request $request)
    {
        // Simple API Token Check
        $token = $request->bearerToken();
        if ($token !== env('DRONE_API_TOKEN')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized Access'], 401);
        }

        $data = $request->validate([
            'ph' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'tds' => 'nullable|integer',
            'turbidity' => 'nullable|numeric',
            'ec' => 'nullable|numeric',
            'do' => 'nullable|numeric',
            'drone_id' => 'nullable|string',
        ]);

        $log = WaterQualityLog::create($data);
        
        // Simpan ke cache untuk polling cepat
        cache()->put('latest_sensor_data', $log->toArray(), now()->addMinutes(5));

        return response()->json(['status' => 'success', 'data' => $log]);
    }

    public function apiData()
    {
        $latest = WaterQualityLog::latest()->first();
        $history = WaterQualityLog::latest()->take(20)->get()->reverse();
        
        return response()->json([
            'latest' => $latest,
            'history' => $history
        ]);
    }

    public function exportCsv()
    {
        $logs = WaterQualityLog::orderBy('created_at', 'desc')->get();
        $filename = "sensor_data_" . date('Ymd_His') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Waktu', 'pH', 'Suhu (C)', 'TDS (ppm)', 'Turbidity (NTU)', 'EC (uS/cm)', 'DO (mg/L)', 'Drone ID'];

        $callback = function() use($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at,
                    $log->ph,
                    $log->temperature,
                    $log->tds,
                    $log->turbidity,
                    $log->ec,
                    $log->do,
                    $log->drone_id,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function report()
    {
        $logs = WaterQualityLog::orderBy('created_at', 'desc')->take(100)->get();
        
        // Jika data kosong, gunakan simulasi agar laporan tidak "hening"
        if ($logs->isEmpty()) {
            $logs = collect();
            for ($i = 20; $i >= 0; $i--) {
                $log = new WaterQualityLog([
                    'ph' => 7.0 + (rand(0, 4) / 10.0),
                    'temperature' => 25.0 + (rand(0, 30) / 10.0),
                    'tds' => 400 + rand(0, 100),
                    'turbidity' => 3.0 + (rand(0, 20) / 10.0),
                    'ec' => 1100 + rand(0, 200),
                    'do' => 6.0 + (rand(0, 15) / 10.0),
                    'drone_id' => 'Simulasi-Report'
                ]);
                $log->created_at = now()->subHours($i);
                $logs->prepend($log); // Terbaru di atas
            }
        }

        $summary = [
            'avg_ph' => $logs->avg('ph'),
            'avg_temp' => $logs->avg('temperature'),
            'avg_tds' => $logs->avg('tds'),
            'avg_turbidity' => $logs->avg('turbidity'),
            'avg_do' => $logs->avg('do'),
            'count' => $logs->count(),
            'start_date' => $logs->last()->created_at,
            'end_date' => $logs->first()->created_at
        ];

        return view('report', compact('logs', 'summary'));
    }
    public function sensorDetails($type)
    {
        $validTypes = ['ph', 'temperature', 'turbidity'];
        if (!in_array($type, $validTypes)) {
            abort(404);
        }

        $logs = WaterQualityLog::orderBy('created_at', 'desc')->take(100)->get();
        
        // Fill with dummy data if empty (sames as report logic)
        if ($logs->isEmpty()) {
            $logs = collect();
            for ($i = 20; $i >= 0; $i--) {
                $log = new WaterQualityLog([
                    'ph' => 7.0 + (rand(0, 4) / 10.0),
                    'temperature' => 25.0 + (rand(0, 30) / 10.0),
                    'tds' => 400 + rand(0, 100),
                    'turbidity' => 3.0 + (rand(0, 20) / 10.0),
                    'ec' => 1100 + rand(0, 200),
                    'do' => 6.0 + (rand(0, 15) / 10.0),
                ]);
                $log->created_at = now()->subHours($i);
                $logs->prepend($log);
            }
        }

        $latest = $logs->first();
        
        $chartData = $logs->map(function ($log) use ($type) {
            return [
                'x' => $log->created_at->format('H:i'),
                'y' => $log->$type
            ];
        })->reverse()->values();

        $titles = [
            'ph' => 'Tingkat Keasaman (pH)',
            'temperature' => 'Suhu Cairan',
            'turbidity' => 'Kekeruhan (Turbidity)'
        ];

        $units = [
            'ph' => 'pH',
            'temperature' => '°C',
            'turbidity' => 'NTU'
        ];

        return view('sensor-details', [
            'type' => $type,
            'title' => $titles[$type],
            'unit' => $units[$type],
            'latest' => $latest,
            'chartData' => $chartData
        ]);
    }
}
