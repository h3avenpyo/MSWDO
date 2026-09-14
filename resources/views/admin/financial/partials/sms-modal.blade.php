<!-- Financial Assistance Step 2 - SMS Messaging Modal -->
<div class="modal fade" id="smsMessagingModal" tabindex="-1" aria-labelledby="smsMessagingModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header text-white py-3 px-4" style="background-color: #1A237E !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(255, 255, 255, 0.15); flex-shrink: 0;">
                        <i class="fas fa-paper-plane text-white fs-6"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title fw-bold mb-0 text-white" id="smsMessagingModalLabel">Send SMS Notification</h5>
                        </div>
                        <span class="text-white-50 small" style="font-size: 0.78rem;">Financial Assistance Step 2 Notification System</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <!-- Missing Contact Warning Banner (Shown only when contact is missing or invalid) -->
                <div id="smsNoContactAlert" class="alert alert-danger d-flex align-items-center rounded-3 mb-3 d-none" role="alert">
                    <i class="fas fa-exclamation-triangle fs-5 me-2"></i>
                    <div>
                        <strong id="smsNoContactTitle">No contact number is available for this beneficiary.</strong>
                        <div class="small" id="smsNoContactSubtitle">SMS messaging requires a valid Philippine mobile number (e.g. 09XXXXXXXXX or +63XXXXXXXXXX).</div>
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
                    <!-- Top Row: Beneficiary / Recipient & Contact Number -->
                    <div class="row g-3 align-items-start">
                        <!-- Beneficiary / Recipient -->
                        <div class="col-md-6 col-12">
                            <span class="text-muted small text-uppercase fw-semibold d-block mb-1" style="font-size: 0.74rem; letter-spacing: 0.04em;">Beneficiary / Recipient</span>
                            <div class="fw-bold text-dark fs-6" id="smsModalBeneficiaryName">--</div>
                            <div class="text-muted small mt-0.5" id="smsModalRepContainer">
                                <span class="badge bg-info-subtle text-info border border-info-subtle text-2xs rounded-pill" id="smsModalRepBadge">Representative</span>
                                <span class="ms-1" id="smsModalRepName">--</span>
                            </div>
                        </div>

                        <!-- Contact Number -->
                        <div class="col-md-6 col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.04em;">Contact Number</span>
                                <button type="button" class="btn btn-link btn-sm text-primary p-0 text-decoration-none" id="btnEditContactNumber" style="font-size: 0.78rem;" title="Change or update contact number">
                                    <i class="fas fa-pen-to-square me-1"></i> Change
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-bold text-dark font-monospace fs-6" id="smsModalContactNumber">--</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 px-2" id="smsContactStatusBadge" style="font-size: 0.72rem; font-weight: 500;">
                                    <i class="fas fa-check-circle me-0.5"></i> Valid (PH +63)
                                </span>
                            </div>
                            <div class="text-muted mt-1 d-none" id="smsModalConvertedContainer" style="font-size: 0.76rem;">
                                <span class="text-secondary">Sends via SMS as:</span>
                                <span class="fw-semibold text-primary font-monospace ms-1" id="smsModalConvertedNumber">+63XXXXXXXXXX</span>
                            </div>
                            <!-- Inline Contact Number Editor (Hidden by default) -->
                            <div class="input-group input-group-sm mt-2 d-none" id="smsInlineContactGroup">
                                <input type="text" class="form-control form-control-sm rounded-start font-monospace" id="smsInlineContactInput" placeholder="09XXXXXXXXX or +63XXXXXXXXXX">
                                <button class="btn btn-outline-primary btn-sm" type="button" id="btnSaveInlineContact">Apply</button>
                                <button class="btn btn-outline-secondary btn-sm" type="button" id="btnCancelInlineContact">Cancel</button>
                            </div>
                            <div class="text-danger small mt-1 d-none" id="smsInvalidContactAlert" style="font-size: 0.75rem;">
                                <i class="fas fa-triangle-exclamation me-1"></i> Invalid Philippine mobile number (e.g. 09XXXXXXXXX or +639XXXXXXXXX).
                            </div>
                        </div>
                    </div>

                    <!-- Clean Horizontal Divider -->
                    <hr class="my-2.5 text-muted opacity-25">

                    <!-- Bottom Row: Assistance / Purpose & Claiming Date -->
                    <div class="row g-3 align-items-start">
                        <!-- Assistance / Purpose -->
                        <div class="col-md-6 col-12">
                            <span class="text-muted small text-uppercase fw-semibold d-block mb-1" style="font-size: 0.74rem; letter-spacing: 0.04em;">Assistance / Purpose</span>
                            <div class="text-dark small fw-medium text-truncate" id="smsModalPurpose">--</div>
                            <div class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill text-xs fw-bold mt-1" id="smsModalAmount">₱0.00</div>
                        </div>

                        <!-- Claiming Date -->
                        <div class="col-md-6 col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted small text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.04em;">Claiming Date</span>
                                <button type="button" class="btn btn-link btn-sm text-primary p-0 text-decoration-none" id="btnEditClaimingDate" style="font-size: 0.78rem;" title="Change claiming date">
                                    <i class="fas fa-pen-to-square me-1"></i> Change
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 fs-6 text-dark mt-0.5">
                                <i class="fas fa-calendar-day text-primary me-1"></i>
                                <span class="fw-bold" id="smsModalClaimingDate">--</span>
                            </div>
                            <!-- Inline Claiming Date Picker (Hidden by default) -->
                            <div class="input-group input-group-sm mt-2 d-none" id="smsInlineDatePickerGroup">
                                <input type="date" class="form-control form-control-sm rounded-start" id="smsInlineClaimingDateInput">
                                <button class="btn btn-outline-primary btn-sm" type="button" id="btnSaveInlineClaimingDate">Apply</button>
                                <button class="btn btn-outline-secondary btn-sm" type="button" id="btnCancelInlineClaimingDate">Cancel</button>
                            </div>
                            <!-- Warning if claiming date is missing -->
                            <div class="text-danger small mt-1 d-none" id="smsMissingClaimingDateAlert" style="font-size: 0.75rem;">
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
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-xs fw-semibold btn-brand-primary" id="btnSendSmsSubmit">
                    <span class="spinner-border spinner-border-sm me-1.5 d-none" id="smsSendSpinner" role="status" aria-hidden="true"></span>
                    <i class="fas fa-paper-plane me-1.5" id="smsSendIcon"></i>
                    <span id="smsSendBtnText">Send Message</span>
                </button>
            </div>
        </div>
    </div>
</div>
