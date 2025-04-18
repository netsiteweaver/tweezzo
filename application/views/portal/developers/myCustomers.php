        <div class="row table-responsive">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <p>Click on a Customer to view all tasks associated to it.</p>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>MY CUSTOMERS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($myCustomers as $row):?>
                        <tr>
                            <td><a href="portal/developers/tasks?customer_id=<?php echo $row->customer_id;?>"><?php echo $row->company_name;?></a></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
