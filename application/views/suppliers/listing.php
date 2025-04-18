<?php if($perms['add']): ?>
<div class="row">
    <div class="col-xs-1">
        <a href="<?php echo base_url("suppliers/add"); ?>"><div class="btn btn-xs btn-flat btn-success"><i class="fa fa-plus"></i> Add</div></a>
    </div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box">
        <?php if( (isset($suppliers)) && (!empty($suppliers)) ): ?>
            <div class="box-body table-responsive no-padding">
                <table id="suppliers_listing" class="table">                    
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($suppliers as $supplier): ?>
                        <tr data-id="<?php echo $supplier->uuid;?>">
                            <td><?php echo $supplier->company_name; ?></td>
                            <td><?php echo nl2br($supplier->address); ?></td>
                            <td><?php echo $supplier->email; ?></td>
                            <td><?php echo $supplier->phone_number; ?></td>
                            <td>
                                <?php if($perms['edit']): ?>
                                <a href="<?php echo base_url("suppliers/edit/".$supplier->uuid); ?>"><div class="btn btn-md btn-flat btn-primary"><i class="fa fa-edit"></i></div></a>
                                <?php endif; ?>
                                <?php if($perms['delete']) echo DeleteButton2('suppliers','uuid',$supplier->uuid,'','','',false); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p>No records</p>
            <?php endif; ?>   
        </div>
    </div>
</div>
