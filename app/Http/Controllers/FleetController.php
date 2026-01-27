<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FleetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fleet = \App\Models\Aircraft::orderBy('registration', 'asc')->get();
        return view('fleet.index', compact('fleet'));
    }

    /**
     * Get available layouts by scanning view files
     */
    private function getLayoutOptions()
    {
        $layouts = [];
        $files = glob(resource_path('views/aircraft/*.blade.php'));

        foreach ($files as $file) {
            $filename = basename($file, '.blade.php');

            // Generate a readable name
            // e.g. "b737-e46" -> "B737 E46"
            $name = strtoupper(str_replace('-', ' ', $filename));

            $layouts[$filename] = $name . ' (' . $filename . ')';
        }

        ksort($layouts);
        return $layouts;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $layoutOptions = $this->getLayoutOptions();
        return view('fleet.create', compact('layoutOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'registration' => 'required|unique:aircraft,registration',
            'type' => 'required',
            'layout' => 'required',
            'status' => 'required|in:active,prolong',
        ]);

        // Force Uppercase
        $data = $request->all();
        $data['registration'] = strtoupper($request->registration);
        $data['type'] = strtoupper($request->type);

        \App\Models\Aircraft::create($data);

        return redirect()->route('fleet.index')->with('success', 'Aircraft added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $aircraft = \App\Models\Aircraft::findOrFail($id);
        $layoutOptions = $this->getLayoutOptions();
        return view('fleet.edit', compact('aircraft', 'layoutOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'type' => 'required',
            'status' => 'required|in:active,prolong',
        ]);

        $aircraft = \App\Models\Aircraft::findOrFail($id);

        // Only allow updating type and status. 
        // Registration and Layout are structural and shouldn't change.
        $aircraft->update($request->only(['type', 'status']));

        return redirect()->route('fleet.index')->with('success', 'Aircraft updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $aircraft = \App\Models\Aircraft::findOrFail($id);
        $aircraft->delete();

        return redirect()->route('fleet.index')->with('success', 'Aircraft deleted successfully.');
    }
}
