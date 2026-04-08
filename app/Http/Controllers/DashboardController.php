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
                'adult' => ['pn' => $aircraft->pn_adult, 'types' => ['business', 'economy', 'first', 'spare-pax']],
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

                $sixMonths = $today->copy()->addMonths(6);
                $threeMonths = $today->copy()->addMonths(3);

                $expired = $catSeats->filter(fn($s) => $s->expiry_date && \Carbon\Carbon::parse($s->expiry_date)->lt($today))->count();
                $critical = $catSeats->filter(fn($s) => $s->expiry_date && \Carbon\Carbon::parse($s->expiry_date)->gte($today) && \Carbon\Carbon::parse($s->expiry_date)->lt($threeMonths))->count();
                $warning = $catSeats->filter(fn($s) => $s->expiry_date && \Carbon\Carbon::parse($s->expiry_date)->gte($threeMonths) && \Carbon\Carbon::parse($s->expiry_date)->lt($sixMonths))->count();

                $key = $pn . '|' . $category;
                if (!isset($pnSummary[$key])) {
                    $pnSummary[$key] = [
                        'pn' => $pn,
                        'category' => $category,
                        'total' => 0,
                        'expired' => 0,
                        'critical' => 0,
                        'warning' => 0,
                        'aircraft' => [],
                    ];
                }
                $pnSummary[$key]['total'] += $total;
                $pnSummary[$key]['expired'] += $expired;
                $pnSummary[$key]['critical'] += $critical;
                $pnSummary[$key]['warning'] += $warning;
                if ($expired > 0 || $critical > 0 || $warning > 0) {
                    $pnSummary[$key]['aircraft'][] = [
                        'reg' => $reg,
                        'expired' => $expired,
                        'critical' => $critical,
                        'warning' => $warning,
                    ];
                }
            }
        }

        // Sort: most attention-needed first, then alphabetically
        usort($pnSummary, function ($a, $b) {
            $aAttention = $a['expired'] + $a['critical'] + $a['warning'];
            $bAttention = $b['expired'] + $b['critical'] + $b['warning'];
            if ($bAttention !== $aAttention)
                return $bAttention - $aAttention;
            return strcmp($a['pn'], $b['pn']);
        });

        // ============================================================
        // Build Monthly Replacement Plan
        // Groups all seats by expiry month with P/N + aircraft breakdown
        // ============================================================
        $monthlyPlan = [];
        $sixMonthsAhead = $today->copy()->addMonths(6)->endOfMonth();

        foreach ($aircrafts as $aircraft) {
            $reg = $aircraft->registration;
            $acType = $aircraft->type;
            $pnMap = [
                'adult' => ['pn' => $aircraft->pn_adult, 'types' => ['business', 'economy', 'first', 'spare-pax']],
                'crew' => ['pn' => $aircraft->pn_crew, 'types' => ['cockpit', 'attendant']],
                'infant' => ['pn' => $aircraft->pn_infant, 'types' => ['spare-inf']],
            ];

            $acSeats = Seat::where('registration', $reg)->whereNotNull('expiry_date')->get();

            foreach ($acSeats as $seat) {
                $expiryDate = \Carbon\Carbon::parse($seat->expiry_date);

                // Only include seats expiring within 6 months ahead (and overdue)
                if ($expiryDate->gt($sixMonthsAhead)) {
                    continue;
                }

                // Determine month key
                if ($expiryDate->lt($today)) {
                    $monthKey = 'overdue';
                    $monthLabel = 'Overdue';
                    $monthSort = '0000-00'; // Sort first
                } else {
                    $monthKey = $expiryDate->format('Y-m');
                    $monthLabel = $expiryDate->format('F Y');
                    $monthSort = $monthKey;
                }

                // Determine which P/N category this seat belongs to
                $seatPn = null;
                $seatCategory = null;
                foreach ($pnMap as $category => $info) {
                    if (in_array($seat->class_type, $info['types'])) {
                        $seatPn = $info['pn'];
                        $seatCategory = $category;
                        break;
                    }
                }
                // Skip if no PN (means no life vest for this category) or unknown class_type
                if (!$seatPn || !$seatCategory) {
                    continue;
                }

                // Initialize month bucket
                if (!isset($monthlyPlan[$monthKey])) {
                    $monthlyPlan[$monthKey] = [
                        'key' => $monthKey,
                        'label' => $monthLabel,
                        'sort' => $monthSort,
                        'total' => 0,
                        'pn_breakdown' => [],
                        'aircraft_breakdown' => [],
                    ];
                }

                $monthlyPlan[$monthKey]['total']++;

                // P/N breakdown
                $pnKey = $seatPn . '|' . $seatCategory;
                if (!isset($monthlyPlan[$monthKey]['pn_breakdown'][$pnKey])) {
                    $monthlyPlan[$monthKey]['pn_breakdown'][$pnKey] = [
                        'pn' => $seatPn,
                        'category' => $seatCategory,
                        'count' => 0,
                        'aircraft' => [],
                    ];
                }
                $monthlyPlan[$monthKey]['pn_breakdown'][$pnKey]['count']++;

                // Aircraft breakdown within P/N
                if (!isset($monthlyPlan[$monthKey]['pn_breakdown'][$pnKey]['aircraft'][$reg])) {
                    $monthlyPlan[$monthKey]['pn_breakdown'][$pnKey]['aircraft'][$reg] = 0;
                }
                $monthlyPlan[$monthKey]['pn_breakdown'][$pnKey]['aircraft'][$reg]++;

                // Aircraft total breakdown
                if (!isset($monthlyPlan[$monthKey]['aircraft_breakdown'][$reg])) {
                    $monthlyPlan[$monthKey]['aircraft_breakdown'][$reg] = [
                        'type' => $acType,
                        'count' => 0,
                    ];
                }
                $monthlyPlan[$monthKey]['aircraft_breakdown'][$reg]['count']++;
            }
        }

        // Sort by month (overdue first, then chronological)
        uasort($monthlyPlan, function ($a, $b) {
            return strcmp($a['sort'], $b['sort']);
        });

        // Determine urgency level for each month
        // Match dashboard color scheme: overdue=purple, critical(<3mo)=red, warning(3-6mo)=yellow
        $currentMonth = $today->format('Y-m');
        $threeMonthsBoundary = $today->copy()->addMonths(3);
        $sixMonthsBoundary = $today->copy()->addMonths(6);

        foreach ($monthlyPlan as $key => &$month) {
            $month['isCurrentMonth'] = ($key === $currentMonth);

            if ($key === 'overdue') {
                $month['urgency'] = 'overdue'; // 🟣 Expired - purple
            } else {
                $monthStart = \Carbon\Carbon::createFromFormat('Y-m', $key)->startOfMonth();
                if ($monthStart->lt($threeMonthsBoundary)) {
                    $month['urgency'] = 'critical'; // 🔴 < 3 months - red
                } else {
                    $month['urgency'] = 'warning'; // 🟡 3-6 months - yellow
                }
            }
        }
        unset($month);

        return view('dashboard', [
            'fleet' => $fleet,
            'fleetByAirline' => $fleetByAirline,
            'totalStats' => $totalStats,
            'perFleetStats' => $perFleetStats,
            'lastUpdate' => $lastUpdate ? \Carbon\Carbon::parse($lastUpdate) : null,
            'pnSummary' => $pnSummary,
            'monthlyPlan' => $monthlyPlan,
        ]);
    }
}
