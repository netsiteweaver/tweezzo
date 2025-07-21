        <div class="mt-4 row table-responsive">
            <div class="col-md-12">
                <table id='my-timesheet' class="table table-bordered table-hover">
                    <thead>
                        <tr class='text-center'>
                            <th>TASK</th>
                            <th>SPRINT</th>
                            <th>PROJECT</th>
                            <th>CUSTOMER</th>
                            <th>START</th>
                            <th>FINISH</th>
                            <th>DURATION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totalSeconds = 0;?>
                        <?php foreach($rows as $row):?>
                        <tr class="text-center" data-id="<?php echo $row->id;?>" data-task-uuid = "<?php echo $row->taskUuid;?>">
                            <td class='text-start'><?php echo "{$row->taskNumber} &#x2016; {$row->taskName} &#x2016; {$row->taskSection}";?></td>
                            <td class=''><?php echo "{$row->sprintName}";?></td>
                            <td class=''><?php echo "{$row->projectName}";?></td>
                            <td class=''><?php echo "{$row->customerName}";?></td>
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
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                    <tfoot>
                        <?php
                        $totalHours = floor($totalSeconds / 3600);
                        $totalMinutes = floor( ($totalSeconds % 3600) / 60);
                        ?>
                        <tr>
                            <th colspan='6'>TOTAL TIME IN HOURS</th>
                            <th class="text-center"><?php printf('%02d:%02d', $totalHours, $totalMinutes); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
