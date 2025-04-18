        <div class="row table-responsive">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <p>Click on a Projects to view all tasks associated to it.</p>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>PROJECT</th>
                            <th>CUSTOMER</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($myProjects as $row):?>
                        <tr>
                            <td><a href="portal/developers/tasks?project_id=<?php echo $row->id;?>"><?php echo "{$row->name}";?></a></td>
                            <td><a href="portal/developers/tasks?project_id=<?php echo $row->id;?>"><?php echo "{$row->company_name}";?></a></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
