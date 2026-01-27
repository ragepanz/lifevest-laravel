@extends('layouts.app')

@section('content')
    <div class="form-container-wide">
        <h2 class="form-header">✈️ Add New Aircraft</h2>

        <form action="{{ route('fleet.store') }}" method="POST" class="form-card">
            @csrf

            <div class="form-group">
                <label class="form-label">Registration (e.g. PK-GPC)</label>
                <input type="text" name="registration" value="{{ old('registration') }}" required placeholder="PK-..."
                    class="form-input" style="text-transform: uppercase; {{ $errors->has('registration') ? 'border-color: #ef4444;' : '' }}">
                @error('registration')
                    <span
                        style="color: #ef4444; font-size: 0.875rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Type (e.g. A330-300)</label>
                <input type="text" name="type" value="{{ old('type') }}" required placeholder="B737-800" class="form-input" style="text-transform: uppercase;">
            </div>

            <div class="form-group">
                <label class="form-label">Layout Template</label>
                <select name="layout" required class="form-select">
                    <option value="" disabled selected>Select Layout...</option>
                    @foreach($layoutOptions as $code => $label)
                        <option value="{{ $code }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active">Active</option>
                    <option value="prolong">Prolong</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Aircraft</button>
                <a href="{{ route('fleet.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection