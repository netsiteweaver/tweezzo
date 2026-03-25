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
