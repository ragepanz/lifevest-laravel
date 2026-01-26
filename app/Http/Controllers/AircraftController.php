<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use Illuminate\Http\Request;

class AircraftController extends Controller
{
    /**
     * Show aircraft seat map
     */
    public function show(string $registration)
    {
        // OLD: $layout = config("aircraft_layouts.{$registration}");
        // NEW: Database
        $aircraft = \App\Models\Aircraft::where('registration', $registration)->first();

        if (!$aircraft) {
            abort(404, 'Aircraft not found');
        }

        // Convert object to array for compatibility if needed, but object is better
        // For now, let's keep array structure or use object direct
        $layout = [
            'type' => $aircraft->type,
            'icon' => $aircraft->icon,
            'layout' => $aircraft->layout,
        ];

        // Get all seats for this registration
        $seats = Seat::where('registration', $registration)
            ->get()
            ->keyBy('seat_id');

        // Determine template from DB layout
        $template = 'aircraft.' . ($aircraft->layout ?? 'show');

        // Get last update time for this aircraft
        $lastUpdate = Seat::where('registration', $registration)->max('updated_at');

        return view($template, [
            'registration' => $registration,
            'layout' => $layout,
            'seats' => $seats,
            'lastUpdate' => $lastUpdate ? \Carbon\Carbon::parse($lastUpdate) : null,
            'aircraft' => $aircraft, // Pass full object too
        ]);
    }

    /**
     * Update seat expiry dates
     */
    public function updateSeats(Request $request, string $registration)
    {
        $request->validate([
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'required|string',
            'expiry_date' => 'required|date',
        ]);

        $seatIds = $request->input('seat_ids');
        $expiryDate = $request->input('expiry_date');

        foreach ($seatIds as $seatId) {
            // Parse row and column from seat_id
            preg_match('/^(\d+)?(.+)$/', $seatId, $matches);
            $row = $matches[1] ?: null;
            $col = $matches[2] ?: $seatId;

            // Determine class type based on layout config
            $classType = 'economy'; // default

            if (in_array($seatId, ['captain', 'copilot', 'observer1', 'observer2'])) {
                $classType = 'cockpit';
            } elseif ($row) {
            } elseif ($row) {
                // Get layout for this aircraft (FROM DB)
                // $aircraftConfig = config("aircraft_layouts.{$registration}");
                $aircraft = \App\Models\Aircraft::where('registration', $registration)->first();
                $layout = $aircraft->layout ?? null;

                if ($layout) {
                    $classRows = config("aircraft_class_rows.{$layout}", []);
                    $rowNum = (int) $row;

                    foreach ($classRows as $class => $rows) {
                        if (in_array($rowNum, $rows)) {
                            $classType = $class;
                            break;
                        }
                    }
                }
            }

            Seat::updateOrCreate(
                [
                    'registration' => $registration,
                    'seat_id' => $seatId,
                ],
                [
                    'row' => $row,
                    'col' => $col,
                    'class_type' => $classType,
                    'expiry_date' => $expiryDate,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => count($seatIds) . ' seat(s) updated',
        ]);
    }
}
