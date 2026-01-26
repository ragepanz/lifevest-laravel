@extends('layouts.app')

@section('header-right')
    <div class="aircraft-info">
        <label>Tipe:</label>
        <span class="info-value">{{ config('aircraft_layouts.' . $registration . '.type', 'B777-300') }} <span class="status-badge {{ $aircraft['status'] ?? 'active' }}">{{ strtoupper($aircraft['status'] ?? 'active') }}</span></span>
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

    <!-- First Class - Rows 1-2 (A D G K) -->
    <section class="cabin-section">
        <h2>👑 First Class - Rows 1-2</h2>
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
            @foreach([1, 2] as $row)
                <div class="seat-row-business" data-row="{{ $row }}">
                    @php $seat = $seats["{$row}A"] ?? null;
                    $status = $seat?->status ?? 'no-data'; @endphp
                    <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}A" data-row="{{ $row }}" data-col="A">
                        <div class="seat-id">{{ $row }}A</div>
                        <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                            {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                    </div>
                    <div class="aisle-gap"></div>
                    @php $seat = $seats["{$row}D"] ?? null;
                    $status = $seat?->status ?? 'no-data'; @endphp
                    <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}D" data-row="{{ $row }}" data-col="D">
                        <div class="seat-id">{{ $row }}D</div>
                        <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                            {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                    </div>
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @php $seat = $seats["{$row}G"] ?? null;
                    $status = $seat?->status ?? 'no-data'; @endphp
                    <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}G" data-row="{{ $row }}" data-col="G">
                        <div class="seat-id">{{ $row }}G</div>
                        <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                            {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                    </div>
                    <div class="aisle-gap"></div>
                    @php $seat = $seats["{$row}K"] ?? null;
                    $status = $seat?->status ?? 'no-data'; @endphp
                    <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}K" data-row="{{ $row }}" data-col="K">
                        <div class="seat-id">{{ $row }}K</div>
                        <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                            {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Business Class - Rows 6-16 (Staggered) -->
    <section class="cabin-section">
        <h2>💼 Business Class - Rows 6-16</h2>
        <div class="seat-grid grid-business">
            <div class="grid-header-business">
                <span class="col-label">A/C</span>
                <span class="aisle-gap">🚶</span>
                <span class="col-label">D/E</span>
                <span class="col-label row-label">Row</span>
                <span class="col-label">G/F</span>
                <span class="aisle-gap">🚶</span>
                <span class="col-label">K/H</span>
            </div>
            @php
                // PK-GIF Staggered pattern
                // Row 8: A & K only (2 seats)
                // Other rows staggered: AEFK and CDGH
                $staggeredPattern = [
                    6 => ['A', 'E', 'F', 'K'],
                    7 => ['C', 'D', 'G', 'H'],
                    8 => ['A', 'K'],  // Exception: Only 2 seats
                    9 => ['A', 'E', 'F', 'K'],
                    10 => ['C', 'D', 'G', 'H'],
                    11 => ['A', 'E', 'F', 'K'],
                    12 => ['C', 'D', 'G', 'H'],
                    14 => ['A', 'E', 'F', 'K'],
                    15 => ['C', 'D', 'G', 'H'],
                    16 => ['A', 'E', 'F', 'K'],
                ];
            @endphp
            @foreach($staggeredPattern as $row => $cols)
                <div class="seat-row-business" data-row="{{ $row }}">
                    @if(count($cols) == 4)
                        @php $col = $cols[0]; $seat = $seats["{$row}{$col}"] ?? null; $status = $seat?->status ?? 'no-data'; @endphp
                        <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}" data-col="{{ $col }}">
                            <div class="seat-id">{{ $row }}{{ $col }}</div>
                            <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">{{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                        </div>
                        <div class="aisle-gap"></div>
                        @php $col = $cols[1]; $seat = $seats["{$row}{$col}"] ?? null; $status = $seat?->status ?? 'no-data'; @endphp
                        <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}" data-col="{{ $col }}">
                            <div class="seat-id">{{ $row }}{{ $col }}</div>
                            <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">{{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                        </div>
                        <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                        @php $col = $cols[2]; $seat = $seats["{$row}{$col}"] ?? null; $status = $seat?->status ?? 'no-data'; @endphp
                        <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}" data-col="{{ $col }}">
                            <div class="seat-id">{{ $row }}{{ $col }}</div>
                            <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">{{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                        </div>
                        <div class="aisle-gap"></div>
                        @php $col = $cols[3]; $seat = $seats["{$row}{$col}"] ?? null; $status = $seat?->status ?? 'no-data'; @endphp
                        <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}" data-col="{{ $col }}">
                            <div class="seat-id">{{ $row }}{{ $col }}</div>
                            <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">{{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                        </div>
                    @elseif($row == 8)
                        {{-- Special Row 8: A and K only --}}
                        @php $col = $cols[0]; $seat = $seats["{$row}{$col}"] ?? null; $status = $seat?->status ?? 'no-data'; @endphp
                        <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}" data-col="{{ $col }}">
                            <div class="seat-id">{{ $row }}{{ $col }}</div>
                            <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">{{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                        </div>
                        <div class="aisle-gap"></div>
                        <div class="seat-placeholder"></div>
                        <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                        <div class="seat-placeholder"></div>
                        <div class="aisle-gap"></div>
                        @php $col = $cols[1]; $seat = $seats["{$row}{$col}"] ?? null; $status = $seat?->status ?? 'no-data'; @endphp
                        <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}" data-col="{{ $col }}">
                            <div class="seat-id">{{ $row }}{{ $col }}</div>
                            <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">{{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <!-- Economy Class 1 - Rows 21-25 -->
    <!-- 21-23: Full 3-3-3. 24: Skipped. 25: Middle Only -->
    <section class="cabin-section">
        <h2>🪑 Economy Class - Rows 21-25</h2>
        <div class="seat-grid">
            <div class="grid-header grid-row-3-3-3">
                <span class="col-label col-header" data-col="A">A</span>
                <span class="col-label col-header" data-col="B">B</span>
                <span class="col-label col-header" data-col="C">C</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="D">D</span>
                <span class="col-label col-header" data-col="F">F</span>
                <span class="col-label col-header" data-col="G">G</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="H">H</span>
                <span class="col-label col-header" data-col="J">J</span>
                <span class="col-label col-header" data-col="K">K</span>
            </div>
            @php
                $allCols = ['A', 'B', 'C', 'D', 'F', 'G', 'H', 'J', 'K'];
                $exceptions = [
                    24 => [], // Skipped
                    25 => ['D', 'F', 'G'], // Center only
                ];
            @endphp
            @for($row = 21; $row <= 25; $row++)
                @if($row == 24) @continue @endif
                @php $rowCols = $exceptions[$row] ?? $allCols; @endphp
                <div class="seat-row grid-row-3-3-3" data-row="{{ $row }}">
                    @foreach(['A', 'B', 'C'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['D', 'F', 'G'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['H', 'J', 'K'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                </div>
            @endfor
        </div>
    </section>

    <!-- Economy Class 2 - Rows 26-38 -->
    <!-- 26-37: Full 3-3-3. 38: Sides Only -->
    <section class="cabin-section">
        <h2>🪑 Economy Class - Rows 26-38</h2>
        <div class="seat-grid">
            <div class="grid-header grid-row-3-3-3">
                <span class="col-label col-header" data-col="A">A</span>
                <span class="col-label col-header" data-col="B">B</span>
                <span class="col-label col-header" data-col="C">C</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="D">D</span>
                <span class="col-label col-header" data-col="F">F</span>
                <span class="col-label col-header" data-col="G">G</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="H">H</span>
                <span class="col-label col-header" data-col="J">J</span>
                <span class="col-label col-header" data-col="K">K</span>
            </div>
            @php
                $exceptions = [
                    38 => ['A', 'B', 'C', 'H', 'J', 'K'], // No center (only sides)
                ];
            @endphp
            @for($row = 26; $row <= 38; $row++)
                @php $rowCols = $exceptions[$row] ?? $allCols; @endphp
                <div class="seat-row grid-row-3-3-3" data-row="{{ $row }}">
                    @foreach(['A', 'B', 'C'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['D', 'F', 'G'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['H', 'J', 'K'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                </div>
            @endfor
        </div>
    </section>

    <!-- Economy Class 3 - Rows 39-52 -->
    <!-- 39-51: Full 3-3-3. 52: A, C, H, K -->
    <section class="cabin-section">
        <h2>🪑 Economy Class - Rows 39-52</h2>
        <div class="seat-grid">
            <div class="grid-header grid-row-3-3-3">
                <span class="col-label col-header" data-col="A">A</span>
                <span class="col-label col-header" data-col="B">B</span>
                <span class="col-label col-header" data-col="C">C</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="D">D</span>
                <span class="col-label col-header" data-col="F">F</span>
                <span class="col-label col-header" data-col="G">G</span>
                <span class="row-label">Row</span>
                <span class="col-label col-header" data-col="H">H</span>
                <span class="col-label col-header" data-col="J">J</span>
                <span class="col-label col-header" data-col="K">K</span>
            </div>
            @php
                $exceptions = [
                    52 => ['A', 'C', 'D', 'F', 'G', 'H', 'K'],
                ];
            @endphp
            @for($row = 39; $row <= 52; $row++)
                @php $rowCols = $exceptions[$row] ?? $allCols; @endphp
                <div class="seat-row grid-row-3-3-3" data-row="{{ $row }}">
                    @foreach(['A', 'B', 'C'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['D', 'F', 'G'] as $col)
                        @include('components.seat-cell', ['row' => $row, 'col' => $col, 'rowCols' => $rowCols, 'seats' => $seats])
                    @endforeach
                    <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                    @foreach(['H', 'J', 'K'] as $col)
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