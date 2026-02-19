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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2>📈 Fleet Overview</h2>

            <!-- Fleet Multi-Select Dropdown -->
            <div class="fleet-dropdown" style="position: relative;">
                <button type="button" id="fleetDropdownBtn" class="btn btn-secondary"
                    style="padding: 0.5rem 1rem; display: flex; align-items: center; gap: 8px;">
                    <span>✈️ Filter Fleet</span>
                    <span style="font-size: 0.7em;">▼</span>
                </button>
                <div id="fleetDropdownMenu" class="fleet-dropdown-menu">
                    <!-- Select All Option -->
                    <label class="fleet-checkbox-item all-fleets" style="border-bottom: 1px solid var(--border); margin-bottom: 4px; padding-bottom: 8px;">
                        <input type="checkbox" id="fleetCheckAll" class="fleet-checkbox-all" checked>
                        <span class="fleet-name">All Fleets</span>
                    </label>

                    @foreach($perFleetStats as $baseType => $stats)
                        <label class="fleet-checkbox-item">
                            <input type="checkbox" class="fleet-checkbox" checked
                                data-fleet="{{ $baseType }}"
                                data-safe="{{ $stats['safe'] }}"
                                data-warning="{{ $stats['warning'] }}"
                                data-critical="{{ $stats['critical'] }}"
                                data-expired="{{ $stats['expired'] }}">
                            <span class="fleet-name">{{ $baseType }}</span>
                            <span class="fleet-count">{{ $stats['count'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="summary-cards">
            <div class="summary-card safe">
                <div class="summary-icon">🟢</div>
                <div class="summary-value" id="overviewSafe" data-initial="{{ $totalStats['safe'] }}">
                    {{ $totalStats['safe'] }}
                </div>
                <div class="summary-label">Safe</div>
                <div class="summary-desc">> 6 months</div>
            </div>
            <div class="summary-card warning">
                <div class="summary-icon">🟡</div>
                <div class="summary-value" id="overviewWarning" data-initial="{{ $totalStats['warning'] }}">
                    {{ $totalStats['warning'] }}
                </div>
                <div class="summary-label">Warning</div>
                <div class="summary-desc">3-6 months</div>
            </div>
            <div class="summary-card critical">
                <div class="summary-icon">🔴</div>
                <div class="summary-value" id="overviewCritical" data-initial="{{ $totalStats['critical'] }}">
                    {{ $totalStats['critical'] }}
                </div>
                <div class="summary-label">Critical</div>
                <div class="summary-desc">
                    < 3 months</div>
                </div>
                <div class="summary-card expired">
                    <div class="summary-icon">🟣</div>
                    <div class="summary-value" id="overviewExpired" data-initial="{{ $totalStats['expired'] }}">
                        {{ $totalStats['expired'] }}
                    </div>
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

            // Fleet Overview Multi-Select Logic
            const fleetCheckboxes = document.querySelectorAll('.fleet-checkbox');
            const checkAllBox = document.getElementById('fleetCheckAll');
            const overviewSafe = document.getElementById('overviewSafe');
            const overviewWarning = document.getElementById('overviewWarning');
            const overviewCritical = document.getElementById('overviewCritical');
            const overviewExpired = document.getElementById('overviewExpired');

            // Initial totals (all checked by default or none checked = all)
            const initialStats = {
                safe: parseInt(overviewSafe.dataset.initial),
                warning: parseInt(overviewWarning.dataset.initial),
                critical: parseInt(overviewCritical.dataset.initial),
                expired: parseInt(overviewExpired.dataset.initial)
            };

            function updateOverview() {
                let totalSafe = 0, totalWarning = 0, totalCritical = 0, totalExpired = 0;
                let checkedCount = 0;

                fleetCheckboxes.forEach(cb => {
                    if (cb.checked) {
                        checkedCount++;
                        totalSafe += parseInt(cb.dataset.safe);
                        totalWarning += parseInt(cb.dataset.warning);
                        totalCritical += parseInt(cb.dataset.critical);
                        totalExpired += parseInt(cb.dataset.expired);
                    }
                });

                // If nothing checked, show ALL (or show 0? Usually "All" is better UX, but let's stick to selection)
                // Let's make it: if nothing checked -> Show 0 (or revert to All? Let's revert to All for better UX)
                if (!checkedCount) { // Changed from !anyChecked to !checkedCount
                    totalSafe = initialStats.safe;
                    totalWarning = initialStats.warning;
                    totalCritical = initialStats.critical;
                    totalExpired = initialStats.expired;
                }

                // Update "Check All" state
                if (checkAllBox) {
                    checkAllBox.checked = (checkedCount === fleetCheckboxes.length);
                    checkAllBox.indeterminate = (checkedCount > 0 && checkedCount < fleetCheckboxes.length);
                }

                overviewSafe.textContent = totalSafe;
                overviewWarning.textContent = totalWarning;
                overviewCritical.textContent = totalCritical;
                overviewExpired.textContent = totalExpired;

                // Simple animation
                [overviewSafe, overviewWarning, overviewCritical, overviewExpired].forEach(el => {
                    el.style.transform = 'scale(1.15)';
                    setTimeout(() => el.style.transform = 'scale(1)', 200);
                });
            }

            // "Check All" Event Listener
            checkAllBox?.addEventListener('change', function () {
                const isChecked = this.checked;
                fleetCheckboxes.forEach(cb => {
                    cb.checked = isChecked;
                });
                updateOverview();
            });

            // Individual Checkbox Listener
            fleetCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateOverview);
            });

            // Initial update to set correct state for "Check All" and overview totals
            updateOverview();

            // Toggle Dropdown
            const dropdownBtn = document.getElementById('fleetDropdownBtn');
            const dropdownMenu = document.getElementById('fleetDropdownMenu');

            dropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
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