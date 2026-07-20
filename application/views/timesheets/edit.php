<?php
$returnSuffix = !empty($return_qs) ? '?return=' . rawurlencode($return_qs) : '';
$cancelUrl = base_url('timesheets/listing' . (!empty($return_qs) ? '?' . $return_qs : ''));
$startVal = !empty($timesheet->start_time) ? date('Y-m-d H:i', strtotime($timesheet->start_time)) : '';
$finishVal = !empty($timesheet->finish_time) ? date('Y-m-d H:i', strtotime($timesheet->finish_time)) : '';
?>
<div class="row">
    <div class="col-md-8">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Edit timesheet entry</h3>
            </div>
            <form method="post" action="<?php echo base_url('timesheets/edit/' . (int) $timesheet->id . $returnSuffix); ?>">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Task</label>
                            <div class="form-control" style="height:auto;">
                                <?php echo htmlspecialchars((!empty($timesheet->taskRef) ? $timesheet->taskRef : $timesheet->taskNumber) . ' — ' . $timesheet->taskName); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Developer</label>
                            <div class="form-control" style="height:auto;">
                                <?php echo htmlspecialchars($timesheet->developerName . ' (' . $timesheet->developerEmail . ')'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Customer</label>
                            <div class="form-control"><?php echo htmlspecialchars($timesheet->customerName); ?></div>
                        </div>
                        <div class="col-md-4">
                            <label>Project</label>
                            <div class="form-control"><?php echo htmlspecialchars($timesheet->projectName); ?></div>
                        </div>
                        <div class="col-md-4">
                            <label>Sprint</label>
                            <div class="form-control"><?php echo htmlspecialchars($timesheet->sprintName); ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_time">Start time <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    </div>
                                    <input type="text" class="form-control timesheet-datetime" name="start_time" id="start_time" value="<?php echo htmlspecialchars($startVal); ?>" placeholder="YYYY-MM-DD HH:mm" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="finish_time">Finish time <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    </div>
                                    <input type="text" class="form-control timesheet-datetime" name="finish_time" id="finish_time" value="<?php echo htmlspecialchars($finishVal); ?>" placeholder="YYYY-MM-DD HH:mm" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Duration</label>
                        <div class="form-control bg-light" id="timesheet-duration-preview">—</div>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" name="notes" id="notes" rows="4"><?php echo htmlspecialchars($timesheet->notes); ?></textarea>
                    </div>
                    <p class="text-muted mb-0">Duration is recalculated automatically from start and finish when you save.</p>
                </div>
                <div class="card-footer">
                    <a href="<?php echo $cancelUrl; ?>" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Cancel</a>
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
