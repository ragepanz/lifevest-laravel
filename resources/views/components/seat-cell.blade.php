@php
    $seatId = $row . $col;
    $hasSeat = in_array($col, $rowCols);
    $seat = $seats[$seatId] ?? null;
    $status = $seat?->status ?? 'no-data';
    $dateFormat = isset($isPdfExport) && $isPdfExport ? 'd M Y' : 'j M Y';
    $expiryDate = $seat?->expiry_date?->format($dateFormat) ?? '-';
@endphp

@if($hasSeat)
    <div class="seat-card status-{{ $status }}" data-seat="{{ $seatId }}" data-row="{{ $row }}" data-col="{{ $col }}"
        data-status="{{ $status }}">
        <div class="seat-id">{{ $seatId }}</div>
        <div class="seat-date" data-date="{{ $seat?->expiry_date?->format('Y-m-d') ?? '' }}">
            {{ $expiryDate }}
        </div>
    </div>
@else
    <div class="seat-placeholder"></div>
@endif