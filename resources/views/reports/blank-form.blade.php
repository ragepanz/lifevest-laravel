<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Blank Form {{ $registration }}</title>
    <style>
        /* PDF Specific Styles - Blank Form Version */
        @page {
            margin: 15px;
        }

        body {
            font-family: sans-serif;
            font-size: 10pt;
            color: #333;
        }

        .page-break {
            page-break-after: always;
        }

        /* Header */
        .report-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .airline-title {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-meta {
            font-size: 10pt;
            color: #555;
        }

        .form-note {
            font-size: 9pt;
            font-style: italic;
            margin-top: 5px;
            color: #666;
        }

        /* Sections */
        .cabin-section,
        .cockpit-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .avoid-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        h2 {
            font-size: 11pt;
            margin-bottom: 8px;
            background-color: #e0e0e0;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        /* Grid Layout */
        .seat-grid,
        .cockpit-grid,
        .spare-grid {
            width: 100%;
            text-align: center;
        }

        .seat-row,
        .grid-header {
            display: block;
            margin-bottom: 4px;
            white-space: nowrap;
            page-break-inside: avoid;
            line-height: 1;
        }

        /* BLANK FORM: Larger Seat Cards for Handwriting */
        .seat-card {
            width: 55px;
            /* Fit to page width */
            height: 40px;
            /* Landscape aspect ratio */
            border: 2px solid #333;
            /* Thicker border */
            background: #fff;
            display: inline-block;
            margin: 1px 2px;
            text-align: center;
            vertical-align: top;
            padding: 2px 2px;
            border-radius: 4px;
            box-sizing: border-box;
            overflow: hidden;
            white-space: nowrap;
        }

        /* Cockpit seats - also larger */
        .cockpit-seat {
            width: 80px;
            height: 45px;
        }

        .seat-label {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        /* All seats show as blank (no status color) */
        .seat-card {
            background-color: #fff !important;
            border-color: #333 !important;
        }

        /* Typography inside seat */
        .seat-id {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 0;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 2px;
        }

        /* Hide dates - show blank space for writing */
        .seat-date {
            font-size: 7px;
            color: #bbb;
            min-height: 14px;
            /* Space for handwriting - compact landscape */
        }

        /* Headers Columns (A, B, C...) */
        .col-header {
            width: 55px;
            display: inline-block;
            font-weight: bold;
            font-size: 8px;
            text-align: center;
            margin: 1px 2px;
        }

        /* Row Number */
        .row-number {
            width: 25px;
            display: inline-block;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            line-height: 40px;
            font-size: 8px;
        }

        .row-label {
            width: 25px;
            display: inline-block;
            font-size: 7px;
        }

        /* Spacers */
        .seat-placeholder {
            width: 59px;
            /* 55px + 4px margin */
            display: inline-block;
        }

        .aisle-gap {
            width: 5px;
            display: inline-block;
        }

        /* Hide original spare section from partial (we render our own) */
        .spare-grid {
            display: none !important;
        }

        .spare-grid+button,
        .empty-message {
            display: none !important;
        }

        /* Hide the partial's Spare header (last cabin-section) */
        .cabin-section:last-of-type {
            display: none !important;
        }

        /* Custom Blank Form Spare Boxes */
        .blank-spare-section {
            margin-top: 25px;
            text-align: center;
            page-break-before: auto;
        }

        .blank-spare-section h2 {
            font-size: 12pt;
            margin-bottom: 12px;
        }

        .blank-spare-grid {
            width: 85%;
            margin: 15px auto;
            border: 2px dashed #555;
            padding: 15px 10px;
            text-align: left;
            page-break-inside: avoid;
        }

        .blank-spare-grid h3 {
            font-size: 11pt;
            margin: 0 0 10px 0;
            background-color: #e0e0e0;
            padding: 4px 12px;
            border-radius: 4px;
            display: block;
            text-align: center;
        }

        .blank-spare-box {
            width: 55px;
            height: 42px;
            border: 1.5px solid #555;
            background: #fff;
            display: inline-block;
            margin: 3px 2px;
            text-align: center;
            vertical-align: top;
            padding: 2px;
            border-radius: 3px;
            box-sizing: border-box;
        }

        .blank-spare-box .box-num {
            font-size: 8px;
            font-weight: bold;
            color: #555;
            border-bottom: 1px dotted #bbb;
            padding-bottom: 1px;
            margin-bottom: 1px;
        }

        .blank-spare-box .box-date {
            min-height: 18px;
            /* Space for handwriting */
        }

        /* Hide interactive elements */
        button,
        .btn,
        .btn-delete-spare,
        .btn-add-spare {
            display: none !important;
        }

        /* Signature Box */
        .signature-box {
            margin-top: 30px;
            border: 1px solid #333;
            padding: 15px;
            text-align: left;
        }

        .signature-row {
            display: block;
            margin-bottom: 20px;
        }

        .signature-label {
            font-weight: bold;
            font-size: 10pt;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            display: inline-block;
            width: 200px;
            margin-left: 10px;
        }

        /* PDF FIX: Attendant section flex layout fix */
        .cabin-section>div[style]>div[style] {
            display: inline-block;
            vertical-align: top;
            margin: 0 15px;
        }

        .cabin-section>div[style]>div[style]>div[style]>.seat-card {
            display: inline-block;
            margin: 0 3px;
        }
    </style>
</head>

<body>
    <div class="report-header">
        <div class="airline-title">
            @if($aircraft->airline_id == 1)
                GARUDA INDONESIA
            @elseif($aircraft->airline_id == 2)
                CITILINK
            @else
                Life Vest Tracker
            @endif
        </div>
        <div class="report-meta">
            <strong>BLANK FORM - LIFE VEST INSPECTION</strong><br>
            Registration: <strong>{{ $registration }}</strong> &nbsp;|&nbsp;
            Type: <strong>{{ $aircraft->type }}</strong> &nbsp;|&nbsp;
            Date: _______________
        </div>
        <div class="form-note">
            Tulis tanggal expired pada setiap kotak seat (format: DD/MM/YYYY)
        </div>
    </div>

    <div class="content">
        @if(View::exists('aircraft.partials.' . $aircraft->layout))
            @include('aircraft.partials.' . $aircraft->layout)
        @else
            <div style="text-align: center; padding: 50px; color: red;">
                Layout '{{ $aircraft->layout }}' not found.
            </div>
        @endif

        <!-- Blank Form: Numbered Spare Boxes -->
        <section class="blank-spare-section">

            <div class="blank-spare-grid">
                <h3>PAX</h3>
                @for($i = 1; $i <= $maxPax; $i++)
                    <div class="blank-spare-box">
                        <div class="box-num">{{ $i }}</div>
                        <div class="box-date"></div>
                    </div>
                @endfor
            </div>

            <div class="blank-spare-grid">
                <h3>INF</h3>
                @for($i = 1; $i <= $maxInf; $i++)
                    <div class="blank-spare-box">
                        <div class="box-num">{{ $i }}</div>
                        <div class="box-date"></div>
                    </div>
                @endfor
            </div>
        </section>
    </div>

    <!-- Signature Box -->
    <div class="signature-box">
        <div class="signature-row">
            <span class="signature-label">APPROVED BY:</span>
            <span class="signature-line"></span>
            &nbsp;&nbsp;&nbsp;
            <span class="signature-label">Date:</span>
            <span class="signature-line" style="width: 120px;"></span>
        </div>
        <div class="signature-row">
            <span class="signature-label">CHECKED BY:</span>
            <span class="signature-line"></span>
            &nbsp;&nbsp;&nbsp;
            <span class="signature-label">Date:</span>
            <span class="signature-line" style="width: 120px;"></span>
        </div>
    </div>

    <div
        style="position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8pt; color: #999; border-top: 1px solid #eee; padding-top: 5px;">
        Life Vest Tracker - Blank Inspection Form
    </div>
</body>

</html>