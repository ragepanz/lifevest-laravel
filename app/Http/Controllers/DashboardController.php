<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // OLD: $layouts = config('aircraft_layouts');
        // Load from Database (All status: active & prolong)
        $aircrafts = \App\Models\Aircraft::all();

        $fleet = [];
        $totalStats = [
            'safe' => 0,
            'warning' => 0,
            'critical' => 0,
            'expired' => 0,
            'no_data' => 0,
        ];

        foreach ($aircrafts as $aircraft) {
            $registration = $aircraft->registration;
            $seats = Seat::where('registration', $registration)->get();

            $stats = [
                'safe' => 0,
                'warning' => 0,
                'critical' => 0,
                'expired' => 0,
                'no_data' => 0,
            ];

            foreach ($seats as $seat) {
                $status = $seat->status;
                $key = $status === 'no-data' ? 'no_data' : $status;
                $stats[$key]++;
                $totalStats[$key]++;
            }

            $total = array_sum($stats) ?: 1;
            $healthPercent = round(($stats['safe'] / $total) * 100);

            foreach ($aircrafts as $aircraft) {
                $registration = $aircraft->registration;
                // ... (stats logic) ...

                $fleet[$registration] = [
                    'type' => $aircraft->type,
                    'registration' => $registration,
                    'icon' => $aircraft->icon,
                    'status' => $aircraft->status, // Pass status to view
                    'stats' => $stats,
                    'health' => $healthPercent,
                ];
            }
        }

        // Get global last update time
        $lastUpdate = Seat::max('updated_at');

        // Group fleet by aircraft type
        $fleetByType = [];
        foreach ($fleet as $registration => $aircraft) {
            // Extract base type (B737, B777, A330, ATR72)
            preg_match('/^([A-Z]+\d+)/', $aircraft['type'], $matches);
            $baseType = $matches[1] ?? $aircraft['type'];

            if (!isset($fleetByType[$baseType])) {
                $fleetByType[$baseType] = [
                    'name' => $baseType . ' Fleet',
                    'icon' => $aircraft['icon'],
                    'aircraft' => [],
                ];
            }
            $fleetByType[$baseType]['aircraft'][$registration] = $aircraft;
        }

        return view('dashboard', [
            'fleet' => $fleet,
            'fleetByType' => $fleetByType,
            'totalStats' => $totalStats,
            'lastUpdate' => $lastUpdate ? \Carbon\Carbon::parse($lastUpdate) : null,
        ]);
    }
}
