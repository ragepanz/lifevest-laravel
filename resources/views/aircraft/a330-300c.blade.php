@extends('layouts.app')

@section('header-right')
    <div class="aircraft-info">
        <label>Tipe:</label>
        <span class="info-value">{{ config('aircraft_layouts.' . $registration . '.type', 'A330-300') }} <span class="status-badge {{ $aircraft['status'] ?? 'active' }}">{{ strtoupper($aircraft['status'] ?? 'active') }}</span></span>
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

    <!-- Economy Class - Rows 21-34 (skip row 24) -->
    <section class="cabin-section">
        <h2>🪑 Economy Class - Rows 21-34</h2>
        <div class="seat-grid">
            <div class="grid-header grid-row-2-4-2">
                <span class="col-label col-header" data-col="A">A</span>
                <span class="col-label col-header" data-col="C">C</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="D">D</span>
                <span class="col-label col-header" data-col="E">E</span>
                <span class="col-label col-header" data-col="F">F</span>
                <span class="col-label col-header" data-col="G">G</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="H">H</span>
                <span class="col-label col-header" data-col="K">K</span>
            </div>
            @php
                $exceptions1 = [
                    33 => ['A', 'C', 'D', 'E', 'F', 'G'],
                    34 => ['D', 'E', 'F', 'G'],
                ];
            @endphp
            @foreach(range(21, 34) as $row)
                @if($row == 24)
                    @continue
                @endif
                @php
                    $rowCols = $exceptions1[$row] ?? ['A', 'C', 'D', 'E', 'F', 'G', 'H', 'K'];
                @endphp
                <div class="seat-row grid-row-2-4-2" data-row="{{ $row }}">
                    @foreach(['A', 'C'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['D', 'E', 'F', 'G'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['H', 'K'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>

    <!-- Economy Class - Rows 35-54 -->
    <section class="cabin-section">
        <h2>🪑 Economy Class - Rows 35-54</h2>
        <div class="seat-grid">
            <div class="grid-header grid-row-2-4-2">
                <span class="col-label col-header" data-col="A">A</span>
                <span class="col-label col-header" data-col="C">C</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="D">D</span>
                <span class="col-label col-header" data-col="E">E</span>
                <span class="col-label col-header" data-col="F">F</span>
                <span class="col-label col-header" data-col="G">G</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="H">H</span>
                <span class="col-label col-header" data-col="K">K</span>
            </div>
            @php
                $exceptions2 = [
                    35 => ['H', 'K'],
                    36 => ['A', 'C', 'H', 'K'],
                ];
            @endphp
            @foreach(range(35, 54) as $row)
                @php
                    $rowCols = $exceptions2[$row] ?? ['A', 'C', 'D', 'E', 'F', 'G', 'H', 'K'];
                @endphp
                <div class="seat-row grid-row-2-4-2" data-row="{{ $row }}">
                    @foreach(['A', 'C'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['D', 'E', 'F', 'G'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['H', 'K'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>

    <!-- Economy Class - Rows 55-70 -->
    <section class="cabin-section">
        <h2>🪑 Economy Class - Rows 55-70</h2>
        <div class="seat-grid">
            <div class="grid-header grid-row-2-4-2">
                <span class="col-label col-header" data-col="A">A</span>
                <span class="col-label col-header" data-col="C">C</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="D">D</span>
                <span class="col-label col-header" data-col="E">E</span>
                <span class="col-label col-header" data-col="F">F</span>
                <span class="col-label col-header" data-col="G">G</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="H">H</span>
                <span class="col-label col-header" data-col="K">K</span>
            </div>
            @php
                $exceptions3 = [
                    55 => ['A', 'C', 'H', 'K'],
                    56 => ['A', 'C', 'H', 'K'],
                    66 => ['A', 'C', 'D', 'F', 'G', 'H', 'K'],
                    67 => ['A', 'C', 'D', 'F', 'G', 'H', 'K'],
                    68 => ['A', 'C', 'D', 'F', 'G', 'H', 'K'],
                    69 => ['A', 'C', 'D', 'F', 'G', 'H', 'K'],
                    70 => ['D', 'F', 'G', 'H', 'K'],
                ];
            @endphp
            @foreach(range(55, 70) as $row)
                @php
                    $rowCols = $exceptions3[$row] ?? ['A', 'C', 'D', 'E', 'F', 'G', 'H', 'K'];
                @endphp
                <div class="seat-row grid-row-2-4-2" data-row="{{ $row }}">
                    @foreach(['A', 'C'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['D', 'E', 'F', 'G'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['H', 'K'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                </div>
            @endforeach
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