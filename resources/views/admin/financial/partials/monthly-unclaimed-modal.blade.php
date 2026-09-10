<!-- Monthly Unclaimed Financial Assistance Bulk Messaging Modal -->
<div class="modal fade" id="monthlyUnclaimedModal" tabindex="-1" aria-labelledby="monthlyUnclaimedModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #1A237E 0%, #283593 100%) !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle p-2 bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="fas fa-bullhorn text-white fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title fw-bold mb-0 text-white" id="monthlyUnclaimedModalLabel">Message Unclaimed Financial Assistance</h5>
                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-0.5 text-xs fw-bold" id="monthlyModalMonthBadge">Loading...</span>
                        </div>
                        <span class="text-white-50 small">Send Tagalog SMS notifications to beneficiaries with unclaimed financial assistance for the selected month</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4 bg-light bg-opacity-25">
                
                <!-- Month Filter & Overview Controls Bar -->
                <div class="card border rounded-3 p-3 mb-3 bg-white shadow-xs">
                    <div class="row g-3 align-items-end">
                        <!-- Month Selector -->
                        <div class="col-md-6 col-12">
                            <label class="form-label small fw-bold text-dark mb-1">
                                <i class="fas fa-calendar-alt text-primary me-1"></i> Select Payroll Month
                            </label>
                            <div class="input-group input-group-sm">
                                <select class="form-select form-select-sm rounded-start-3 fw-semibold" id="monthlyUnclaimedSelect">
                                    <option value="" disabled selected>Loading available months...</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary rounded-end-3" id="btnRefreshMonthlyUnclaimed" title="Refresh records for this month">
                                    <i class="fas fa-rotate"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Search within Modal -->
                        <div class="col-md-6 col-12">
                            <label class="form-label small fw-bold text-dark mb-1">
                                <i class="fas fa-search text-muted me-1"></i> Search Beneficiaries in this Month
                            </label>
                            <input type="text" class="form-control form-control-sm rounded-3" id="monthlySearchInput" 
                                placeholder="Filter by beneficiary name, rep, barangay...">
                        </div>
                    </div>
                </div>

                <!-- Template Preview Card -->
                <div class="alert alert-info py-2.5 px-3 rounded-3 mb-3 border-info-subtle bg-info-subtle bg-opacity-50">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-info-circle text-primary mt-1"></i>
                        <div class="small text-dark">
                            <strong>Tagalog Default Template:</strong>
                            <span class="fst-italic text-secondary d-block mt-0.5">
                                "Magandang araw, [Beneficiary Name]. Ito po ay mula sa Municipal Social Welfare and Development Office (MSWDO). Ang inyong tulong pinansyal ay maaari nang kunin sa [Claiming Date]. Mangyaring magtungo sa aming tanggapan sa naturang petsa at dalhin ang inyong valid ID at mga kinakailangang dokumento. Maraming salamat po."
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Results Notification Alert Banner (Appears after bulk send) -->
                <div id="bulkSendResultAlert" class="alert alert-success d-none rounded-3 mb-3 p-3 shadow-xs border-success" role="alert">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fs-4 text-success me-2.5"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-success" id="bulkResultTitle">Bulk messaging completed successfully!</h6>
                                <div class="small text-muted" id="bulkResultSubtitle">Processed 0 beneficiaries.</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success rounded-pill px-2.5 py-1 text-xs" id="bulkResultSentBadge">0 Sent</span>
                            <span class="badge bg-danger rounded-pill px-2.5 py-1 text-xs" id="bulkResultFailedBadge">0 Failed</span>
                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 text-xs" id="bulkResultNoContactBadge">0 No Contact</span>
                        </div>
                    </div>
                </div>

                <!-- Table Header Selection Controls -->
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input" type="checkbox" id="masterSelectAllCheckbox">
                            <label class="form-check-label small fw-bold text-dark user-select-none" for="masterSelectAllCheckbox">
                                Select All Unclaimed (<span id="monthlySelectedCount">0</span> selected)
                            </label>
                        </div>
                        <button type="button" class="btn btn-link btn-sm text-secondary p-0 text-decoration-none small" id="btnDeselectAll">
                            Deselect All
                        </button>
                    </div>
                    <div class="small text-muted">
                        <i class="fas fa-shield-alt text-success me-1"></i> Messages sent individually with personalized beneficiary details
                    </div>
                </div>

                <!-- Beneficiaries Master Table -->
                <div class="table-responsive rounded-3 border bg-white shadow-xs" style="max-height: 420px;">
                    <table class="table table-hover table-clean align-middle mb-0 small">
                        <thead class="table-light sticky-top shadow-xs">
                            <tr>
                                <th class="text-center" style="width: 40px;">
                                    <span class="visually-hidden">Select</span>
                                </th>
                                <th class="text-center" style="width: 45px;">#</th>
                                <th>Beneficiary Name</th>
                                <th>Barangay</th>
                                <th>Contact Number</th>
                                <th class="text-end">Amount</th>
                                <th>Claiming Date</th>
                                <th>Notification Status</th>
                                <th class="text-center" style="width: 110px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="monthlyUnclaimedTableBody">
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    Loading unclaimed records for the selected month...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light py-2.5 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Showing <strong id="monthlyShowingCount">0</strong> unclaimed assistance records
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">
                        Close
                    </button>
                    <!-- Send to Selected -->
                    <button type="button" class="btn btn-outline-primary rounded-pill px-3.5 btn-sm fw-semibold shadow-xs" id="btnSendSelectedUnclaimed" disabled>
                        <i class="fas fa-check-double me-1"></i> Send to Selected (<span id="btnSelectedCountSpan">0</span>)
                    </button>
                    <!-- Send to All Unclaimed -->
                    <button type="button" class="btn btn-primary rounded-pill px-4 btn-sm fw-semibold shadow-xs" id="btnSendAllUnclaimed" style="background: #1A237E; border-color: #1A237E;">
                        <span class="spinner-border spinner-border-sm me-1 d-none" id="bulkSendSpinner" role="status" aria-hidden="true"></span>
                        <i class="fas fa-paper-plane me-1" id="bulkSendIcon"></i>
                        <span id="btnSendAllText">Send Message to All Unclaimed</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
