/**
 * Life Vest Tracker - Laravel Version
 * ====================================
 * Seat selection and date management
 */

// State
const state = {
    selectedSeats: new Set(),
};

// DOM Elements
const elements = {
    btnSetDate: null,
    btnClearSelection: null,
    selectionInfo: null,
    dateModal: null,
    dateInput: null,
    btnCancel: null,
    btnApplyDate: null,
    modalClose: null,
    modalInfo: null,
    toast: null,
};

// Initialize when DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Check if we're on aircraft page
    if (!window.AIRCRAFT_CONFIG) return;

    // Get elements
    elements.btnSetDate = document.getElementById('btnSetDate');
    elements.btnClearSelection = document.getElementById('btnClearSelection');
    elements.selectionInfo = document.getElementById('selectionInfo');
    elements.dateModal = document.getElementById('dateModal');
    elements.dateInput = document.getElementById('dateInput');
    elements.btnCancel = document.getElementById('btnCancel');
    elements.btnApplyDate = document.getElementById('btnApplyDate');
    elements.modalClose = document.getElementById('modalClose');
    elements.modalInfo = document.getElementById('modalInfo');
    elements.toast = document.getElementById('toast');

    // Setup event listeners
    setupEventListeners();
});

function setupEventListeners() {
    // Seat click events
    document.querySelectorAll('.seat-card').forEach(seat => {
        seat.addEventListener('click', handleSeatClick);
    });

    // Row number click events
    document.querySelectorAll('.row-number').forEach(rowNum => {
        rowNum.addEventListener('click', handleRowClick);
    });

    // Column header click events
    document.querySelectorAll('.col-header').forEach(colHeader => {
        colHeader.addEventListener('click', handleColumnClick);
    });

    // Toolbar buttons
    if (elements.btnSetDate) {
        elements.btnSetDate.addEventListener('click', openDateModal);
    }
    if (elements.btnClearSelection) {
        elements.btnClearSelection.addEventListener('click', clearSelection);
    }

    // Modal buttons
    if (elements.btnCancel) {
        elements.btnCancel.addEventListener('click', closeDateModal);
    }
    if (elements.modalClose) {
        elements.modalClose.addEventListener('click', closeDateModal);
    }
    if (elements.btnApplyDate) {
        elements.btnApplyDate.addEventListener('click', applyDate);
    }

    // Close modal on backdrop click
    if (elements.dateModal) {
        elements.dateModal.addEventListener('click', (e) => {
            if (e.target === elements.dateModal) closeDateModal();
        });
    }

    // Click outside to clear selection
    document.addEventListener('click', (e) => {
        // Ignore clicks on seats, toolbar, modal, or toast
        if (e.target.closest('.seat-card') ||
            e.target.closest('.toolbar') ||
            e.target.closest('.modal-content') ||
            e.target.closest('.row-number') ||
            e.target.closest('.col-header') ||
            e.target.closest('#toast')) {
            return;
        }

        // Only clear if we have selections
        if (state.selectedSeats.size > 0) {
            clearSelection();
            // showToast('Selection cleared', 'info'); // Optional feedback
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', handleKeyboard);
}

// Helper to select a single seat
function selectSeat(seatId) {
    if (!state.selectedSeats.has(seatId)) {
        state.selectedSeats.add(seatId);
        const seatCard = document.querySelector(`.seat-card[data-seat="${seatId}"]`);
        if (seatCard) {
            seatCard.classList.add('selected');
        }
    }
}

// Helper to deselect a single seat
function deselectSeat(seatId) {
    if (state.selectedSeats.has(seatId)) {
        state.selectedSeats.delete(seatId);
        const seatCard = document.querySelector(`.seat-card[data-seat="${seatId}"]`);
        if (seatCard) {
            seatCard.classList.remove('selected');
        }
    }
}

// Toggle seat selection (using helpers)
function toggleSeat(seatId) {
    if (state.selectedSeats.has(seatId)) {
        deselectSeat(seatId);
    } else {
        selectSeat(seatId);
    }
}

// Handle seat click
function handleSeatClick(e) {
    const seatId = this.dataset.seat;
    if (!seatId) return;

    e.stopPropagation();

    // Check for Shift+Click range selection
    if (e.shiftKey && state.lastSelectedSeat) {
        handleRangeSelection(state.lastSelectedSeat, seatId);
    }
    // Check for Ctrl/Cmd+Click for multi-select
    else if (e.ctrlKey || e.metaKey) {
        toggleSeat(seatId);
    }
    // Normal click - single select (or toggle if already selected)
    else {
        // If clicking on an unselected seat, clear others first
        if (!state.selectedSeats.has(seatId)) {
            clearSelection();
            selectSeat(seatId);
        } else {
            // If clicking an already selected seat without modifier, toggle it (standard behavior)
            // or if user wants strict single select, we could just keep it selected.
            // Let's allow deselecting if it's the only one, or clearing others if multiple.
            if (state.selectedSeats.size > 1) {
                clearSelection();
                selectSeat(seatId);
            } else {
                toggleSeat(seatId);
            }
        }
    }

    // Update last selected seat for range selection
    if (state.selectedSeats.has(seatId)) {
        state.lastSelectedSeat = seatId;
    }

    updateUI();
}

function handleRangeSelection(startSeatId, endSeatId) {
    // Get all seat cards in DOM order
    const allSeats = Array.from(document.querySelectorAll('.seat-card[data-seat]'));

    const startIndex = allSeats.findIndex(s => s.dataset.seat === startSeatId);
    const endIndex = allSeats.findIndex(s => s.dataset.seat === endSeatId);

    if (startIndex === -1 || endIndex === -1) return;

    const minIdx = Math.min(startIndex, endIndex);
    const maxIdx = Math.max(startIndex, endIndex);

    // Select all seats in between in DOM order
    // This is robust for any layout (staggered, grids, etc.)
    for (let i = minIdx; i <= maxIdx; i++) {
        selectSeat(allSeats[i].dataset.seat);
    }

    showToast(`Selected ${maxIdx - minIdx + 1} seats`, 'info');
}

// Row click handler
function handleRowClick(e) {
    e.stopPropagation();
    const row = e.currentTarget.dataset.row;
    const seatsInRow = document.querySelectorAll(`.seat-card[data-row="${row}"]`);

    clearSelection();
    seatsInRow.forEach(seat => {
        state.selectedSeats.add(seat.dataset.seat);
        seat.classList.add('selected');
    });

    updateUI();
    showToast(`Row ${row} selected (${seatsInRow.length} seats)`, 'success');
}

// Column click handler
function handleColumnClick(e) {
    e.stopPropagation();
    const col = e.currentTarget.dataset.col;
    const section = e.currentTarget.closest('.cabin-section');
    const seatsInCol = section.querySelectorAll(`.seat-card[data-col="${col}"]`);

    seatsInCol.forEach(seat => {
        if (!state.selectedSeats.has(seat.dataset.seat)) {
            state.selectedSeats.add(seat.dataset.seat);
            seat.classList.add('selected');
        }
    });

    updateUI();
    showToast(`Column ${col} added (${seatsInCol.length} seats)`, 'success');
}

// Toggle seat selection
function toggleSeatSelection(seatId, seatCard) {
    if (state.selectedSeats.has(seatId)) {
        state.selectedSeats.delete(seatId);
        seatCard.classList.remove('selected');
    } else {
        state.selectedSeats.add(seatId);
        seatCard.classList.add('selected');
    }
}

// Clear all selections
function clearSelection() {
    state.selectedSeats.clear();
    document.querySelectorAll('.seat-card.selected').forEach(seat => {
        seat.classList.remove('selected');
    });
    updateUI();
}

// Update UI based on selection
function updateUI() {
    const count = state.selectedSeats.size;

    // Update button state
    if (elements.btnSetDate) {
        elements.btnSetDate.disabled = count === 0;
    }

    // Update selection info
    if (elements.selectionInfo) {
        if (count === 0) {
            elements.selectionInfo.textContent = 'No seats selected';
        } else {
            elements.selectionInfo.textContent = `${count} seat${count > 1 ? 's' : ''} selected`;
        }
    }
}

// Open date modal
function openDateModal() {
    if (state.selectedSeats.size === 0) return;

    if (elements.modalInfo) {
        elements.modalInfo.textContent = `Setting date for ${state.selectedSeats.size} seat(s)`;
    }
    if (elements.dateModal) {
        elements.dateModal.classList.add('show');
    }
}

// Close date modal
function closeDateModal() {
    if (elements.dateModal) {
        elements.dateModal.classList.remove('show');
    }
}

// Apply date to selected seats
async function applyDate() {
    const dateValue = elements.dateInput?.value;
    if (!dateValue) {
        showToast('Please select a date', 'error');
        return;
    }

    const seatIds = Array.from(state.selectedSeats);

    try {
        const response = await fetch(window.AIRCRAFT_CONFIG.updateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.AIRCRAFT_CONFIG.csrfToken,
            },
            body: JSON.stringify({
                seat_ids: seatIds,
                expiry_date: dateValue,
            }),
        });

        const data = await response.json();

        if (data.success) {
            // Update UI
            const formattedDate = new Date(dateValue).toLocaleDateString('en-GB', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });

            seatIds.forEach(seatId => {
                const seatCard = document.querySelector(`.seat-card[data-seat="${seatId}"]`);
                if (seatCard) {
                    const dateEl = seatCard.querySelector('.seat-date');
                    if (dateEl) {
                        dateEl.textContent = formattedDate;
                        dateEl.dataset.date = dateValue;
                    }

                    // Update status class
                    updateSeatStatus(seatCard, dateValue);
                }
            });

            closeDateModal();
            clearSelection();
            showToast(data.message, 'success');
        }
    } catch (error) {
        console.error('Error updating seats:', error);
        showToast('Failed to update seats', 'error');
    }
}

