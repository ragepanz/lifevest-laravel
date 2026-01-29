@extends('layouts.app')

@section('content')
    <div class="form-container-wide">
        <h2 class="form-header">✈️ Edit Aircraft: {{ $aircraft->registration }}</h2>

        <form action="{{ route('fleet.update', $aircraft->id) }}" method="POST" class="form-card">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Airline</label>
                <select name="airline_id_disabled" disabled class="form-select"
                    style="background-color: var(--bg-tertiary) !important; color: var(--text-secondary); cursor: not-allowed; opacity: 1;">
                    @foreach($airlines as $airline)
                        <option value="{{ $airline->id }}" {{ old('airline_id', $aircraft->airline_id) == $airline->id ? 'selected' : '' }}>
                            {{ $airline->name }} ({{ $airline->code }})
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="airline_id" value="{{ $aircraft->airline_id }}">
                <small style="color: var(--text-secondary); font-size: 0.75rem; display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                    🔒 Airline cannot be changed
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Registration</label>
                <input type="text" value="{{ $aircraft->registration }}" disabled class="form-input"
                    style="background-color: var(--bg-tertiary) !important; color: var(--text-secondary); cursor: not-allowed; opacity: 1;">
                <small style="color: var(--text-secondary); font-size: 0.75rem; display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                    🔒 Registration cannot be changed
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Type (e.g. A330-300)</label>
                <input type="text" name="type" value="{{ old('type', $aircraft->type) }}" readonly class="form-input"
                    style="text-transform: uppercase; background-color: var(--bg-tertiary) !important; color: var(--text-secondary); cursor: not-allowed; opacity: 1;">
                <small style="color: var(--text-secondary); font-size: 0.75rem; display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                    🔒 Type cannot be changed
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Layout Template</label>
                <input type="text" value="{{ $aircraft->layout }}" disabled class="form-input"
                    style="background-color: var(--bg-tertiary) !important; color: var(--text-secondary); cursor: not-allowed; opacity: 1;">
                <small style="color: var(--text-secondary); font-size: 0.75rem; display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                    🔒 Layout cannot be changed
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ $aircraft->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="prolong" {{ $aircraft->status == 'prolong' ? 'selected' : '' }}>Prolong</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Aircraft</button>
                <a href="{{ route('fleet.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection