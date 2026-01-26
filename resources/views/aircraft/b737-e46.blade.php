@extends('layouts.app')

@section('header-right')
    <div class="aircraft-info">
        <label>Tipe:</label>
        <span class="info-value">
            {{ config('aircraft_layouts.' . $registration . '.type', 'B737-800') }}
            <span class="status-badge {{ $aircraft['status'] ?? 'active' }}">
                {{ strtoupper($aircraft['status'] ?? 'active') }}
            </span>
        </span>
    </div>
    <div class="aircraft-info">
        <label>Registrasi:</label>
        <span class="info-value">{{ $registration }}</span>
    </div>
@endsection

@section('content')
    <!-- Toolbar -->
    <x-toolbar />

    <!-- Status Legend -->
    <x-status-legend />

    <!-- Cockpit Section -->
    <x-cockpit-section :seats="$seats" />

    <!-- Business Class - Rows 6-8 (2-2 layout: A C - Row - H K) -->
    <section class="cabin-section">
        <h2>💼 Business Class - Rows 6-8</h2>
        <div class="seat-grid">
            <div class="grid-header grid-row-2-2">
                <span class="col-label col-header" data-col="A">A</span>
                <span class="col-label col-header" data-col="C">C</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="H">H</span>
                <span class="col-label col-header" data-col="K">K</span>
            </div>
            @foreach([6, 7, 8] as $row)
                <div class="seat-row grid-row-2-2" data-row="{{ $row }}">
                    @foreach(['A', 'C'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => ['A', 'C', 'H', 'K'], 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['H', 'K'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => ['A', 'C', 'H', 'K'], 'seats' => $seats])
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>

    <!-- Economy Class - Rows 21-46 (3-3 layout: A B C - Row - H J K) -->
    <section class="cabin-section">
        <h2>🪑 Economy Class - Rows 21-46</h2>
        <div class="seat-grid">
            <div class="grid-header grid-row-3-3">
                <span class="col-label col-header" data-col="A">A</span>
                <span class="col-label col-header" data-col="B">B</span>
                <span class="col-label col-header" data-col="C">C</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="H">H</span>
                <span class="col-label col-header" data-col="J">J</span>
                <span class="col-label col-header" data-col="K">K</span>
            </div>
            @for($row = 21; $row <= 46; $row++)
                @if($row == 24)
                    @continue
                @endif
                <div class="seat-row grid-row-3-3" data-row="{{ $row }}">
                    @foreach(['A', 'B', 'C'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => ['A', 'B', 'C', 'H', 'J', 'K'], 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['H', 'J', 'K'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => ['A', 'B', 'C', 'H', 'J', 'K'], 'seats' => $seats])
                    @endforeach
                </div>
            @endfor
        </div>
    </section>

    <!-- Date Modal -->
    @include('components.date-modal')
@endsection

@push('scripts')
    <script>
        window.AIRCRAFT_CONFIG = {
            registration: '{{ $registration }}',
            updateUrl: '{{ route('aircraft.updateSeats', $registration) }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
@endpush