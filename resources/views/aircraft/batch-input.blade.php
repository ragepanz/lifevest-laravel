@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8" style="max-width: 1600px;">
        <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold mb-2">⚡ Batch Input: {{ $aircraft->type }}</h1>
                <p class="text-gray-400">
                    Registration: <span class="font-bold text-white">{{ $registration }}</span> |
                    Layout: <span class="font-bold text-white">{{ $aircraft->layout }}</span>
                </p>
            </div>
            <a href="{{ route('aircraft.show', $registration) }}"
                class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500 transition">
                &larr; Back to Seat Map
            </a>
        </div>

        <div
            style="background: rgba(59, 130, 246, 0.1); border-left: 4px solid #3b82f6; padding: 1rem; margin-bottom: 2rem; border-radius: 0.5rem;">
            <p style="color: #93c5fd; font-size: 0.9rem;">
                <strong>📋 Cara Pakai:</strong> Copy kolom tanggal dari Excel, paste ke kolom yang sesuai.
                <br>
                Format: <strong>Oct-25</strong>, <strong>24-Jan-25</strong>, atau <strong>01/03/2030</strong>
            </p>
        </div>

        <form action="{{ route('aircraft.storeBatchInput', $registration) }}" method="POST">
            @csrf

            {{-- Economy Sections --}}
            @foreach($sections as $sectionIndex => $section)
                <div
                    style="background: var(--bg-card); padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 2rem; border: 1px solid var(--border);">
                    <h2 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem; color: var(--text-primary);">
                        🪑 {{ $section['name'] }}
                    </h2>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                        Rows:
                        {{ implode(', ', array_slice($section['rows'], 0, 5)) }}{{ count($section['rows']) > 5 ? '...' . end($section['rows']) : '' }}
                        ({{ count($section['rows']) }} rows)
                    </p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 0.75rem;">
                        @foreach($section['columns'] as $col)
                            <div
                                style="background: var(--bg-hover); padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border);">
                                <label for="section_{{ $sectionIndex }}_col_{{ $col }}"
                                    style="display: block; font-size: 1rem; font-weight: bold; margin-bottom: 0.25rem; text-align: center; color: var(--text-primary);">
                                    {{ $col }}
                                </label>
                                <div
                                    style="font-size: 0.7rem; color: var(--text-muted); text-align: center; margin-bottom: 0.5rem;">
                                    {{ count($section['rows']) }} rows
                                </div>
                                <textarea name="section_{{ $sectionIndex }}_col_{{ $col }}"
                                    id="section_{{ $sectionIndex }}_col_{{ $col }}" rows="{{ min(count($section['rows']), 15) }}"
                                    style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.25rem; font-family: monospace; font-size: 0.8rem; background: var(--bg-input); color: var(--text-primary); resize: vertical;"
                                    placeholder="Oct-25&#10;Jan-34&#10;..."></textarea>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Spare Section --}}
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                {{-- PAX Spare --}}
                <div
                    style="background: var(--bg-card); padding: 1.5rem; border-radius: 0.75rem; border: 1px solid var(--border);">
                    <h2 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem; color: var(--text-primary);">
                        🧑 Adult Life Vest Spare (PAX)
                    </h2>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                        Paste tanggal saja, jumlah otomatis dihitung
                    </p>
                    <textarea name="pax_dates" id="pax_dates" rows="10"
                        style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.25rem; font-family: monospace; font-size: 0.85rem; background: var(--bg-input); color: var(--text-primary);"
                        placeholder="Oct-25&#10;Jan-34&#10;Feb-33&#10;..."></textarea>
                </div>

                {{-- INF Spare --}}
                <div
                    style="background: var(--bg-card); padding: 1.5rem; border-radius: 0.75rem; border: 1px solid var(--border);">
                    <h2 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem; color: var(--text-primary);">
                        👶 Infant Life Vest (INF)
                    </h2>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                        Paste tanggal saja, jumlah otomatis dihitung
                    </p>
                    <textarea name="inf_dates" id="inf_dates" rows="10"
                        style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.25rem; font-family: monospace; font-size: 0.85rem; background: var(--bg-input); color: var(--text-primary);"
                        placeholder="Oct-25&#10;Jan-34&#10;Feb-33&#10;..."></textarea>
                </div>
            </div>

            {{-- Submit --}}
            <div
                style="display: flex; justify-content: flex-end; gap: 1rem; position: sticky; bottom: 1rem; background: var(--bg-main); padding: 1rem; border-radius: 0.5rem; box-shadow: 0 -2px 10px rgba(0,0,0,0.3);">
                <a href="{{ route('aircraft.show', $registration) }}"
                    style="padding: 0.75rem 1.5rem; background: var(--text-muted); color: white; border-radius: 0.5rem; text-decoration: none;">
                    Cancel
                </a>
                <button type="submit"
                    style="padding: 0.75rem 1.5rem; background: #22c55e; color: white; font-weight: bold; border-radius: 0.5rem; border: none; cursor: pointer;">
                    💾 Save Batch Data
                </button>
            </div>
        </form>
    </div>
@endsection