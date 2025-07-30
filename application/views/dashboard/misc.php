<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead, tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed; /* keeps columns aligned */
    }

    tbody {
        display: block;
        height: 250px;    /* set your height */
        overflow-y: auto; /* enable vertical scroll */
    }

</style>

<div class="row small-text">
        <div class="col-md-6 table-responsive">
            <h4 class='text-center'>Latest Back Office Access</h4>
            <div class="div">
                <table id="latest_logins_table" class="table table-bordered table-hover">
                    <thead>
                        <tr class='text-center bg-yellow'>
                            <th>DATE</th>
                            <th>USERNAME / EMAIL</th>
                            <th>OS</th>
                            <th>RESULT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($latest_logins as $row):?>
                        <tr title="<?php echo $row->ip . " | " . $row->os . " | " . $row->browser . " | " . $row->result_other;?>" class='text-center <?php echo (($row->result=='SUCCESS')?'':'red');?>' >
                            <td><?php echo $row->datetime;?></td>
                            <td><?php echo $row->username . (!empty($row->email)?' / '.$row->email:'');?></td>
                            <td><?php echo $row->os;?></td>
                            <td><?php echo $row->result;?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6 d-nonez">
            <h4 class='text-center'>Overall Progress per Client</h4>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr class='text-center'>
                        <th class='table-primary' style='width:16.667%;'>COMPANY</th>
                        <th class='table-primary'>TOTAL TASKS</th>
                        <th class='table-primary'>COMPLETED</th>
                        <th class='table-primary'>%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($task_progress as $c => $item):?>
                    <tr class='text-center'>
                        <td><?php echo $item->company_name;?></td>
                        <td><?php echo $item->total_tasks;?></td>
                        <td><?php echo $item->completed_tasks;?></td>
                        <!-- <td><?php //echo ceil($item->overall_progress_pct);?>%</td> -->
                        <td style='width:100px'>
                            <div class="progress position-relative" style="height: 20px;">
                                <div 
                                class="progress-bar 
                                        <?php 
                                        if ($item->overall_progress_pct >= 80) echo 'bg-success'; 
                                        elseif ($item->overall_progress_pct >= 50) echo 'bg-info'; 
                                        elseif ($item->overall_progress_pct >= 20) echo 'bg-warning'; 
                                        else echo 'bg-danger'; 
                                        ?>" 
                                role="progressbar" 
                                style="width: <?= $item->overall_progress_pct ?>%;" 
                                aria-valuenow="<?= $item->overall_progress_pct ?>" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                </div>
                                <span class="w-100 text-center" style='position:absolute; bottom:10px;'><?= $item->overall_progress_pct ?>%</span>
                            </div>
                        </td>


                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>  

    <div class="row small-text">
        <div class="col-md-6 table-responsive">
            <h4 class='text-center'>Latest Customer Portal Access</h4>
            <div class="div">
                <table id="latest_logins_table" class="table table-bordered table-hover">
                    <thead>
                        <tr class='text-center bg-orange'>
                            <th>DATE</th>
                            <th>USERNAME / EMAIL</th>
                            <th>OS</th>
                            <th>RESULT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($latest_customer_access as $row):?>
                        <tr title="<?php echo $row->ip . " | " . $row->os . " | " . $row->browser . " | " . $row->result_other;?>" class='text-center <?php echo (($row->result=='SUCCESS')?'':'red');?>' >
                            <td><?php echo $row->datetime;?></td>
                            <td><?php echo $row->email;?></td>
                            <td><?php echo $row->os;?></td>
                            <td><?php echo $row->result;?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6 table-responsive">
            <h4 class='text-center'>Latest Developer Portal Access</h4>
            <div class="div">
                <table id="latest_logins_table" class="table table-bordered table-hover">
                    <thead>
                        <tr class='text-center bg-teal'>
                            <th>DATE</th>
                            <th>USERNAME / EMAIL</th>
                            <th>OS</th>
                            <th>RESULT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($latest_developer_access as $row):?>
                        <tr title="<?php echo $row->ip . " | " . $row->os . " | " . $row->browser . " | " . $row->result_other;?>" class='text-center <?php echo (($row->result=='SUCCESS')?'':'red');?>' >
                            <td><?php echo $row->datetime;?></td>
                            <td><?php echo $row->email;?></td>
                            <td><?php echo $row->os;?></td>
                            <td><?php echo $row->result;?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>  