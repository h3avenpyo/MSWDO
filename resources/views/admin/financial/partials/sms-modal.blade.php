<!-- Financial Assistance Step 2 - SMS Messaging Modal -->
<div class="modal fade" id="smsMessagingModal" tabindex="-1" aria-labelledby="smsMessagingModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white py-3 px-4" style="background: linear-gradient(135deg, #1A237E 0%, #283593 100%) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle p-2 bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-paper-plane text-white fs-6"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="smsMessagingModalLabel">Send SMS Notification</h5>
                        <span class="text-white-50 small">Financial Assistance Step 2 Notification System</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <!-- Missing Contact Warning Banner (Shown only when contact is missing) -->
                <div id="smsNoContactAlert" class="alert alert-danger d-flex align-items-center rounded-3 mb-3 d-none" role="alert">
                    <i class="fas fa-exclamation-triangle fs-5 me-2"></i>
                    <div>
                        <strong>No contact number is available for this beneficiary.</strong>
                        <div class="small">SMS messaging is disabled until a valid contact number is updated in the beneficiary record.</div>
                    </div>
                </div>

                <!-- Previous Notification Alert Banner (Shown if already notified) -->
                <div id="smsDuplicateAlert" class="alert alert-warning d-flex align-items-center justify-content-between rounded-3 mb-3 py-2 px-3 d-none" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fs-5 me-2 text-warning-emphasis"></i>
                        <div>
                            <span class="fw-semibold text-warning-emphasis">This beneficiary was already notified.</span>
                            <span class="text-muted small ms-1" id="smsLastSentText">Last message sent: --</span>
                            <div class="text-muted small">Do you still want to send another message?</div>
                        </div>
                    </div>
                    <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 text-xs fw-bold">Previously Notified</span>
                </div>

                <!-- Beneficiary Summary Details Card -->
                <div class="card border rounded-3 p-3 mb-3 bg-light bg-opacity-50">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6 col-12">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Beneficiary / Recipient</span>
                            <div class="fw-bold text-dark fs-6" id="smsModalBeneficiaryName">--</div>
                            <div class="text-muted small" id="smsModalRepContainer">
                                <span class="badge bg-info-subtle text-info border border-info-subtle text-2xs rounded-pill" id="smsModalRepBadge">Representative</span>
                                <span class="ms-1" id="smsModalRepName">--</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Contact Number</span>
                            <div class="d-flex align-items-center gap-2 mt-0.5">
                                <span class="fw-bold text-dark font-monospace fs-6" id="smsModalContactNumber">--</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle text-2xs rounded-pill" id="smsContactStatusBadge">
                                    <i class="fas fa-check-circle me-0.5"></i> Verified
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 pt-2 border-top">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Assistance / Purpose</span>
                            <div class="text-dark small fw-medium text-truncate" id="smsModalPurpose">--</div>
                            <div class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill text-xs fw-bold mt-1" id="smsModalAmount">₱0.00</div>
                        </div>
                        <div class="col-md-6 col-12 pt-2 border-top">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Claiming Date</span>
                            <div class="d-flex align-items-center gap-2 mt-0.5">
                                <i class="fas fa-calendar-day text-primary"></i>
                                <span class="fw-bold text-dark" id="smsModalClaimingDate">--</span>
                                <button type="button" class="btn btn-link btn-sm text-primary p-0 text-decoration-none small" id="btnEditClaimingDate" title="Change claiming date">
                                    <i class="fas fa-pen-to-square ms-1"></i> Change
                                </button>
                            </div>
                            <!-- Inline Claiming Date Picker (Hidden by default) -->
                            <div class="input-group input-group-sm mt-2 d-none" id="smsInlineDatePickerGroup">
                                <input type="date" class="form-control form-control-sm rounded-start-3" id="smsInlineClaimingDateInput">
                                <button class="btn btn-outline-primary" type="button" id="btnSaveInlineClaimingDate">Apply</button>
                                <button class="btn btn-outline-secondary" type="button" id="btnCancelInlineClaimingDate">Cancel</button>
                            </div>
                            <!-- Warning if claiming date is missing -->
                            <div class="text-danger small mt-1 d-none" id="smsMissingClaimingDateAlert">
                                <i class="fas fa-triangle-exclamation me-1"></i> No claiming date assigned. Please assign a date before sending unclaimed assistance notification.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Formulation Section -->
                <form id="smsComposeForm" onsubmit="return false;">
                    <input type="hidden" id="smsIntakeId" name="intake_id" value="">
                    <input type="hidden" id="smsRecipientNumber" name="recipient_contact_number" value="">
                    <input type="hidden" id="smsClaimingDateHidden" name="claiming_date" value="">

                    <!-- Message Type Selector Tabs -->
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <label class="form-label small fw-bold text-dark mb-0">
                            <i class="fas fa-comment-dots text-primary me-1"></i> Message Template & Content
                        </label>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Message template selector">
                            <button type="button" class="btn btn-outline-primary active btn-template-select" data-type="Unclaimed Assistance">
                                <i class="fas fa-clock me-1"></i> Unclaimed Notice
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-template-select" data-type="Follow-up">
                                <i class="fas fa-redo me-1"></i> Follow-up
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-template-select" data-type="Custom Message">
                                <i class="fas fa-pen me-1"></i> Custom
                            </button>
                        </div>
                    </div>

                    <!-- Message Textarea -->
                    <div class="mb-2">
                        <textarea class="form-control rounded-3 border" id="smsMessageBody" name="message" rows="4" 
                            placeholder="Write message to beneficiary..." style="resize: vertical; font-size: 0.92rem;"></textarea>
                    </div>

                    <!-- Character and Segment Counter -->
                    <div class="d-flex justify-content-between align-items-center text-muted small px-1">
                        <div>
                            <span id="smsCharCount" class="fw-semibold text-dark">0</span> characters
                            <span class="text-muted">|</span>
                            <span id="smsSegmentCount" class="fw-semibold text-primary">1</span> SMS segment(s)
                        </div>
                        <div>
                            <button type="button" class="btn btn-link btn-sm text-secondary p-0 text-decoration-none" id="btnResetDefaultMessage">
                                <i class="fas fa-rotate-left me-1"></i> Reset to Default Template
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Message History Accordion/Section -->
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark small">
                            <i class="fas fa-history text-muted me-1"></i> Messaging History
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1" id="smsHistoryCountBadge">0</span>
                        </span>
                        <button class="btn btn-sm btn-link text-decoration-none p-0 small" type="button" data-bs-toggle="collapse" data-bs-target="#smsHistoryCollapse" aria-expanded="false" aria-controls="smsHistoryCollapse">
                            <span id="smsHistoryToggleText">Show History</span> <i class="fas fa-chevron-down ms-1 text-2xs"></i>
                        </button>
                    </div>

                    <div class="collapse mt-2" id="smsHistoryCollapse">
                        <div class="table-responsive rounded-3 border" style="max-height: 200px;">
                            <table class="table table-sm table-hover mb-0 align-middle small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Message Preview</th>
                                        <th>Type</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="smsHistoryTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No messages sent yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light py-2.5 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-xs fw-semibold" id="btnSendSmsSubmit" style="background: #1A237E; border-color: #1A237E;">
                    <span class="spinner-border spinner-border-sm me-1.5 d-none" id="smsSendSpinner" role="status" aria-hidden="true"></span>
                    <i class="fas fa-paper-plane me-1.5" id="smsSendIcon"></i>
                    <span id="smsSendBtnText">Send Message</span>
                </button>
            </div>
        </div>
    </div>
</div>
