        <div class="row table-responsive">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <p>Click on a Sprint to view all tasks associated to it.</p>
                <table id='mySprints' class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>SPRINTS</th>
                            <th>PROJECT</th>
                            <th>CUSTOMER</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($mySprints as $row):?>
                        <tr data-customer-id="<?php echo $row->customer_id;?>" data-project-id="<?php echo $row->project_id;?>" data-sprint-id="<?php echo $row->id;?>">
                            <td class='select-sprint cursor-pointer'><?php echo "{$row->name}";?></td>
                            <td class='select-sprint cursor-pointer'><?php echo "{$row->project_name}";?></td>
                            <td class='select-sprint cursor-pointer'><?php echo "{$row->company_name}";?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
