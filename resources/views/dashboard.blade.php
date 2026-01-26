@extends('layouts.app')

@section('content')
    <!-- Summary Section -->
    <section class="summary-section">
        <h2>📈 Fleet Overview</h2>
        <div class="summary-cards">
            <div class="summary-card safe">
                <div class="summary-icon">🟢</div>
                <div class="summary-value">{{ $totalStats['safe'] }}</div>
                <div class="summary-label">Safe</div>
                <div class="summary-desc">> 6 months</div>
            </div>
            <div class="summary-card warning">
                <div class="summary-icon">🟡</div>
                <div class="summary-value">{{ $totalStats['warning'] }}</div>
                <div class="summary-label">Warning</div>
                <div class="summary-desc">3-6 months</div>
            </div>
            <div class="summary-card critical">
                <div class="summary-icon">🔴</div>
                <div class="summary-value">{{ $totalStats['critical'] }}</div>
                <div class="summary-label">Critical</div>
                <div class="summary-desc">
                    < 3 months</div>
                </div>
                <div class="summary-card expired">
                    <div class="summary-icon">🟣</div>
                    <div class="summary-value">{{ $totalStats['expired'] }}</div>
                    <div class="summary-label">Expired</div>
                    <div class="summary-desc">Past due</div>
                </div>

            </div>
    </section>

    <!-- Fleet Cards Section - Grouped by Type -->
    @foreach($fleetByType as $baseType => $typeGroup)
        <section class="fleet-section">
            <h2>{{ $typeGroup['icon'] }} {{ $typeGroup['name'] }}</h2>
            <div class="fleet-cards">
                @foreach($typeGroup['aircraft'] as $registration => $aircraft)
                    <a href="{{ route('aircraft.show', $registration) }}"
                        class="fleet-card {{ $aircraft['health'] >= 70 ? 'healthy' : ($aircraft['health'] >= 40 ? 'warning' : 'critical') }}"
                        data-status="{{ $aircraft['status'] ?? 'active' }}">
                        <div class="fleet-card-header">
                            <div>
                                <div class="fleet-card-type">
                                    {{ $aircraft['type'] }}
                                    <span class="status-badge {{ $aircraft['status'] ?? 'active' }}">
                                        {{ strtoupper($aircraft['status'] ?? 'active') }}
                                    </span>
                                </div>
                                <div class="fleet-card-reg">{{ $registration }}</div>
                            </div>
                            <div class="fleet-card-icon">{{ $aircraft['icon'] }}</div>
                        </div>
                        <div class="fleet-card-stats">
                            <div class="fleet-stat safe">
                                <div class="fleet-stat-value">{{ $aircraft['stats']['safe'] }}</div>
                                <div class="fleet-stat-label">Safe</div>
                            </div>
                            <div class="fleet-stat warning">
                                <div class="fleet-stat-value">{{ $aircraft['stats']['warning'] }}</div>
                                <div class="fleet-stat-label">Warning</div>
                            </div>
                            <div class="fleet-stat critical">
                                <div class="fleet-stat-value">{{ $aircraft['stats']['critical'] }}</div>
                                <div class="fleet-stat-label">Critical</div>
                            </div>
                            <div class="fleet-stat expired">
                                <div class="fleet-stat-value">{{ $aircraft['stats']['expired'] }}</div>
                                <div class="fleet-stat-label">Expired</div>
                            </div>
                        </div>
                        <div class="fleet-card-progress">
                            @php
                                $total = array_sum($aircraft['stats']) ?: 1;
                            @endphp
                            <div class="progress-bar">
                                <div class="progress-segment safe"
                                    style="width: {{ ($aircraft['stats']['safe'] / $total) * 100 }}%"></div>
                                <div class="progress-segment warning"
                                    style="width: {{ ($aircraft['stats']['warning'] / $total) * 100 }}%"></div>
                                <div class="progress-segment critical"
                                    style="width: {{ ($aircraft['stats']['critical'] / $total) * 100 }}%"></div>
                                <div class="progress-segment expired"
                                    style="width: {{ ($aircraft['stats']['expired'] / $total) * 100 }}%"></div>
                                <div class="progress-segment no-data"
                                    style="width: {{ ($aircraft['stats']['no_data'] / $total) * 100 }}%"></div>
                            </div>
                        </div>
                        <div class="fleet-card-footer">
                            <span
                                class="health-score {{ $aircraft['health'] >= 70 ? 'good' : ($aircraft['health'] >= 40 ? 'medium' : 'bad') }}">
                                {{ $aircraft['health'] }}% Health
                            </span>
                            <span class="fleet-card-action">Open →</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endforeach

    <!-- Quick Stats -->
    <section class="stats-section">
        <h2>📊 Quick Stats</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ count($fleet) }}</div>
                <div class="stat-label">Aircraft</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ array_sum($totalStats) }}</div>
                <div class="stat-label">Total Seats Tracked</div>
            </div>
            <div class="stat-item">
                @php
                    $totalTracked = $totalStats['safe'] + $totalStats['warning'] + $totalStats['critical'] + $totalStats['expired'];
                    $healthScore = $totalTracked > 0 ? round(($totalStats['safe'] / $totalTracked) * 100) : 0;
                @endphp
                <div class="stat-value">{{ $healthScore }}%</div>
                <div class="stat-label">Health Score</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $totalStats['critical'] + $totalStats['expired'] }}</div>
                <div class="stat-label">Needs Attention</div>
            </div>
        </div>
    </section>
@endsection