// Update seat status based on date
function updateSeatStatus(seatCard, dateValue) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const expiryDate = new Date(dateValue);
    expiryDate.setHours(0, 0, 0, 0);

    const diffDays = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));

    // Remove all status classes
    seatCard.classList.remove('status-safe', 'status-warning', 'status-critical', 'status-expired', 'status-no-data');

    // Add appropriate status class
    if (diffDays < 0) {
        seatCard.classList.add('status-expired');
    } else if (diffDays < 90) {
        seatCard.classList.add('status-critical');
    } else if (diffDays < 180) {
        seatCard.classList.add('status-warning');
    } else {
        seatCard.classList.add('status-safe');
    }
}

// Keyboard handler
function handleKeyboard(e) {
    // Escape - close modal or clear selection
    if (e.key === 'Escape') {
        if (elements.dateModal?.classList.contains('show')) {
            closeDateModal();
        } else if (state.selectedSeats.size > 0) {
            clearSelection();
            showToast('Selection cleared', 'info');
        }
    }

    // Enter - open date modal if seats selected
    if (e.key === 'Enter') {
        if (!elements.dateModal?.classList.contains('show') && state.selectedSeats.size > 0) {
            e.preventDefault();
            openDateModal();
        }
    }

    // Ctrl+A - select all
    if (e.key === 'a' && (e.ctrlKey || e.metaKey)) {
        e.preventDefault();
        // Select all visible seats
        document.querySelectorAll('.seat-card').forEach(seat => {
            state.selectedSeats.add(seat.dataset.seat);
            seat.classList.add('selected');
        });
        updateUI();
        showToast('All seats selected', 'success');
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    if (!elements.toast) return;

    const icon = type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ';
    elements.toast.querySelector('.toast-icon').textContent = icon;
    elements.toast.querySelector('.toast-message').textContent = message;
    elements.toast.className = `toast ${type} show`;

    setTimeout(() => {
        elements.toast.classList.remove('show');
    }, 3000);
}

// ========================================
// DASHBOARD SEARCH & FILTER
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const statusFilter = document.getElementById('statusFilter');

    // Only run on dashboard (where search exists)
    if (!searchInput || !typeFilter) return;

    function filterFleet() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const typeValue = typeFilter.value.toLowerCase();
        const statusValue = statusFilter ? statusFilter.value.toLowerCase() : 'all';

        // Get all fleet sections
        const fleetSections = document.querySelectorAll('.fleet-section');

        fleetSections.forEach(section => {
            const sectionType = section.querySelector('h2')?.textContent.toLowerCase() || '';
            const cards = section.querySelectorAll('.fleet-card');
            let visibleCount = 0;

            // Check if section matches type filter
            const typeMatch = typeValue === 'all' || sectionType.includes(typeValue);

            cards.forEach(card => {
                const registration = card.querySelector('.fleet-card-reg')?.textContent.toLowerCase() || '';
                const cardStatus = card.dataset.status || 'active';

                const searchMatch = !searchTerm || registration.includes(searchTerm);
                const statusMatch = statusValue === 'all' || cardStatus === statusValue;

                if (typeMatch && searchMatch && statusMatch) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Hide entire section if no visible cards or type doesn't match
            // (Only hide section if type strictly doesn't match OR no cards visible after filtering)
            section.style.display = (visibleCount > 0) ? '' : 'none';
        });
    }

    // Event listeners
    searchInput.addEventListener('input', filterFleet);
    typeFilter.addEventListener('change', filterFleet);
    if (statusFilter) statusFilter.addEventListener('change', filterFleet);

    // Clear search on Escape
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            searchInput.value = '';
            filterFleet();
        }
    });
});


