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

    <!-- Business Class - Rows 6-12 (Staggered: even=C,E,F,H odd=A,D,G,K, row 12 only E,F) -->
    <section class="cabin-section">
        <h2>💼 Business Class - Rows 6-12</h2>
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
                // PK-GIA Staggered pattern
                $staggeredPattern = [
                    6 => ['C', 'E', 'F', 'H'],
                    7 => ['A', 'D', 'G', 'K'],
                    8 => ['C', 'E', 'F', 'H'],
                    9 => ['A', 'D', 'G', 'K'],
                    10 => ['C', 'E', 'F', 'H'],
                    11 => ['A', 'D', 'G', 'K'],
                    12 => ['E', 'F'], // Only center 2 seats
                ];
            @endphp
            @foreach($staggeredPattern as $row => $cols)
                <div class="seat-row-business" data-row="{{ $row }}">
                    @if(count($cols) == 4)
                        {{-- Full row with 4 seats --}}
                        @php $col = $cols[0];
                            $seat = $seats["{$row}{$col}"] ?? null;
                        $status = $seat?->status ?? 'no-data'; @endphp
                        <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}"
                            data-col="{{ $col }}">
                            <div class="seat-id">{{ $row }}{{ $col }}</div>
                            <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                                {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                        </div>
                        <div class="aisle-gap"></div>
                        @php $col = $cols[1];
                            $seat = $seats["{$row}{$col}"] ?? null;
                        $status = $seat?->status ?? 'no-data'; @endphp
                        <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}"
                            data-col="{{ $col }}">
                            <div class="seat-id">{{ $row }}{{ $col }}</div>
                            <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                                {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                        </div>
                        <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                        @php $col = $cols[2];
                            $seat = $seats["{$row}{$col}"] ?? null;
                        $status = $seat?->status ?? 'no-data'; @endphp
                        <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}"
                            data-col="{{ $col }}">
                            <div class="seat-id">{{ $row }}{{ $col }}</div>
                            <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                                {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                        </div>
                        <div class="aisle-gap"></div>
                        @php $col = $cols[3];
                            $seat = $seats["{$row}{$col}"] ?? null;
                        $status = $seat?->status ?? 'no-data'; @endphp
                        <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}"
                            data-col="{{ $col }}">
                            <div class="seat-id">{{ $row }}{{ $col }}</div>
                            <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                                {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                        </div>
                    @else
                        {{-- Row 12: Only center 2 seats (E, F) --}}
                        <div class="seat-placeholder"></div>
                        <div class="aisle-gap"></div>
                        @php $col = $cols[0];
                            $seat = $seats["{$row}{$col}"] ?? null;
                        $status = $seat?->status ?? 'no-data'; @endphp
                        <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}"
                            data-col="{{ $col }}">
                            <div class="seat-id">{{ $row }}{{ $col }}</div>
                            <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                                {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                        </div>
                        <div class="row-number" data-row="{{ $row }}">{{ $row }}</div>
                        @php $col = $cols[1];
                            $seat = $seats["{$row}{$col}"] ?? null;
                        $status = $seat?->status ?? 'no-data'; @endphp
                        <div class="seat-card status-{{ $status }}" data-seat="{{ $row }}{{ $col }}" data-row="{{ $row }}"
                            data-col="{{ $col }}">
                            <div class="seat-id">{{ $row }}{{ $col }}</div>
                            <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
                                {{ $seat?->expiry_date?->format('j M Y') ?? '-' }}</div>
                        </div>
                        <div class="aisle-gap"></div>
                        <div class="seat-placeholder"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <!-- Economy Class 1 - Rows 21-36 (3-3-3 layout: ABC - DFG - HJK) -->
    <section class="cabin-section">
        <h2>🪑 Economy Class - Rows 21-36</h2>
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
                    36 => ['D', 'F', 'G'], // Only center
                ];
            @endphp
            @for($row = 21; $row <= 36; $row++)
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

    <!-- Economy Class 2 - Rows 37-49 -->
    <section class="cabin-section">
        <h2>🪑 Economy Class - Rows 37-49</h2>
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
                    49 => ['A', 'B', 'C', 'H', 'J', 'K'], // No center section (D,F,G)
                ];
            @endphp
            @for($row = 37; $row <= 49; $row++)
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

    <!-- Economy Class 3 - Rows 50-63 (row 63 has exceptions) -->
    <section class="cabin-section">
        <h2>🪑 Economy Class - Rows 50-63</h2>
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
                    63 => ['A', 'B', 'D', 'F', 'G', 'J', 'K'], // No C, no H (as interpreted from screenshot)
                    // Wait, user said "ABC - DFG - HJK why is there E??"
                    // and for row 63 exceptions previously I put: A B | D E F G | J K (no C, no H)
                    // So for 3-3-3 it becomes: A B | D F G | J K
                ];
            @endphp
            @for($row = 50; $row <= 63; $row++)
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