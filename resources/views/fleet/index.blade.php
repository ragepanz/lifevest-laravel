@extends('layouts.app')

@section('content')
    <div class="header-section">
        <h2 class="form-header" style="text-align: left; margin:0;">⚙️ Fleet Manager</h2>
        <a href="{{ route('fleet.create') }}" class="btn btn-primary">
            + Add New Aircraft
        </a>
    </div>

    @if(session('success'))
        <div class="alert-box alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Client-Side Search & Sort -->
    <div class="filter-bar">
        <div class="filter-inputs">
            <input type="text" id="fleetSearch" placeholder="Search Registration or Type..." class="form-input">
            <select id="fleetSort" class="form-select" style="cursor: pointer;">
                <option value="registration_asc">Sort by Reg (A-Z)</option>
                <option value="registration_desc">Sort by Reg (Z-A)</option>
                <option value="type_asc">Sort by Type (A-Z)</option>
                <option value="type_desc">Sort by Type (Z-A)</option>
                <option value="status_active">Stat: Active First</option>
                <option value="status_prolong">Stat: Prolong First</option>
            </select>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <!-- No buttons needed for instant search -->
        </div>
    </div>

    <div class="fleet-table-wrapper">
        <table class="fleet-table">
            <thead>
                <tr>
                    <th class="fleet-th" style="width: 50px;">#</th>
                    <th class="fleet-th">Registration</th>
                    <th class="fleet-th">Type</th>
                    <th class="fleet-th">Layout Code</th>
                    <th class="fleet-th">Status</th>
                    <th class="fleet-th text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fleet as $aircraft)
                    <tr>
                        <td class="fleet-td text-muted">{{ $loop->iteration }}</td>
                        <td class="fleet-td font-bold">{{ $aircraft->registration }}</td>
                        <td class="fleet-td">{{ $aircraft->type }}</td>
                        <td class="fleet-td font-mono">{{ $aircraft->layout }}</td>
                        <td class="fleet-td">
                            <span class="status-badge {{ $aircraft->status }}">
                                {{ strtoupper($aircraft->status) }}
                            </span>
                        </td>
                        <td class="fleet-td text-right">
                            <a href="{{ route('fleet.edit', $aircraft->id) }}" class="btn btn-sm btn-secondary"
                                style="display:inline-flex; height:32px; padding: 0 12px; margin-right: 0.5rem;">Edit</a>
                            <form action="{{ route('fleet.destroy', $aircraft->id) }}" method="POST"
                                style="display: inline-block;"
                                onsubmit="return confirm('Are you sure you want to delete {{ $aircraft->registration }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    style="background: var(--danger); color: white; height:32px; padding: 0 12px;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection