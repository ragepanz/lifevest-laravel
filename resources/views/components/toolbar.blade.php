@props(['registration' => null])

<!-- Toolbar Component -->
<div class="toolbar">
    <div class="toolbar-left">
        <button class="btn btn-primary" id="btnSetDate" disabled>
            📅 Set Date
        </button>
        <div class="divider"></div>
        <button class="btn btn-outline" id="btnClearSelection">
            ✖️ Clear Selection
        </button>
        <div class="divider"></div>
        <span class="selection-info" id="selectionInfo">No seats selected</span>
    </div>
    <div class="toolbar-right">
        <p class="toolbar-hint">💡 Klik nomor baris atau huruf kolom untuk select cepat</p>
        @if($registration && Route::has('reports.pdf'))
            <div class="divider"></div>
            <a href="{{ route('reports.pdf', $registration) }}" target="_blank" class="btn btn-export-pdf">
                Export PDF
            </a>
        @endif
        @if($registration && Route::has('reports.blank'))
            <a href="{{ route('reports.blank', $registration) }}" target="_blank" class="btn btn-export-blank">
                Blank Form
            </a>
        @endif
        @if($registration && Route::has('aircraft.batchInput'))
            <a href="{{ route('aircraft.batchInput', $registration) }}" class="btn btn-batch-input">
                ⚡ Batch Input
            </a>
        @endif
    </div>
</div>