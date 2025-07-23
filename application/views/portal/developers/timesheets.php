<style>
    #my-timesheet a{
        color: #4c4c4c !important;
        font-style: italic;
        cursor:pointer;
    }
</style>
<form action="">
    <div class="row">
        <div class="col-md-2">
            <label for="">Customer</label>
            <select name="customer_id" id="" class="form-control" disabled>
                <option value="">Click on Customer</option>
                <?php foreach($myCustomers as $c):?>
                <option value="<?php echo $c->customer_id;?>" <?php echo $c->customer_id == $this->input->get("customer_id") ? "selected" : "";?>><?php echo $c->company_name;?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="">Project</label>
            <select name="project_id" id="" class="form-control" disabled>
                <option value="">Click on a Project</option>
                <?php foreach($myProjects as $c):?>
                <option value="<?php echo $c->id;?>" <?php echo $c->id == $this->input->get("project_id") ? "selected" : "";?>><?php echo "{$c->name} [{$c->company_name}]";?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="">Sprint</label>
            <select name="sprint_id" id="" class="form-control" disabled>
                <option value="">Clict on a Sprint</option>
                <?php foreach($mySprints as $c):?>
                <option value="<?php echo $c->id;?>" <?php echo $c->id == $this->input->get("sprint_id") ? "selected" : "";?>><?php echo "{$c->name} [{$c->project_name} &#x2016 {$c->company_name}]";?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-success"><i class="fa fa-check"></i> Apply</button>
        </div>
    </div>
</form>
        <div class="mt-4 row table-responsive">
            <div class="col-md-12">
                <table id='my-timesheet' class="table table-bordered table-hover">
                    <thead>
                        <tr class='text-center'>
                            <th colspan='3'>TASK</th>
                            <th colspan='3'>OTHER DETAILS</th>
                            <th rowspan='2'>NOTES</th>
                            <th colspan='3'>TIME</th>
                            <th rowspan='2'></th>
                        </tr>
                        <tr class='text-center'>
                            <th>#</th>
                            <th>TASK</th>
                            <th>SECTION</th>
                            <th>SPRINT</th>
                            <th>PROJECT</th>
                            <th>CUSTOMER</th>
                            <th>START</th>
                            <th>FINISH</th>
                            <th>DURATION (M)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totalSeconds = 0;?>
                        <?php foreach($rows as $row):?>
                        <tr class="text-center" data-id="<?php echo $row->id;?>" data-task-uuid = "<?php echo $row->taskUuid;?>">
                            <td class='text-start'><?php echo "{$row->taskNumber}";?></td>
                            <td class='text-start'><?php echo "{$row->taskName}";?></td>
                            <td class='text-start'><?php echo "{$row->taskSection}";?></td>
                            <td>
                                <a href="portal/developers/timesheets?sprint_id=<?php echo $row->sprintId;?>">
                                    <?php echo "{$row->sprintName}";?>
                                </a>
                            </td>
                            <td>
                                <a href="portal/developers/timesheets?project_id=<?php echo $row->projectId;?>">
                                    <?php echo "{$row->projectName}";?>
                                </a>
                                
                            </td>
                            <td>
                                <a href="portal/developers/timesheets?customer_id=<?php echo $row->customerId;?>">
                                    <?php echo "{$row->customerName}";?>
                                </a>
                            </td>
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

                                    $start = new DateTime($row->start_time);
                                    $finish = new DateTime($row->finish_time);
                                    $interval = $start->diff($finish);

                                    $hours = ($interval->days * 24) + $interval->h;
                                    $minutes = $interval->i;
                                    // printf('%02d:%02d', $hours, $minutes);
                                } else {
                                    echo '-';
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
                            <th colspan='8'>TOTAL TIME</th>
                            <th class="text-center">
                                <?php
                                    printf('%02d days %02d:%02d', $totalDays, $totalHours, $totalMinutes);
                                ?>
                            </th>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </div>
