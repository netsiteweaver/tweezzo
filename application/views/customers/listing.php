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
    <div class="col-3 col-md-2">
        <label for="">Active Status</label>
        <select class="form-control" name="active_filter" id="active_filter">
            <option value="active" <?php echo (empty($active_filter) || $active_filter == 'active') ? 'selected':'';?>>Only Active</option>
            <option value="all" <?php echo ($active_filter == 'all') ? 'selected':'';?>>All</option>
            <option value="inactive" <?php echo ($active_filter == 'inactive') ? 'selected':'';?>>Only Inactive</option>
        </select>
    </div>
    <div class="col-3 col-sm-3 col-md-3">
        <label for="">Options</label>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="hide_completed" id="hide_completed" value="1" <?php echo (!empty($hide_completed)) ? 'checked':'';?>>
            <label class="form-check-label" for="hide_completed">
                Hide customers with all tasks completed
            </label>
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
                                <th class="text-center" title="Number of users with portal access">Portal Users</th>
                                <th>&nbsp;</th>
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
                                    <td class="text-center">
                                        <?php if ($perms['edit']) : ?>
                                        <span class="badge badge-<?php echo ((int) $customer->access_count > 0) ? 'info' : 'secondary'; ?> portal-user-count managePortalUsers cursor-pointer" title="Manage portal users"><?php echo (int) $customer->access_count; ?></span>
                                        <?php else : ?>
                                        <?php echo (int) $customer->access_count; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class='text-center activeOrNot'><div class="btn btn-block btn-<?php echo ($customer->active=='1')?'info':'danger';?>"><i class='fa fa-<?php echo ($customer->active=='1')?'check':'times';?>'></i></div></td>
                                    <td>
                                        <?php if ($perms['view']) : ?>
                                        <a href="<?php echo base_url("tasks/listing?customer_id=" . $customer->customer_id); ?>"><div class="btn btn-default"><i class="fa fa-bars"></i></div></a>
                                        <?php endif;?>
                                        <?php if ($perms['edit']) : ?>
                                            <a href="<?php echo base_url("customers/edit/" . $customer->uuid."?referer=customers/listing/".$this->uri->segment(3,1)); ?>">
                                                <div class="btn btn-md btn-primary"><i class="fa fa-edit"></i></div>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($perms['edit']) : ?>
                                            <div class="btn btn-<?php echo ($customer->active=='1')?'warning':'success';?> toggleActive" title="<?php echo ($customer->active=='1')?'Set Inactive':'Set Active';?>">
                                                <i class="fa fa-<?php echo ($customer->active=='1')?'eye-slash':'eye';?>"></i>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($perms['edit']) : ?>
                                            <div class="btn btn-secondary managePortalUsers" title="Portal Users">
                                                <i class="fa fa-user"></i>
                                            </div>
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

<!-- Modal Portal Users: list, add/edit, reset password, remove -->
<div class="modal fade" id="modalPortalUsers" tabindex="-1" role="dialog" aria-labelledby="modalPortalUsersTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPortalUsersTitle"><i class="fa fa-users"></i> Portal Users &ndash; <span class="customer-name"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="portal_customer_uuid" value="">

                <!-- New password (shown after a reset) -->
                <div class="alert alert-success new-password-panel d-none">
                    <button type="button" class="close close-new-password" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <p class="mb-2"><strong>New password for <span class="reset-user-name"></span></strong> (<span class="reset-user-email"></span>) &ndash; it has been emailed to the user.</p>
                    <div class="input-group" style="max-width:420px;">
                        <input type="text" class="form-control new-password-display" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary copy-password-btn" type="button"><i class="fa fa-copy"></i> Copy</button>
                        </div>
                    </div>
                    <small class="text-muted">You can also copy the password and send it directly via WhatsApp.</small>
                </div>

                <!-- List -->
                <div class="portal-users-list-panel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted users-count-label"></span>
                        <button type="button" class="btn btn-info btn-sm add-portal-user"><i class="fa fa-plus"></i> <i class="fa fa-user"></i> Add User</button>
                    </div>
                    <div class="portal-users-list">
                        <p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>
                    </div>
                    <p class="text-muted small mb-0">
                        <i class="fa fa-user-shield mr-1"></i><strong>Admin</strong>: can add, edit and remove other users from the portal.<br>
                        <i class="fa fa-check-double mr-1"></i><strong>Validator</strong>: can validate or reject tasks on the staging server.
                    </p>
                </div>

                <!-- Add / Edit form -->
                <div class="portal-user-form-panel d-none">
                    <h6 class="form-title mb-3"></h6>
                    <input type="hidden" class="access_id" value="">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" class="name form-control" placeholder="Enter user's name">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" class="email form-control" placeholder="Enter user's email">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Phone</label>
                            <input type="text" class="phone form-control" placeholder="[OPTIONAL] Enter phone number">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Country</label>
                            <select class="country_code form-control">
                                <option value="mu" selected>Mauritius</option>
                                <option value="in">India</option>
                                <option value="ua">Ukraine</option>
                                <option value="pl">Poland</option>
                                <option value="br">Brazil</option>
                                <option value="cn">China</option>
                                <option value="mx">Mexico</option>
                                <option value="pk">Pakistan</option>
                                <option value="ma">Morocco</option>
                                <option value="cr">Costa Rica</option>
                                <option value="ru">Russia</option>
                                <option value="fr">France</option>
                                <option value="de">Germany</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <div class="form-check">
                                <input type="checkbox" class="admin form-check-input" id="portalUserAdmin" value="1">
                                <label class="form-check-label" for="portalUserAdmin">Admin (can add/remove other users)</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="can_validate form-check-input" id="portalUserCanValidate" value="1">
                                <label class="form-check-label" for="portalUserCanValidate">Validator (can validate/reject tasks on staging)</label>
                            </div>
                        </div>
                        <div class="col-md-6 form-group add-mode-only">
                            <div class="form-check">
                                <input type="checkbox" class="generate_password form-check-input" id="portalUserGeneratePassword" value="1" checked>
                                <label class="form-check-label" for="portalUserGeneratePassword">Generate password and send it by email</label>
                            </div>
                            <input type="text" class="password form-control mt-2 d-none" placeholder="Enter password (4 chars min)">
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="button" class="btn btn-secondary cancel-portal-user"><i class="fa fa-times"></i> Cancel</button>
                        <button type="button" class="btn btn-primary save-portal-user"><i class="fa fa-save"></i> Save</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>
