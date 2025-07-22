<div class="row mt-4">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <div style="box-shadow:3px 3px 3px #ccc; border:1px solid #ccc;padding:20px;width:100%;text-align:center;">
            <h6>Are you sure you want to delete timesheet entry below:</h6>
            <table class="table table-bordered">
                <tbody class="text-start">
                    <tr>
                        <th>Task Number</th>
                        <td><?php echo $timesheet->taskNumber;?></td>
                    </tr>
                    <tr>
                        <th>Task Name</th>
                        <td><?php echo $timesheet->taskName;?></td>
                    </tr>
                    <tr>
                        <th>Task Section</th>
                        <td><?php echo $timesheet->taskSection;?></td>
                    </tr>
                    <tr>
                        <th>Sprint</th>
                        <td><?php echo $timesheet->sprintName;?></td>
                    </tr>
                    <tr>
                        <th>Project</th>
                        <td><?php echo $timesheet->projectName;?></td>
                    </tr>
                    <tr>
                        <th>Customer</th>
                        <td><?php echo $timesheet->customerName;?></td>
                    </tr>
                    <tr>
                        <th>Start Time</th>
                        <td><?php echo $timesheet->start_time;?></td>
                    </tr>
                    <tr>
                        <th>Finish Time</th>
                        <td><?php echo $timesheet->finish_time;?></td>
                    </tr>
                    <tr>
                        <th>Duration</th>
                        <td><?php echo $timesheet->duration_minutes;?></td>
                    </tr>
                </tbody>
            </table>
            <a href="portal/developers/timesheets">
                <div class="btn btn-info"><i class="fa fa-times"></i> Cancel</div>
            </a>
            <a href="portal/developers/delete_timesheet/<?php echo $timesheet->id;?>/confirm">
                <div class="btn btn-danger"><i class="fa fa-trash"></i> Delete</div>
            </a>
        </div>
    </div>
</div>