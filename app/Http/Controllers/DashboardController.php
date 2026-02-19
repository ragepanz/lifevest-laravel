<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Aircraft;
use App\Models\Airline;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // Load from Database (All status: active & prolong) with airline relationship
        $aircrafts = Aircraft::with('airline')->get();

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

            $fleet[$registration] = [
                'type' => $aircraft->type,
                'registration' => $registration,
                'icon' => $aircraft->icon ?? '✈️',
                'status' => $aircraft->status,
                'stats' => $stats,
                'health' => $healthPercent,
                'airline_id' => $aircraft->airline_id,
                'airline_name' => $aircraft->airline?->name ?? 'Unknown',
                'airline_icon' => $aircraft->airline?->icon ?? '🏢',
            ];
        }

        // Build per-fleet-type stats
        $perFleetStats = [];
        foreach ($fleet as $registration => $acData) {
            preg_match('/^([A-Z]+\d+)/', $acData['type'], $matches);
            $baseType = $matches[1] ?? $acData['type'];

            if (!isset($perFleetStats[$baseType])) {
                $perFleetStats[$baseType] = [
                    'safe' => 0,
                    'warning' => 0,
                    'critical' => 0,
                    'expired' => 0,
                    'no_data' => 0,
                    'count' => 0,
                ];
            }
            $perFleetStats[$baseType]['safe'] += $acData['stats']['safe'];
            $perFleetStats[$baseType]['warning'] += $acData['stats']['warning'];
            $perFleetStats[$baseType]['critical'] += $acData['stats']['critical'];
            $perFleetStats[$baseType]['expired'] += $acData['stats']['expired'];
            $perFleetStats[$baseType]['no_data'] += $acData['stats']['no_data'];
            $perFleetStats[$baseType]['count']++;
        }
        ksort($perFleetStats);

        // Get global last update time
        $lastUpdate = Seat::max('updated_at');

        // Get all airlines for grouping
        $airlines = Airline::all();

        // Group fleet by Airline -> then by Aircraft Type
        $fleetByAirline = [];
        foreach ($airlines as $airline) {
            $airlineFleet = collect($fleet)->filter(fn($a) => $a['airline_id'] === $airline->id);

            if ($airlineFleet->isEmpty()) {
                continue; // Skip airlines with no aircraft
            }

            // Group by aircraft type within this airline
            $byType = [];
            foreach ($airlineFleet as $registration => $aircraft) {
                // Extract base type (B737, B777, A330, ATR72)
                preg_match('/^([A-Z]+\d+)/', $aircraft['type'], $matches);
                $baseType = $matches[1] ?? $aircraft['type'];

                if (!isset($byType[$baseType])) {
                    $byType[$baseType] = [
                        'name' => $baseType . ' Fleet',
                        'icon' => $aircraft['icon'],
                        'aircraft' => [],
                    ];
                }
                $byType[$baseType]['aircraft'][$registration] = $aircraft;
            }

            $fleetByAirline[$airline->id] = [
                'name' => $airline->name,
                'icon' => $airline->icon,
                'code' => $airline->code,
                'types' => $byType,
                'aircraft_count' => $airlineFleet->count(),
            ];
        }

        // Build Part Number replacement summary
        $today = now()->startOfDay();
        $pnSummary = [];

        foreach ($aircrafts as $aircraft) {
            $reg = $aircraft->registration;
            $pnMap = [
                'adult' => ['pn' => $aircraft->pn_adult, 'types' => ['business', 'economy', 'spare-pax']],
                'crew' => ['pn' => $aircraft->pn_crew, 'types' => ['cockpit', 'attendant']],
                'infant' => ['pn' => $aircraft->pn_infant, 'types' => ['spare-inf']],
            ];

            $acSeats = Seat::where('registration', $reg)->get();

            foreach ($pnMap as $category => $info) {
                if (empty($info['pn']))
                    continue;

                $pn = $info['pn'];
                $catSeats = $acSeats->filter(fn($s) => in_array($s->class_type, $info['types']));
                $total = $catSeats->count();
                $expired = $catSeats->filter(fn($s) => $s->expiry_date && \Carbon\Carbon::parse($s->expiry_date)->lt($today))->count();

                $key = $pn . '|' . $category;
                if (!isset($pnSummary[$key])) {
                    $pnSummary[$key] = [
                        'pn' => $pn,
                        'category' => $category,
                        'total' => 0,
                        'expired' => 0,
                        'aircraft' => [],
                    ];
                }
                $pnSummary[$key]['total'] += $total;
                $pnSummary[$key]['expired'] += $expired;
                if ($expired > 0) {
                    $pnSummary[$key]['aircraft'][] = ['reg' => $reg, 'expired' => $expired];
                }
            }
        }

        // Sort: expired first (descending), then alphabetically
        usort($pnSummary, function ($a, $b) {
            if ($b['expired'] !== $a['expired'])
                return $b['expired'] - $a['expired'];
            return strcmp($a['pn'], $b['pn']);
        });

        return view('dashboard', [
            'fleet' => $fleet,
            'fleetByAirline' => $fleetByAirline,
            'totalStats' => $totalStats,
            'perFleetStats' => $perFleetStats,
            'lastUpdate' => $lastUpdate ? \Carbon\Carbon::parse($lastUpdate) : null,
            'pnSummary' => $pnSummary,
        ]);
    }
}
