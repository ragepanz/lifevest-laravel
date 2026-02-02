@extends('layouts.app')

@section('header-right')
    <x-aircraft-header-info :aircraft="$aircraft" :registration="$registration" />
@endsection

@section('content')
    <!-- Toolbar -->
    <x-toolbar />

    <!-- Status Legend -->
    <x-status-legend />
    @include('aircraft.partials.a330-900b')

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