<?php
$returnQs = !empty($return_qs) ? $return_qs : '';
$cancelUrl = base_url('timesheets/listing' . ($returnQs !== '' ? '?' . $returnQs : ''));
$confirmUrl = base_url('timesheets/delete/' . (int) $timesheet->id . '/confirm' . ($returnQs !== '' ? '?return=' . rawurlencode($returnQs) : ''));
?>
<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Delete timesheet entry</h3>
            </div>
            <div class="card-body">
                <p>Are you sure you want to delete this timesheet entry?</p>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width:35%;">Code</th>
                            <td><?php echo htmlspecialchars(!empty($timesheet->taskRef) ? $timesheet->taskRef : $timesheet->taskNumber); ?></td>
                        </tr>
                        <tr>
                            <th>Task</th>
                            <td><?php echo htmlspecialchars($timesheet->taskName); ?></td>
                        </tr>
                        <tr>
                            <th>Developer</th>
                            <td><?php echo htmlspecialchars($timesheet->developerName . ' (' . $timesheet->developerEmail . ')'); ?></td>
                        </tr>
                        <tr>
                            <th>Customer</th>
                            <td><?php echo htmlspecialchars($timesheet->customerName); ?></td>
                        </tr>
                        <tr>
                            <th>Project / Sprint</th>
                            <td><?php echo htmlspecialchars($timesheet->projectName . ' / ' . $timesheet->sprintName); ?></td>
                        </tr>
                        <tr>
                            <th>Start</th>
                            <td><?php echo htmlspecialchars($timesheet->start_time); ?></td>
                        </tr>
                        <tr>
                            <th>Finish</th>
                            <td><?php echo htmlspecialchars($timesheet->finish_time); ?></td>
                        </tr>
                        <tr>
                            <th>Duration (min)</th>
                            <td><?php echo htmlspecialchars((string) $timesheet->duration_minutes); ?></td>
                        </tr>
                        <tr>
                            <th>Notes</th>
                            <td><?php echo nl2br(htmlspecialchars($timesheet->notes)); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="<?php echo $cancelUrl; ?>" class="btn btn-warning"><i class="fa fa-times"></i> Cancel</a>
                <a href="<?php echo $confirmUrl; ?>" class="btn btn-danger float-right"><i class="fa fa-trash"></i> Confirm delete</a>
            </div>
        </div>
    </div>
</div>
