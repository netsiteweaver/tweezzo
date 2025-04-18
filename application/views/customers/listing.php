<form action="">
<div class="row">
    <?php if ($perms['add']) : ?>
    <div class="col-3 col-sm-3 col-md-1 mt-4">
        <a href="<?php echo base_url("customers/add?referer=customers/listing/".$this->uri->segment(3)); ?>">
            <button type="button" class="btn btn-success btn-block"><i class="fa fa-plus"></i> Add</button>
        </a>
    </div>
    <?php endif; ?>
    <div class="col-3 col-md-2">
        <label for="">Display</label>
        <select class="form-control" name="display" id="rpp">
            <option value="">Select</option>
            <option value="10" <?php echo ( (empty($rows_per_page)) || ($rows_per_page == 10) ) ? 'selected':'';?>>10 rows</option>
            <option value="25" <?php echo ($rows_per_page == 25) ? 'selected':'';?>>25 rows</option>
            <option value="50" <?php echo ($rows_per_page == 50) ? 'selected':'';?>>50 rows</option>
            <option value="100" <?php echo ($rows_per_page == 100) ? 'selected':'';?>>100 rows</option>
        </select>
    </div>
    <div class="col-3 col-sm-3 col-md-2">
        <label for="">Search</label>
        <div class="input-group">
            <input type="search" name="search_text" id="search_text" class="form-control" placeholder="Search Order" value="<?php echo $this->input->get("search_text");?>">
            <div class="input-group-text clear-search cursor-pointer"><i class="fa fa-times"></i></div>
        </div>
    </div>
    <div class="col-3 col-sm-3 col-md-2 mt-4">
        <button class="btn btn-info btn-block"><i class="fa fa-check"></i> Apply</button>
    </div>
</div>
</form>
<?php if( (isset($pagination)) && (!empty($pagination)) ) echo $pagination;?>
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box">
            <?php if ((isset($customers)) && (!empty($customers))) : ?>
                <div class="box-body table-responsive no-padding">
                    <table id="customers_listing" class="table">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $customer) : ?>
                                <tr data-uuid="<?php echo $customer->uuid; ?>">
                                    <td><?php echo $customer->company_name;?></td>
                                    <td><?php echo $customer->full_name; ?></td>
                                    <td><?php echo $customer->address; ?></td>
                                    <td><?php echo $customer->email; ?></td>
                                    <td>
                                        <?php if ($perms['view']) : ?>
                                        <a href="<?php echo base_url("tasks/listing?customer_id=" . $customer->customer_id); ?>"><div class="btn btn-default"><i class="fa fa-bars"></i></div></a>
                                        <?php endif;?>
                                        <?php if ($perms['edit']) : ?>
                                            <a href="<?php echo base_url("customers/edit/" . $customer->uuid."?referer=customers/listing/".$this->uri->segment(3,1)); ?>">
                                                <div class="btn btn-md btn-primary"><i class="fa fa-edit"></i></div>
                                            </a>
                                        <?php endif; ?>
                                        <!-- <?php if($perms['delete']) //echo DeleteButton2('customers','uuid',$customer->uuid,'','','',false); ?> -->
                                        <?php if($perms['delete']):?>
                                            <div class="btn btn-danger deleteCustomer"><i class="fa fa-trash"></i></div>
                                        <?php endif;?>
                                        <!-- <div class="btn bg-orange resetPassword"><i class="fa fa-lock"></i></div> -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            <?php else : ?>
                <p>No records</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if( (isset($pagination)) && (!empty($pagination)) ) echo $pagination;?>
<!-- Modal Notes-->
<div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPasswordTitle">Password Change</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Password</label>
                    <input type="text" class="form-control" id="password" name="password" placeholder="Password">
                </div>
                <div class="form-group">
                    <label for="">Confirm Password</label>
                    <input type="text" class="form-control" id="password" name="password" placeholder="Password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                    Close</button>
                <button type="button" class="btn btn-primary setDueDate"><i class="fa fa-check"></i> Proceed</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Notes-->
<div class="modal fade" id="modalCustomerInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCustomerInfoTitle">Customer Info</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Customer</th>
                            <td class='customer'></td>
                        </tr>
                        <tr>
                            <th>Projects</th>
                            <td class='projects'></td>
                        </tr>
                        <tr>
                            <th>Sprints</th>
                            <td class='sprints'></td>
                        </tr>
                        <tr>
                            <th>Tasks</th>
                            <td class='tasks'></td>
                        </tr>
                    </tbody>
                </table>
                <p>By proceeding all the items shown above will be deleted. Are you sure you want to proceed?</p>
            </div>
            <input type="hidden" name="customer_uuid" value="">
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>Cancel</button>
                <button type="button" data-uuid='' class="btn btn-danger deleteConfirm"><i class="fa fa-check"></i> Yes, Proceed</button>
            </div>
        </div>
    </div>
</div>