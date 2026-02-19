@extends('layouts.app')

@section('content')
    <!-- Filter Toggle Button -->
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
        <button type="button" id="toggleFilters" class="btn btn-secondary"
            style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem;">
            <span>🔍 Filter</span>
            <span id="filterArrow" style="transition: transform 0.2s;">▼</span>
        </button>
        <span id="filterCount" style="color: var(--text-secondary); font-size: 0.875rem;"></span>
    </div>

    <!-- Collapsible Filter Bar -->
    <div id="filterPanel" class="filter-bar"
        style="display: none; flex-wrap: wrap; gap: 0.75rem; align-items: center; margin-bottom: 1.5rem; padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">

        <!-- Search Registration -->
        <input type="text" id="searchInput" class="form-input" placeholder="🔍 Search registration..."
            style="min-width: 200px; max-width: 250px;">

        <select id="filterAirline" class="form-select" style="min-width: 180px; cursor: pointer;">
            <option value="">All Airlines</option>
            @foreach($fleetByAirline as $airlineId => $airline)
                <option value="{{ $airline['name'] }}">{{ $airline['name'] }}</option>
            @endforeach
        </select>

        <select id="filterType" class="form-select" style="min-width: 150px; cursor: pointer;">
            <option value="">All Types</option>
            @php
                $uniqueTypes = collect($fleet)->pluck('type')->unique()->sort();
            @endphp
            @foreach($uniqueTypes as $type)
                <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>

        <select id="filterStatus" class="form-select" style="min-width: 130px; cursor: pointer;">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="prolong">Prolong</option>
        </select>

        <select id="filterHealth" class="form-select" style="min-width: 160px; cursor: pointer;">
            <option value="">All Health</option>
            <option value="critical">🔴 Critical/Expired</option>
            <option value="warning">🟡 Warning</option>
            <option value="safe">🟢 Safe</option>
        </select>

        <button type="button" id="clearFilters" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Clear</button>
    </div>

    <!-- Summary Section -->
    <section class="summary-section">
        <h2>📈 Fleet Overview</h2>

        <!-- Fleet Type Tabs -->
        <div class="fleet-tabs">
            <button class="fleet-tab active" data-fleet="all"
                data-safe="{{ $totalStats['safe'] }}"
                data-warning="{{ $totalStats['warning'] }}"
                data-critical="{{ $totalStats['critical'] }}"
                data-expired="{{ $totalStats['expired'] }}">
                All
            </button>
            @foreach($perFleetStats as $baseType => $stats)
            <button class="fleet-tab" data-fleet="{{ $baseType }}"
                data-safe="{{ $stats['safe'] }}"
                data-warning="{{ $stats['warning'] }}"
                data-critical="{{ $stats['critical'] }}"
                data-expired="{{ $stats['expired'] }}">
                {{ $baseType }} <span class="fleet-tab-count">{{ $stats['count'] }}</span>
            </button>
            @endforeach
        </div>

        <div class="summary-cards">
            <div class="summary-card safe">
                <div class="summary-icon">🟢</div>
                <div class="summary-value" id="overviewSafe">{{ $totalStats['safe'] }}</div>
                <div class="summary-label">Safe</div>
                <div class="summary-desc">> 6 months</div>
            </div>
            <div class="summary-card warning">
                <div class="summary-icon">🟡</div>
                <div class="summary-value" id="overviewWarning">{{ $totalStats['warning'] }}</div>
                <div class="summary-label">Warning</div>
                <div class="summary-desc">3-6 months</div>
            </div>
            <div class="summary-card critical">
                <div class="summary-icon">🔴</div>
                <div class="summary-value" id="overviewCritical">{{ $totalStats['critical'] }}</div>
                <div class="summary-label">Critical</div>
                <div class="summary-desc">
                    < 3 months</div>
                </div>
                <div class="summary-card expired">
                    <div class="summary-icon">🟣</div>
                    <div class="summary-value" id="overviewExpired">{{ $totalStats['expired'] }}</div>
                    <div class="summary-label">Expired</div>
                    <div class="summary-desc">Past due</div>
                </div>
            </div>
    </section>

    <!-- Fleet Cards Section - Grouped by Airline then by Type -->
    @foreach($fleetByAirline as $airlineId => $airline)
        <section class="airline-section" data-airline="{{ $airline['name'] }}" style="margin-bottom: 2rem;">
            <div class="airline-header"
                style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--border);">
                <div>
                    <h2 style="margin: 0; font-size: 1.5rem;">{{ $airline['name'] }}</h2>
                    <span style="color: var(--text-secondary); font-size: 0.875rem;">{{ $airline['code'] }} •
                        <span class="airline-count">{{ $airline['aircraft_count'] }}</span> aircraft</span>
                </div>
            </div>

            @foreach($airline['types'] as $baseType => $typeGroup)
                <section class="fleet-section" style="margin-left: 1rem;">
                    <h3 style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; font-size: 1.125rem;">
                        {{ $typeGroup['icon'] }} {{ $typeGroup['name'] }}
                        <span class="type-count"
                            style="color: var(--text-secondary); font-weight: normal; font-size: 0.875rem;">({{ count($typeGroup['aircraft']) }})</span>
                    </h3>
                    <div class="fleet-cards">
                        @foreach($typeGroup['aircraft'] as $registration => $aircraft)
                            <a href="{{ route('aircraft.show', $registration) }}"
                                class="fleet-card {{ $aircraft['health'] >= 70 ? 'healthy' : ($aircraft['health'] >= 40 ? 'warning' : 'critical') }}"
                                data-status="{{ $aircraft['status'] ?? 'active' }}"
                                data-health="{{ $aircraft['health'] >= 70 ? 'safe' : ($aircraft['health'] >= 40 ? 'warning' : 'critical') }}"
                                data-airline="{{ $airline['name'] }}" data-type="{{ $aircraft['type'] }}">
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
        </section>
    @endforeach

    <!-- Life Vest Replacement Summary -->
    @if(count($pnSummary) > 0)
        <section class="replacement-section">
            <h2>🔄 Life Vest Replacement Summary</h2>
            <div class="replacement-grid">
                @foreach($pnSummary as $item)
                    <div class="replacement-card {{ $item['expired'] > 0 ? 'has-expired' : 'all-good' }}">
                        <div class="replacement-header">
                            <div>
                                <span class="replacement-pn">{{ $item['pn'] }}</span>
                                <span
                                    class="replacement-category {{ $item['category'] }}">{{ strtoupper($item['category']) }}</span>
                            </div>
                            <div class="replacement-counts">
                                <span class="replacement-total">{{ $item['total'] }} total</span>
                                @if($item['expired'] > 0)
                                    <span class="replacement-expired">⚠️ {{ $item['expired'] }} expired</span>
                                @else
                                    <span class="replacement-ok">✅ 0 expired</span>
                                @endif
                            </div>
                        </div>
                        @if(count($item['aircraft']) > 0)
                            <div class="replacement-breakdown">
                                @foreach($item['aircraft'] as $ac)
                                    <span class="breakdown-item">{{ $ac['reg'] }}: {{ $ac['expired'] }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Quick Stats -->
    <section class="stats-section">
        <h2>📊 Quick Stats</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ count($fleetByAirline) }}</div>
                <div class="stat-label">Airlines</div>
            </div>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Fleet Overview Tab Switching
            const fleetTabs = document.querySelectorAll('.fleet-tab');
            const overviewSafe = document.getElementById('overviewSafe');
            const overviewWarning = document.getElementById('overviewWarning');
            const overviewCritical = document.getElementById('overviewCritical');
            const overviewExpired = document.getElementById('overviewExpired');

            fleetTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    fleetTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    overviewSafe.textContent = this.dataset.safe;
                    overviewWarning.textContent = this.dataset.warning;
                    overviewCritical.textContent = this.dataset.critical;
                    overviewExpired.textContent = this.dataset.expired;

                    // Animate values
                    [overviewSafe, overviewWarning, overviewCritical, overviewExpired].forEach(el => {
                        el.style.transform = 'scale(1.15)';
                        setTimeout(() => el.style.transform = 'scale(1)', 200);
                    });
                });
            });
            const toggleBtn = document.getElementById('toggleFilters');
            const filterPanel = document.getElementById('filterPanel');
            const filterArrow = document.getElementById('filterArrow');
            const filterAirline = document.getElementById('filterAirline');
            const filterType = document.getElementById('filterType');
            const filterStatus = document.getElementById('filterStatus');
            const filterHealth = document.getElementById('filterHealth');
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearFilters');
            const filterCount = document.getElementById('filterCount');

            const cards = Array.from(document.querySelectorAll('.fleet-card'));
            const airlineSections = Array.from(document.querySelectorAll('.airline-section'));
            const fleetSections = Array.from(document.querySelectorAll('.fleet-section'));

            // Toggle filter panel
            toggleBtn?.addEventListener('click', function () {
                const isHidden = filterPanel.style.display === 'none';
                filterPanel.style.display = isHidden ? 'flex' : 'none';
                filterArrow.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
            });

            function applyFilters() {
                const airlineFilter = filterAirline?.value || '';
                const typeFilter = filterType?.value || '';
                const statusFilter = filterStatus?.value || '';
                const healthFilter = filterHealth?.value || '';
                const searchQuery = (searchInput?.value || '').toLowerCase().trim();

                let visibleCount = 0;
                const totalCount = cards.length;

                cards.forEach(card => {
                    const cardAirline = card.dataset.airline || '';
                    const cardType = card.dataset.type || '';
                    const cardStatus = card.dataset.status || '';
                    const cardHealth = card.dataset.health || '';
                    // Get registration from the card (looking for fleet-card-reg class)
                    const cardRegElement = card.querySelector('.fleet-card-reg');
                    const cardReg = (cardRegElement?.textContent || '').toLowerCase();

                    let show = true;

                    // Registration search filter
                    if (searchQuery && !cardReg.includes(searchQuery)) {
                        show = false;
                    }

                    // Airline filter
                    if (airlineFilter && cardAirline !== airlineFilter) {
                        show = false;
                    }

                    // Type filter
                    if (typeFilter && cardType !== typeFilter) {
                        show = false;
                    }

                    // Status filter
                    if (statusFilter && cardStatus !== statusFilter) {
                        show = false;
                    }

                    // Health filter
                    if (healthFilter && cardHealth !== healthFilter) {
                        show = false;
                    }

                    card.style.display = show ? '' : 'none';
                    if (show) visibleCount++;
                });

                // Hide empty fleet sections (type groups)
                fleetSections.forEach(section => {
                    const visibleCards = section.querySelectorAll('.fleet-card:not([style*="display: none"])');
                    section.style.display = visibleCards.length > 0 ? '' : 'none';

                    // Update type count
                    const typeCount = section.querySelector('.type-count');
                    if (typeCount) {
                        typeCount.textContent = `(${visibleCards.length})`;
                    }
                });

                // Hide empty airline sections
                airlineSections.forEach(section => {
                    const visibleCards = section.querySelectorAll('.fleet-card:not([style*="display: none"])');
                    section.style.display = visibleCards.length > 0 ? '' : 'none';

                    // Update airline count
                    const airlineCount = section.querySelector('.airline-count');
                    if (airlineCount) {
                        airlineCount.textContent = visibleCards.length;
                    }
                });

                // Update filter count display
                if (airlineFilter || typeFilter || statusFilter || healthFilter || searchQuery) {
                    filterCount.textContent = `Showing ${visibleCount} of ${totalCount} aircraft`;
                } else {
                    filterCount.textContent = '';
                }
            }

            // Event listeners
            filterAirline?.addEventListener('change', applyFilters);
            filterType?.addEventListener('change', applyFilters);
            filterStatus?.addEventListener('change', applyFilters);
            filterHealth?.addEventListener('change', applyFilters);
            searchInput?.addEventListener('input', applyFilters); // Real-time search

            clearBtn?.addEventListener('click', function () {
                if (filterAirline) filterAirline.value = '';
                if (filterType) filterType.value = '';
                if (filterStatus) filterStatus.value = '';
                if (filterHealth) filterHealth.value = '';
                if (searchInput) searchInput.value = '';
                applyFilters();
            });
        });
    </script>
@endpush