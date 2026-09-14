<!-- Monthly Unclaimed Financial Assistance Bulk Messaging Modal -->
<div class="modal fade" id="monthlyUnclaimedModal" tabindex="-1" aria-labelledby="monthlyUnclaimedModalLabel"
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header text-white py-3 px-4" style="background-color: #1A237E !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 38px; height: 38px; background: rgba(255, 255, 255, 0.15); flex-shrink: 0;">
                        <i class="fas fa-bullhorn text-white fs-6"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title fw-bold mb-0 text-white" id="monthlyUnclaimedModalLabel">Message Unclaimed Beneficiaries</h5>
                            <span id="monthlyModalMonthBadge" class="badge bg-warning text-dark rounded-pill px-2.5 py-1 text-xs fw-bold">Loading...</span>
                        </div>
                        <span class="text-white-50 small" style="font-size: 0.78rem;">Financial Assistance Step 2 Notification System</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">

                <!-- Filter Bar: Month Selector & Search -->
                <div class="row g-3 mb-3 pb-2 border-bottom">
                    <div class="col-md-5 col-12">
                        <label class="form-label text-muted small text-uppercase fw-semibold d-block mb-1" for="monthlyUnclaimedSelect" style="font-size: 0.74rem; letter-spacing: 0.04em;">
                            <i class="fas fa-calendar-alt text-primary me-1"></i> Payroll Month
                        </label>
                        <div class="input-group input-group-sm">
                            <select class="form-select form-select-sm rounded-start-3" id="monthlyUnclaimedSelect">
                                <option value="" disabled selected>Loading available months...</option>
                            </select>
                            <button type="button" class="btn btn-outline-secondary rounded-end-3" id="btnRefreshMonthlyUnclaimed"
                                title="Refresh">
                                <i class="fas fa-rotate"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-7 col-12">
                        <label class="form-label text-muted small text-uppercase fw-semibold d-block mb-1" for="monthlySearchInput" style="font-size: 0.74rem; letter-spacing: 0.04em;">
                            <i class="fas fa-search text-primary me-1"></i> Search in Table
                        </label>
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-2.5 text-muted small" style="pointer-events: none;"></i>
                            <input type="text" class="form-control form-control-sm rounded-3 ps-4" id="monthlySearchInput"
                                placeholder="Filter by beneficiary name, representative, barangay, or contact number...">
                        </div>
                    </div>
                </div>

                <!-- Two-Section Layout: Claiming Date & SMS Message -->
                <div class="card border rounded-3 p-3 mb-3 bg-light bg-opacity-50">
                    <div class="row g-3">
                        <!-- Section 1: Claiming Date -->
                        <div class="col-md-4 col-12">
                            <label class="form-label text-muted small text-uppercase fw-semibold d-block mb-1" for="monthlyBulkClaimingDate" style="font-size: 0.74rem; letter-spacing: 0.04em;">
                                <i class="fas fa-calendar-day text-primary me-1"></i> Claiming Date <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm mb-1.5">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i class="fas fa-calendar-day"></i></span>
                                <input type="date" class="form-control form-control-sm rounded-end border-start-0 ps-1 font-monospace"
                                    id="monthlyBulkClaimingDate" required min="{{ date('Y-m-d') }}"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="text-muted small mt-1" style="font-size: 0.76rem; line-height: 1.35;">
                                <i class="fas fa-info-circle text-secondary me-0.5"></i> Select the date when beneficiaries can claim their assistance.
                            </div>
                        </div>

                        <!-- Section 2: SMS Message -->
                        <div class="col-md-8 col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label text-muted small text-uppercase fw-semibold mb-0" for="monthlyBulkMessageBody" style="font-size: 0.74rem; letter-spacing: 0.04em;">
                                    <i class="fas fa-comment-dots text-primary me-1"></i> SMS Message Content <span class="text-danger">*</span>
                                </label>
                                <button type="button"
                                    class="btn btn-link btn-sm p-0 text-decoration-none text-secondary"
                                    id="btnResetBulkTemplate" style="font-size: 0.78rem;" title="Reset to default message template">
                                    <i class="fas fa-rotate-left me-1"></i> Reset to Default
                                </button>
                            </div>

                            <!-- User-Friendly Insert Options -->
                            <div class="d-flex align-items-center gap-1.5 mb-2 flex-wrap">
                                <span class="text-muted small me-1" style="font-size: 0.76rem;">Click to insert:</span>
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill py-0.5 px-2.5"
                                    id="btnInsertNameTag" style="font-size: 0.76rem;" title="Automatically insert the beneficiary's full name">
                                    <i class="fas fa-user-tag me-1"></i> Insert Beneficiary Name
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill py-0.5 px-2.5"
                                    id="btnInsertDateTag" style="font-size: 0.76rem;" title="Automatically insert the selected claiming date">
                                    <i class="fas fa-calendar-plus me-1"></i> Insert Claiming Date
                                </button>
                            </div>

                            <textarea class="form-control rounded-3 border"
                                id="monthlyBulkMessageBody" rows="4" placeholder="Enter SMS message text..."
                                style="resize: vertical; font-size: 0.92rem; line-height: 1.45;"></textarea>

                            <div class="d-flex justify-content-between align-items-center mt-1.5 text-muted small px-1"
                                style="font-size: 0.78rem;">
                                <span class="text-muted" style="font-size: 0.76rem;">
                                    <i class="fas fa-lightbulb text-warning me-1"></i> Tip: You can freely edit this message or insert tags above.
                                </span>
                                <span id="monthlyBulkCharCount" class="fw-semibold text-dark font-monospace">0 characters (1 SMS)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Notification Alert Banner -->
                <div id="bulkSendResultAlert" class="alert alert-success d-flex align-items-center justify-content-between rounded-3 mb-3 py-2 px-3 d-none" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fs-5 me-2 text-success"></i>
                        <div>
                            <span class="fw-semibold text-dark" id="bulkResultTitle">Bulk messaging completed.</span>
                            <span class="text-muted small ms-1" id="bulkResultSubtitle">Processed 0 beneficiaries.</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1.5">
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 text-xs fw-bold" id="bulkResultSentBadge">0 Sent</span>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 text-xs fw-bold" id="bulkResultFailedBadge">0 Failed</span>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 text-xs fw-bold" id="bulkResultNoContactBadge">0 No Contact</span>
                    </div>
                </div>

                <!-- Table Header Selection Controls -->
                <div class="d-flex justify-content-between align-items-center mb-2 px-0.5">
                    <div class="form-check mb-0 d-flex align-items-center">
                        <input class="form-check-input me-2 mt-0" type="checkbox" id="masterSelectAllCheckbox">
                        <label class="form-check-label small fw-semibold text-dark user-select-none" for="masterSelectAllCheckbox">
                            Select All (<span id="monthlySelectedCount" class="fw-bold text-primary">0</span> selected)
                        </label>
                        <button type="button" class="btn btn-link btn-sm text-secondary p-0 ms-2 text-decoration-none"
                            id="btnDeselectAll" style="font-size: 0.78rem;">
                            Deselect All
                        </button>
                    </div>
                    <div class="small text-muted">
                        Showing <strong id="monthlyShowingCount" class="text-dark">0</strong> records
                    </div>
                </div>

                <!-- Beneficiaries Table -->
                <div class="table-responsive rounded-3 border" style="max-height: 380px;">
                    <table class="table table-sm table-hover align-middle mb-0 small">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="text-center" style="width: 38px;"></th>
                                <th class="text-center text-muted fw-semibold" style="width: 40px;">#</th>
                                <th class="fw-semibold text-dark">Beneficiary Name</th>
                                <th class="fw-semibold text-dark">Barangay</th>
                                <th class="fw-semibold text-dark">Contact Number</th>
                                <th class="text-end fw-semibold text-dark">Amount</th>
                                <th class="fw-semibold text-dark">Claiming Date</th>
                                <th class="fw-semibold text-dark">Status</th>
                            </tr>
                        </thead>
                        <tbody id="monthlyUnclaimedTableBody">
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    Loading unclaimed records for the selected month...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light py-2.5 px-4 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    Close
                </button>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-3.5 fw-semibold" id="btnSendSelectedUnclaimed" disabled>
                        <i class="fas fa-check-double me-1.5"></i> Send to Selected (<span id="btnSelectedCountSpan">0</span>)
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-xs fw-semibold btn-brand-primary" id="btnSendAllUnclaimed">
                        <span class="spinner-border spinner-border-sm me-1.5 d-none" id="bulkSendSpinner" role="status"
                            aria-hidden="true"></span>
                        <i class="fas fa-paper-plane me-1.5" id="bulkSendIcon"></i>
                        <span id="btnSendAllText">Send to All Unclaimed</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>