// ========================================
// FLEET MANAGER SEARCH & SORT
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('fleetSearch');
    const sortSelect = document.getElementById('fleetSort');
    const tableBody = document.querySelector('.fleet-table tbody');

    // Only run on Fleet Manager page
    if (!searchInput || !sortSelect || !tableBody) return;

    const rows = Array.from(tableBody.querySelectorAll('tr'));

    function filterAndSort() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const sortValue = sortSelect.value;

        // 1. Filter Rows
        const visibleRows = rows.filter(row => {
            // Index 1 = Registration, Index 2 = Type (Index 0 is Numbering)
            const reg = row.cells[1].textContent.toLowerCase();
            const type = row.cells[2].textContent.toLowerCase();
            const visible = reg.includes(searchTerm) || type.includes(searchTerm);
            row.style.display = visible ? '' : 'none';
            return visible;
        });

        // 2. Sort Visible Rows
        visibleRows.sort((a, b) => {
            const regA = a.cells[1].textContent.trim();
            const regB = b.cells[1].textContent.trim();
            const typeA = a.cells[2].textContent.trim();
            const typeB = b.cells[2].textContent.trim();
            const statusA = a.querySelector('.status-badge').textContent.trim();
            const statusB = b.querySelector('.status-badge').textContent.trim();

            switch (sortValue) {
                case 'registration_desc': return regB.localeCompare(regA);
                case 'type_asc': return typeA.localeCompare(typeB);
                case 'type_desc': return typeB.localeCompare(typeA);
                case 'status_active': return statusA === statusB ? 0 : (statusA === 'ACTIVE' ? -1 : 1);
                case 'status_prolong': return statusA === statusB ? 0 : (statusA === 'PROLONG' ? -1 : 1);
                default: return regA.localeCompare(regB); // registration_asc
            }
        });

        // 3. Re-append sorted rows & Update Numbers
        visibleRows.forEach((row, index) => {
            tableBody.appendChild(row);
            // Update the first column (index 0) to be 1, 2, 3...
            row.cells[0].textContent = index + 1;
        });
    }

    // Events
    searchInput.addEventListener('input', filterAndSort);
    sortSelect.addEventListener('change', filterAndSort);
});
