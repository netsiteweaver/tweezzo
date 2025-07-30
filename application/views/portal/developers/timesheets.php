<style>
#my-timesheet a.underline {
    border-bottom: 1px solid #00d5ff77 !important;
    cursor: pointer;
}
#my-timesheet a.underline:hover {
    border-bottom: 3px solid #00d5ffee !important;
}
</style>
<form action="" class='mt-5'>
    <div class="row">
        <div class="col-md-2">
            <label for="">From</label>
            <input class='form-control' name="from" type="date" value="<?php echo (!empty($this->input->get("from"))) ? $this->input->get("from") : date('Y-m-01');?>">
        </div>
        <div class="col-md-2">
            <label for="">To</label>
            <input class='form-control' name="to" type="date" value="<?php echo (!empty($this->input->get("to"))) ? $this->input->get("to") : date('Y-m-t');?>">
        </div>
        <div class="col-md-2 d-none">
            <label for="">By</label>
            <select name="method" id="" class="form-select">
                <option value="start_time" <?php echo ($this->input->get("method") == 'start_time') ? 'selected' : '';?>>Start Date</option>
                <option value="finish_time" <?php echo ($this->input->get("method") == 'finish_time') ? 'selected' : '';?>>Finish Date</option>
            </select>
        </div>
        <div class='col-md-2'>
            <label for="">Customer</label>
            <select name="customer_id" id=""
                class="form-select <?php echo (!empty($this->input->get("customer_id")))?'bg-info text-white':'';?>">
                <option value="">Select Customer</option>
                <?php foreach($myCustomers as $c):?>
                <option value="<?php echo $c->customer_id;?>"
                    <?php echo $c->customer_id == $this->input->get("customer_id") ? "selected" : "";?>>
                    <?php echo $c->company_name;?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class='col-md-2 d-none'>
            <label for="">Sprint</label>
            <select name="sprint_id" id=""
                class="form-select <?php echo (!empty($this->input->get("sprint_id")))?'bg-info text-white':'';?>">
                <option value="">Select Sprint</option>
                <?php foreach($mySprints as $c):?>
                <option value="<?php echo $c->id;?>"
                    <?php echo $c->id == $this->input->get("sprint_id") ? "selected" : "";?>>
                    <?php echo "{$c->name} [{$c->project_name} ‖ {$c->company_name}]";?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class='col-md-2 d-none'>
            <label for="">Project</label>
            <select name="project_id" id=""
                class="form-select <?php echo (!empty($this->input->get("project_id")))?'bg-info text-white':'';?>">
                <option value="">Select Project</option>
                <?php foreach($myProjects as $c):?>
                <option value="<?php echo $c->id;?>"
                    <?php echo $c->id == $this->input->get("project_id") ? "selected" : "";?>>
                    <?php echo "{$c->name} [{$c->company_name}]";?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-md-1" style='margin-top:34px;'>
            <button class="btn btn-info">Apply</button>
        </div>
    </div>
</form>

<div class="mt-4 row table-responsive">
    <div class="col-md-12">
        <table id='my-timesheet' class="table table-bordered table-hover">
            <thead>
                <tr class='text-center'>
                    <th colspan='3'>TASK INFORMATION</th>
                    <th colspan='3'>OTHER DETAILS</th>
                    <th rowspan='2'>NOTES</th>
                    <th colspan='3'>TIME</th>
                    <th rowspan='2'></th>
                </tr>
                <tr class='text-center'>
                    <th>#</th>
                    <th>TASK</th>
                    <th>SECTION</th>
                    <th class="<?php echo (!empty($this->input->get("sprint_id")))?'bg-info text-white':'';?>">SPRINT</th>
                    <th class="<?php echo (!empty($this->input->get("project_id")))?'bg-info text-white':'';?>">PROJECT</th>
                    <th class="<?php echo (!empty($this->input->get("customer_id")))?'bg-info text-white d-none':'';?>">CUSTOMER</th>
                    <th>START</th>
                    <th>FINISH</th>
                    <th>DURATION (H)</th>
                </tr>
            </thead>
            <tbody>
                <?php $totalSeconds = 0;?>
                <?php foreach($rows as $row):?>
                <tr class="text-center" data-id="<?php echo $row->id;?>" data-task-uuid="<?php echo $row->taskUuid;?>">
                    <td class='text-start'><a class='underline' href="portal/developers/view?task_uuid=<?php echo $row->taskUuid;?>"><?php echo "{$row->taskNumber}";?></a></td>
                    <td class='text-start'><a class='underline' href="portal/developers/view?task_uuid=<?php echo $row->taskUuid;?>"><?php echo "{$row->taskName}";?></a></td>
                    <td class='text-start'><a class='underline' href="portal/developers/view?task_uuid=<?php echo $row->taskUuid;?>"><?php echo "{$row->taskSection}";?></a></td>
                    <td><a class='underline' href="portal/developers/timesheets?sprint_id=<?php echo $row->sprintId;?>"><?php echo "{$row->sprintName}";?></a></td>
                    <td><a class='underline' href="portal/developers/timesheets?project_id=<?php echo $row->projectId;?>"><?php echo "{$row->projectName}";?></a></td>
                    <td class='<?php echo (!empty($this->input->get("customer_id")))?'bg-info text-white d-none':'';?>'><a class='underline' href="portal/developers/timesheets?customer_id=<?php echo $row->customerId;?>"><?php echo "{$row->customerName}";?>
                    </a></td>
                    <td class=''><?php echo "{$row->notes}";?></td>
                    <td class=''><?php echo "{$row->start_time}";?></td>
                    <td class=''><?php echo "{$row->finish_time}";?></td>
                    <td class=''>
                        <?php 
                                if (!empty($row->start_time) && !empty($row->finish_time)) {
                                    $ts1 = strtotime($row->start_time);
                                    $ts2 = strtotime($row->finish_time);
                                    $diff = $ts2-$ts1;

                                    $totalSeconds += $diff;

                                    $hours = floor($diff / 3600);
                                    $minutes = floor(($diff % 3600) / 60);
                                    printf('%02d:%02d', $hours, $minutes);
                                } else {
                                    //echo '-';
                                }
                                ?>
                    </td>
                    <td>
                        <a href="portal/developers/delete_timesheet/<?php echo $row->id;?>">
                            <div class="btn btn-danger"><i class="fa fa-trash"></i></div>
                        </a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
            <tfoot>
                <?php
                    $totalDays = floor($totalSeconds / 86400); // 1 day = 86400 seconds
                    $remainingSeconds = $totalSeconds % 86400;

                    $totalHours = floor($remainingSeconds / 3600);
                    $remainingSeconds %= 3600;

                    $totalMinutes = floor($remainingSeconds / 60);
                ?>
                <tr>
                    <th colspan='9'>TOTAL TIME</th>
                    <th class="text-center">
                        <?php if(intval($totalDays)>0){
                            printf('%02d days %02d:%02d', $totalDays, $totalHours, $totalMinutes);
                        }else{
                            printf('%02d:%02d', $totalHours, $totalMinutes);
                        }?>
                    </th>
                </tr>
            </tfoot>

        </table>
    </div>
</div>