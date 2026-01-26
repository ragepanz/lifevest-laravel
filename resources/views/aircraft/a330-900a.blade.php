@extends('layouts.app')

@section('header-right')
    <div class="aircraft-info">
        <label>Tipe:</label>
        <span class="info-value">{{ config('aircraft_layouts.' . $registration . '.type', 'A330-900') }} <span class="status-badge {{ $aircraft['status'] ?? 'active' }}">{{ strtoupper($aircraft['status'] ?? 'active') }}</span></span>
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

    <!-- Business Class - Rows 6-11 (1-1-1-1 layout: A D G K with aisle gaps) -->
    <section class="cabin-section">
        <h2>💼 Business Class - Rows 6-11</h2>
        <div class="seat-grid grid-business">
            <div class="grid-header-business">
                <span class="col-label">A</span>
                <span class="aisle-gap">🚶</span>
                <span class="col-label">D</span>
                <span class="col-label row-label">Row</span>
                <span class="col-label">G</span>
                <span class="aisle-gap">🚶</span>
                <span class="col-label">K</span>
            </div>
            @foreach([6, 7, 8, 9, 10, 11] as $row)
                <div class="seat-row-business" data-row="{{ $row }}">
                    @php $col = 'A';
                        $seat = $seats["{$row}{$col}"] ?? null;
                    $status = $seat?->status ?? 'no-data'; @endphp
                    <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}"
                        data-col="{{ $col }}">
                        <div class="seat-id">{{ $row }}{{ $col }}</div>
                        <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                            {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                    </div>
                    <div class="aisle-gap"></div>
                    @php $col = 'D';
                        $seat = $seats["{$row}{$col}"] ?? null;
                    $status = $seat?->status ?? 'no-data'; @endphp
                    <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}"
                        data-col="{{ $col }}">
                        <div class="seat-id">{{ $row }}{{ $col }}</div>
                        <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                            {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                    </div>
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @php $col = 'G';
                        $seat = $seats["{$row}{$col}"] ?? null;
                    $status = $seat?->status ?? 'no-data'; @endphp
                    <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}"
                        data-col="{{ $col }}">
                        <div class="seat-id">{{ $row }}{{ $col }}</div>
                        <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                            {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                    </div>
                    <div class="aisle-gap"></div>
                    @php $col = 'K';
                        $seat = $seats["{$row}{$col}"] ?? null;
                    $status = $seat?->status ?? 'no-data'; @endphp
                    <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}"
                        data-col="{{ $col }}">
                        <div class="seat-id">{{ $row }}{{ $col }}</div>
                        <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                            {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Economy Class 1 - Rows 21-40 (2-4-2 layout: A C - Row - D E F G - Row - H K) -->
    <section class="cabin-section">
        <h2>🪑 Economy Class - Rows 21-40</h2>
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
                $skipRows = [24];
                $exceptions = [
                    40 => ['A', 'C', 'H', 'K'],
                ];
            @endphp
            @for($row = 21; $row <= 40; $row++)
                @if(in_array($row, $skipRows))
                    @continue
                @endif
                @php
                    $rowCols = $exceptions[$row] ?? ['A', 'C', 'D', 'E', 'F', 'G', 'H', 'K'];
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
            @endfor
        </div>
    </section>

    <!-- Economy Class 2 - Rows 41-58 -->
    <section class="cabin-section">
        <h2>🪑 Economy Class - Rows 41-58</h2>
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
                $exceptions = [
                    41 => ['D', 'E', 'F', 'G'],
                    54 => ['A', 'C', 'D', 'F', 'G', 'H', 'K'],
                    55 => ['A', 'C', 'D', 'F', 'G', 'H', 'K'],
                    56 => ['A', 'C', 'D', 'F', 'G', 'H', 'K'],
                    57 => ['A', 'C', 'D', 'F', 'G'],
                    58 => ['D', 'F', 'G'],
                ];
            @endphp
            @for($row = 41; $row <= 58; $row++)
                @php
                    $rowCols = $exceptions[$row] ?? ['A', 'C', 'D', 'E', 'F', 'G', 'H', 'K'];
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