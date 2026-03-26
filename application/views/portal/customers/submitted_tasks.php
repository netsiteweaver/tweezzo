<div class="row mt-4 table-responsive">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Submitted On</th>
                    <th>Submitted By</th>
                    <th>Section</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Converted Task</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($submitted_tasks)): ?>
                    <?php foreach ($submitted_tasks as $submitted_task): ?>
                    <tr data-task-uuid="<?php echo htmlspecialchars((string)$submitted_task->uuid, ENT_QUOTES, 'UTF-8'); ?>">
                        <td><?php echo htmlspecialchars($submitted_task->created_on); ?></td>
                        <td><?php echo htmlspecialchars(!empty($submitted_task->submitted_by) ? $submitted_task->submitted_by : 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($submitted_task->section); ?></td>
                        <td><?php echo htmlspecialchars($submitted_task->name); ?></td>
                        <td>
                            <?php
                            $badge_class = 'warning';
                            if ($submitted_task->stage === 'validated') {
                                $badge_class = 'success';
                            } elseif ($submitted_task->stage === 'rejected') {
                                $badge_class = 'danger';
                            }
                            ?>
                            <span class="badge bg-<?php echo $badge_class; ?>">
                                <?php echo strtoupper(str_replace('_', ' ', $submitted_task->stage)); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($submitted_task->converted_task_uuid)): ?>
                                <a href="<?php echo base_url('portal/customers/view?task_uuid=' . rawurlencode($submitted_task->converted_task_uuid)); ?>">
                                    <?php echo htmlspecialchars(!empty($submitted_task->converted_task_ref) ? $submitted_task->converted_task_ref : 'View'); ?>
                                </a>
                            <?php else: ?>
                                &mdash;
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary view-submitted-task"
                                data-bs-toggle="modal"
                                data-bs-target="#submittedTaskDetailsModal"
                                data-submitted-on="<?php echo htmlspecialchars((string)$submitted_task->created_on, ENT_QUOTES, 'UTF-8'); ?>"
                                data-submitted-by="<?php echo htmlspecialchars((string)(!empty($submitted_task->submitted_by) ? $submitted_task->submitted_by : 'N/A'), ENT_QUOTES, 'UTF-8'); ?>"
                                data-section="<?php echo htmlspecialchars((string)$submitted_task->section, ENT_QUOTES, 'UTF-8'); ?>"
                                data-task-name="<?php echo htmlspecialchars((string)$submitted_task->name, ENT_QUOTES, 'UTF-8'); ?>"
                                data-description="<?php echo htmlspecialchars((string)$submitted_task->description, ENT_QUOTES, 'UTF-8'); ?>"
                                data-scope-client-expectation="<?php echo htmlspecialchars((string)$submitted_task->scope_client_expectation, ENT_QUOTES, 'UTF-8'); ?>"
                                data-scope-not-included="<?php echo htmlspecialchars((string)$submitted_task->scope_not_included, ENT_QUOTES, 'UTF-8'); ?>"
                                data-scope-when-done="<?php echo htmlspecialchars((string)$submitted_task->scope_when_done, ENT_QUOTES, 'UTF-8'); ?>"
                                data-stage="<?php echo htmlspecialchars((string)$submitted_task->stage, ENT_QUOTES, 'UTF-8'); ?>"
                                data-rejection-reason="<?php echo htmlspecialchars((string)$submitted_task->rejection_reason, ENT_QUOTES, 'UTF-8'); ?>"
                                data-task-images="<?php echo htmlspecialchars(json_encode($submitted_task->images), ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="bi bi-eye"></i> View
                            </button>
                            <?php if (!empty($submitted_task->can_delete)): ?>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger delete-submitted-task mt-1"
                                    data-task-uuid="<?php echo htmlspecialchars((string)$submitted_task->uuid, ENT_QUOTES, 'UTF-8'); ?>">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No submitted tasks yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="submittedTaskDetailsModal" tabindex="-1" aria-labelledby="submittedTaskDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submittedTaskDetailsLabel">Submitted Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-2"><strong>Submitted On:</strong> <span data-field="submitted_on"></span></div>
                        <div class="mb-2"><strong>Submitted By:</strong> <span data-field="submitted_by"></span></div>
                        <div class="mb-2"><strong>Status:</strong> <span data-field="status"></span></div>
                        <div class="mb-3"><strong>Section:</strong> <span data-field="section"></span></div>
                        <div class="mb-3"><strong>Task:</strong> <span data-field="task_name"></span></div>
                        <div class="mb-0"><strong>Description:</strong><div class="border rounded p-2 bg-light" data-field="description"></div></div>
                        <div class="mt-3">
                            <strong>Images:</strong>
                            <div data-field="task_images" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3"><strong>Expected From This Task:</strong><div class="border rounded p-2 bg-light" data-field="scope_client_expectation"></div></div>
                        <div class="mb-3"><strong>Not Included:</strong><div class="border rounded p-2 bg-light" data-field="scope_not_included"></div></div>
                        <div class="mb-3"><strong>When Considered Done:</strong><div class="border rounded p-2 bg-light" data-field="scope_when_done"></div></div>
                        <div class="mb-0 d-none" data-field-wrap="rejection_reason">
                            <strong>Rejection Reason:</strong>
                            <div class="border rounded p-2 bg-light text-danger" data-field="rejection_reason"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url("assets/js/portal/customers/submitted_tasks.js?t=".date("YmdHis"));?>"></script>
