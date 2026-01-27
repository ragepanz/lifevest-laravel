@extends('layouts.app')

@section('content')
    <div class="form-container-wide">
        <h2 class="form-header">✏️ Edit Aircraft: {{ $aircraft->registration }}</h2>

        <form action="{{ route('fleet.update', $aircraft->id) }}" method="POST" class="form-card">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Registration (Cannot be changed)</label>
                <input type="text" value="{{ $aircraft->registration }}" disabled class="form-input">
            </div>

            <div class="form-group">
                <div class="form-group">
                    <label class="form-label">Type (Cannot be changed)</label>
                    <input type="text" value="{{ $aircraft->type }}" disabled class="form-input">
                    <input type="hidden" name="type" value="{{ $aircraft->type }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Layout Template (Cannot be changed)</label>
                <input type="text" value="{{ $aircraft->layout }}" disabled class="form-input">
                <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">To change layout, please
                    delete and re-create the aircraft.</small>
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