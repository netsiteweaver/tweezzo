<div class="row justify-content-center mb-5 mt-3">
    <div class="col-lg-9 col-md-11">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-journal-check me-2"></i>How To Validate A Task</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    Use this guide when a task reaches the <span class="stage-button stage-button-staging" style="display:inline-block; padding:2px 8px;">STAGING</span> stage.
                    Validation confirms the task is ready for production.
                </p>

                <div class="border rounded p-3 mb-4" style="background:#f8fbff;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <strong><i class="bi bi-stars text-warning me-1"></i> Validation Flow</strong>
                            <div class="text-muted small">Review -> Test -> Decide</div>
                        </div>
                        <div class="small text-center">
                            <span class="badge bg-light text-dark border">1. Inspect</span>
                            <i class="bi bi-arrow-right mx-1"></i>
                            <span class="badge bg-light text-dark border">2. Test</span>
                            <i class="bi bi-arrow-right mx-1"></i>
                            <span class="badge bg-success">3. Validate</span>
                            <span class="mx-1 text-muted">or</span>
                            <span class="badge bg-danger">Reject</span>
                        </div>
                    </div>
                </div>

                <ol class="mb-4">
                    <li class="mb-2">
                        Open <strong>Tasks</strong> and select the task to review.
                    </li>
                    <li class="mb-2">
                        Check the task details, scope, and attachments against your original requirements.
                    </li>
                    <li class="mb-2">
                        Test the feature on the staging environment and confirm expected behavior.
                    </li>
                    <li class="mb-2">
                        If everything is correct, click <strong>Validate</strong> in the task page.
                    </li>
                    <li class="mb-2">
                        If something is wrong, add a note and use <strong>Reject</strong> with a clear reason.
                    </li>
                </ol>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100 text-center">
                            <div style="font-size:30px;">🔍</div>
                            <strong>Scope Check</strong>
                            <div class="text-muted small">Confirm details match your request.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100 text-center">
                            <div style="font-size:30px;">🧪</div>
                            <strong>Staging Test</strong>
                            <div class="text-muted small">Try the feature and edge cases.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100 text-center">
                            <div style="font-size:30px;">✅ / ❌</div>
                            <strong>Final Decision</strong>
                            <div class="text-muted small">Validate if correct, reject with notes if not.</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success mb-3">
                    <strong>After validation:</strong> an email notification is sent requesting the team to push the validated task to production.
                </div>

                <div class="alert alert-info mb-0">
                    Keep rejection notes precise and linked to the task scope so the team can fix and resubmit quickly.
                </div>

                <div class="mt-4">
                    <a href="<?php echo base_url('portal/customers/tasks?stages=staging'); ?>" class="btn btn-primary">
                        <i class="bi bi-funnel-fill me-1"></i>View all staging tasks
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
