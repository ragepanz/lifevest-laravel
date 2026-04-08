@extends('layouts.app')

@section('content')
    <!-- Filter Toggle Button -->
    <div id="top" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
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
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h2>📈 Fleet Overview</h2>
                @if(count($pnSummary) > 0)
                    <a href="#replacement-summary" class="btn-jump-pn" title="Jump to Replacement Summary">🔄 P/N Summary ↓</a>
                @endif
                @if(count($monthlyPlan) > 0)
                    <a href="#monthly-plan" class="btn-jump-pn" title="Jump to Monthly Plan">📅 Monthly Plan ↓</a>
                @endif
                <a href="#quick-stats" class="btn-jump-pn" title="Jump to Quick Stats">📊 Quick Stats ↓</a>
            </div>

            <!-- Fleet Multi-Select Dropdown -->
            <div class="fleet-dropdown" style="position: relative;">
                <button type="button" id="fleetDropdownBtn" class="btn btn-secondary"
                    style="padding: 0.5rem 1rem; display: flex; align-items: center; gap: 8px;">
                    <span>✈️ Filter Fleet</span>
                    <span style="font-size: 0.7em;">▼</span>
                </button>
                <div id="fleetDropdownMenu" class="fleet-dropdown-menu">
                    <!-- Select All Option -->
                    <label class="fleet-checkbox-item all-fleets"
                        style="border-bottom: 1px solid var(--border); margin-bottom: 4px; padding-bottom: 8px;">
                        <input type="checkbox" id="fleetCheckAll" class="fleet-checkbox-all" checked>
                        <span class="fleet-name">All Fleets</span>
                    </label>

                    @foreach($perFleetStats as $baseType => $stats)
                        <label class="fleet-checkbox-item">
                            <input type="checkbox" class="fleet-checkbox" checked data-fleet="{{ $baseType }}"
                                data-safe="{{ $stats['safe'] }}" data-warning="{{ $stats['warning'] }}"
                                data-critical="{{ $stats['critical'] }}" data-expired="{{ $stats['expired'] }}">
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
        <section class="replacement-section" id="replacement-summary">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>🔄 Life Vest Replacement Summary</h2>
                <a href="#top" class="btn-jump-pn">⬆ Back to Top</a>
            </div>
            <div class="replacement-grid">
                @foreach($pnSummary as $idx => $item)
                    @php
                        $hasAttention = $item['expired'] > 0 || $item['critical'] > 0 || $item['warning'] > 0;
                    @endphp
                    <div class="replacement-card {{ $hasAttention ? 'has-expired' : 'all-good' }}">
                        <div class="replacement-header">
                            <div>
                                <span class="replacement-pn">{{ $item['pn'] }}</span>
                                <span
                                    class="replacement-category {{ $item['category'] }}">{{ strtoupper($item['category']) }}</span>
                            </div>
                            <div class="replacement-counts">
                                <span class="replacement-total">{{ $item['total'] }} total</span>
                            </div>
                        </div>

                        {{-- Clickable status badges (act as filter tabs) --}}
                        <div class="replacement-badges">
                            @if($item['expired'] > 0)
                                <span class="badge-btn badge-expired {{ $item['expired'] > 0 ? 'active' : '' }}" data-tab="expired"
                                    data-card="{{ $idx }}">🟣 {{ $item['expired'] }} expired</span>
                            @endif
                            @if($item['critical'] > 0)
                                <span
                                    class="badge-btn badge-critical {{ $item['expired'] == 0 && $item['critical'] > 0 ? 'active' : '' }}"
                                    data-tab="critical" data-card="{{ $idx }}">🔴 {{ $item['critical'] }} critical</span>
                            @endif
                            @if($item['warning'] > 0)
                                <span
                                    class="badge-btn badge-warning {{ $item['expired'] == 0 && $item['critical'] == 0 && $item['warning'] > 0 ? 'active' : '' }}"
                                    data-tab="warning" data-card="{{ $idx }}">🟡 {{ $item['warning'] }} warning</span>
                            @endif
                            @if(!$hasAttention)
                                <span class="replacement-ok">✅ All safe</span>
                            @endif
                        </div>

                        {{-- Breakdowns --}}
                        @if(count($item['aircraft']) > 0)
                            <div class="replacement-breakdown" data-card="{{ $idx }}" data-type="expired"
                                style="{{ $item['expired'] > 0 ? '' : 'display:none' }}">
                                @foreach($item['aircraft'] as $ac)
                                    @if($ac['expired'] > 0)
                                        <span class="breakdown-item bd-expired">{{ $ac['reg'] }}: {{ $ac['expired'] }}</span>
                                    @endif
                                @endforeach
                            </div>

                            <div class="replacement-breakdown" data-card="{{ $idx }}" data-type="critical"
                                style="{{ $item['expired'] == 0 && $item['critical'] > 0 ? '' : 'display:none' }}">
                                @foreach($item['aircraft'] as $ac)
                                    @if($ac['critical'] > 0)
                                        <span class="breakdown-item bd-critical">{{ $ac['reg'] }}: {{ $ac['critical'] }}</span>
                                    @endif
                                @endforeach
                            </div>

                            <div class="replacement-breakdown" data-card="{{ $idx }}" data-type="warning"
                                style="{{ $item['expired'] == 0 && $item['critical'] == 0 && $item['warning'] > 0 ? '' : 'display:none' }}">
                                @foreach($item['aircraft'] as $ac)
                                    @if($ac['warning'] > 0)
                                        <span class="breakdown-item bd-warning">{{ $ac['reg'] }}: {{ $ac['warning'] }}</span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Monthly Replacement Plan -->
    @if(count($monthlyPlan) > 0)
        <section class="monthly-plan-section" id="monthly-plan">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <h2>📅 Monthly Replacement Plan</h2>
                    <span class="monthly-plan-subtitle">Timeline kebutuhan penggantian life vest per bulan</span>
                </div>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <button type="button" id="toggleAllMonths" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Expand All</button>
                    <a href="#top" class="btn-jump-pn">⬆ Back to Top</a>
                </div>
            </div>

            {{-- Grand Total Summary --}}
            @php
                $grandTotal = collect($monthlyPlan)->sum('total');
                $overdueTotal = isset($monthlyPlan['overdue']) ? $monthlyPlan['overdue']['total'] : 0;
            @endphp
            <div class="monthly-grand-summary">
                <div class="monthly-grand-item">
                    <span class="monthly-grand-value">{{ $grandTotal }}</span>
                    <span class="monthly-grand-label">Total Life Vests</span>
                </div>
                <div class="monthly-grand-item overdue">
                    <span class="monthly-grand-value">{{ $overdueTotal }}</span>
                    <span class="monthly-grand-label">Overdue</span>
                </div>
                <div class="monthly-grand-item">
                    <span class="monthly-grand-value">{{ count($monthlyPlan) - (isset($monthlyPlan['overdue']) ? 1 : 0) }}</span>
                    <span class="monthly-grand-label">Months Ahead</span>
                </div>
            </div>

            {{-- Monthly Timeline --}}
            <div class="monthly-timeline">
                @foreach($monthlyPlan as $monthKey => $month)
                    <div class="monthly-card {{ $month['urgency'] }}" data-month="{{ $monthKey }}">
                        {{-- Month Header (clickable) --}}
                        <div class="monthly-card-header" onclick="toggleMonth('{{ $monthKey }}')">
                            <div class="monthly-card-left">
                                <span class="monthly-urgency-dot {{ $month['urgency'] }}"></span>
                                <div>
                                    <div class="monthly-card-title">
                                        {{ $month['label'] }}
                                        @if($month['urgency'] === 'overdue')
                                            <span class="monthly-badge overdue">OVERDUE</span>
                                        @elseif($month['urgency'] === 'critical')
                                            <span class="monthly-badge critical">CRITICAL</span>
                                        @elseif($month['urgency'] === 'warning')
                                            <span class="monthly-badge warning">WARNING</span>
                                        @endif
                                        @if($month['isCurrentMonth'] ?? false)
                                            <span class="monthly-badge current-month">THIS MONTH</span>
                                        @endif
                                    </div>
                                    <div class="monthly-card-meta">
                                        {{ count($month['pn_breakdown']) }} Part Number(s) • {{ count($month['aircraft_breakdown']) }} Aircraft
                                    </div>
                                </div>
                            </div>
                            <div class="monthly-card-right">
                                <span class="monthly-card-total">{{ $month['total'] }}</span>
                                <span class="monthly-card-unit">vests</span>
                                <button type="button" class="btn-export-month" onclick="event.stopPropagation(); exportMonthlyExcel('{{ $monthKey }}')" title="Export to Excel">
                                    Export to Excel
                                </button>
                                <span class="monthly-card-arrow" id="arrow-{{ $monthKey }}">▼</span>
                            </div>
                        </div>

                        {{-- Month Detail (collapsible) --}}
                        <div class="monthly-card-body" id="body-{{ $monthKey }}" style="display: none;">
                            {{-- P/N Breakdown --}}
                            @foreach($month['pn_breakdown'] as $pnKey => $pnData)
                                <div class="monthly-pn-row">
                                    <div class="monthly-pn-header">
                                        <div class="monthly-pn-info">
                                            <span class="monthly-pn-name">{{ $pnData['pn'] }}</span>
                                            <span class="monthly-pn-category {{ $pnData['category'] }}">{{ strtoupper($pnData['category']) }}</span>
                                        </div>
                                        <span class="monthly-pn-count">× {{ $pnData['count'] }}</span>
                                    </div>
                                    <div class="monthly-aircraft-list">
                                        @foreach($pnData['aircraft'] as $reg => $count)
                                            <a href="{{ route('aircraft.show', $reg) }}" class="monthly-aircraft-chip" title="Open {{ $reg }}">
                                                {{ $reg }}: {{ $count }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            {{-- Aircraft Summary --}}
                            <div class="monthly-aircraft-summary">
                                <div class="monthly-aircraft-summary-title">Aircraft Summary:</div>
                                <div class="monthly-aircraft-summary-list">
                                    @foreach($month['aircraft_breakdown'] as $reg => $acData)
                                        <a href="{{ route('aircraft.show', $reg) }}" class="monthly-ac-summary-chip">
                                            <span class="monthly-ac-reg">{{ $reg }}</span>
                                            <span class="monthly-ac-type">{{ $acData['type'] }}</span>
                                            <span class="monthly-ac-count">{{ $acData['count'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Embed monthly plan data for Excel export --}}
            <script>
                window.monthlyPlanData = @json($monthlyPlan);
            </script>
        </section>
    @endif

    <!-- Quick Stats -->
    <section class="stats-section" id="quick-stats">
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

            // Replacement Summary - Clickable Badge Filtering
            document.querySelectorAll('.badge-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const cardIdx = this.dataset.card;
                    const tab = this.dataset.tab;

                    // Toggle active badge
                    document.querySelectorAll(`.badge-btn[data-card="${cardIdx}"]`).forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Toggle breakdown visibility
                    document.querySelectorAll(`.replacement-breakdown[data-card="${cardIdx}"]`).forEach(bd => {
                        bd.style.display = bd.dataset.type === tab ? '' : 'none';
                    });
                });
            });

            // Monthly Plan - Toggle All
            const toggleAllBtn = document.getElementById('toggleAllMonths');
            if (toggleAllBtn) {
                let allExpanded = false;
                toggleAllBtn.addEventListener('click', function() {
                    allExpanded = !allExpanded;
                    document.querySelectorAll('.monthly-card-body').forEach(body => {
                        body.style.display = allExpanded ? 'block' : 'none';
                    });
                    document.querySelectorAll('.monthly-card-arrow').forEach(arrow => {
                        arrow.style.transform = allExpanded ? 'rotate(180deg)' : 'rotate(0deg)';
                    });
                    document.querySelectorAll('.monthly-card').forEach(card => {
                        if (allExpanded) {
                            card.classList.add('expanded');
                        } else {
                            card.classList.remove('expanded');
                        }
                    });
                    this.textContent = allExpanded ? 'Collapse All' : 'Expand All';
                });
            }

            // Auto-expand overdue and critical
            document.querySelectorAll('.monthly-card.overdue, .monthly-card.critical').forEach(card => {
                const monthKey = card.dataset.month;
                const body = document.getElementById('body-' + monthKey);
                const arrow = document.getElementById('arrow-' + monthKey);
                if (body) {
                    body.style.display = 'block';
                    card.classList.add('expanded');
                }
                if (arrow) arrow.style.transform = 'rotate(180deg)';
            });
        });

        // Monthly Plan - Toggle individual month (must be global function for onclick)
        function toggleMonth(monthKey) {
            const body = document.getElementById('body-' + monthKey);
            const arrow = document.getElementById('arrow-' + monthKey);
            const card = document.querySelector(`.monthly-card[data-month="${monthKey}"]`);

            if (body) {
                const isHidden = body.style.display === 'none';
                body.style.display = isHidden ? 'block' : 'none';
                if (card) card.classList.toggle('expanded', isHidden);
                if (arrow) arrow.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }

        // Monthly Plan - Export to Excel
        function exportMonthlyExcel(monthKey) {
            if (typeof XLSX === 'undefined') {
                alert('Library Excel belum termuat. Coba refresh halaman.');
                return;
            }

            const data = window.monthlyPlanData;
            if (!data || !data[monthKey]) {
                alert('Data tidak ditemukan untuk bulan ini.');
                return;
            }

            const month = data[monthKey];
            const label = month.label;
            const wb = XLSX.utils.book_new();

            // === Sheet 1: Part Number Breakdown ===
            const pnRows = [];
            const pnBreakdown = month.pn_breakdown;

            for (const pnKey in pnBreakdown) {
                const pn = pnBreakdown[pnKey];
                const aircraft = pn.aircraft;

                for (const reg in aircraft) {
                    pnRows.push({
                        'Part Number': pn.pn,
                        'Category': pn.category.toUpperCase(),
                        'Aircraft': reg,
                        'Qty': aircraft[reg],
                    });
                }
            }

            // Add total row
            pnRows.push({});
            pnRows.push({
                'Part Number': 'TOTAL',
                'Category': '',
                'Aircraft': '',
                'Qty': month.total,
            });

            const ws1 = XLSX.utils.json_to_sheet(pnRows);

            // Set column widths
            ws1['!cols'] = [
                { wch: 20 },  // Part Number
                { wch: 10 },  // Category
                { wch: 12 },  // Aircraft
                { wch: 6 },   // Qty
            ];

            XLSX.utils.book_append_sheet(wb, ws1, 'Part Number Breakdown');

            // === Sheet 2: Aircraft Summary ===
            const acRows = [];
            const acBreakdown = month.aircraft_breakdown;

            for (const reg in acBreakdown) {
                acRows.push({
                    'Registration': reg,
                    'Aircraft Type': acBreakdown[reg].type,
                    'Qty': acBreakdown[reg].count,
                });
            }

            acRows.push({});
            acRows.push({
                'Registration': 'TOTAL',
                'Aircraft Type': '',
                'Qty': month.total,
            });

            const ws2 = XLSX.utils.json_to_sheet(acRows);
            ws2['!cols'] = [
                { wch: 14 },  // Registration
                { wch: 16 },  // Aircraft Type
                { wch: 6 },   // Qty
            ];

            XLSX.utils.book_append_sheet(wb, ws2, 'Aircraft Summary');

            // Generate filename
            const filename = `Life_Vest_Replacement_${label.replace(/\s+/g, '_')}.xlsx`;
            XLSX.writeFile(wb, filename);
        }
    </script>
@endpush