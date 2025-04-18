<div class="row">
    <div class="col-md-12 text-center">
        <p>Notifications are automatically sent to concerned users, developers or customers for any of the events listed below. What is defined here is for the admins that still need to get notified, like project managers or team lead.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-9">
        <div class="card">
            <div class="card-header bg-teal">
                <h3 class="card-title">Create Project</h3>
                
            </div>
            <div class="card-body">
                <p class="notes">Define who will receive email notification when a Project is created</p>
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="create_project" class="table table-bordered table-hover process">
                        <thead>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED ON</th>
                            <th></th>
                        </thead>

                        <tbody>
                            <?php foreach ($users as $user):?>
                            <tr data-user='<?php echo $user->id;?>'>
                                <th class='text-center'>
                                    <?php if(!empty($user->photo)) :?>
                                    <img style='width:50px;' class='' src="uploads/users/<?php echo $user->photo;?>" alt="">
                                    <?php endif;?>
                                </th>
                                <td><?php echo $user->name;?></td>
                                <td><?php echo $user->email;?></td>
                                <td><?php echo ($user->created);?></td>
                                <td>
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$create_project)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn bg-teal updatenotifications" id="" data-type="create_project"><i class="fa fa-save"></i> Save for Create Project</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-9">
        <div class="card">
            <div class="card-header bg-orange">
                <h3 class="card-title">Update Project</h3>
                
            </div>
            <div class="card-body">
                <p class="notes">Define who will receive email notification when a Project is updated</p>
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="update_project" class="table table-bordered table-hover process">
                        <thead>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED ON</th>
                            <th></th>
                        </thead>

                        <tbody>
                            <?php foreach ($users as $user):?>
                            <tr data-user='<?php echo $user->id;?>'>
                                <th class='text-center'>
                                    <?php if(!empty($user->photo)) :?>
                                    <img style='width:50px;' class='' src="uploads/users/<?php echo $user->photo;?>" alt="">
                                    <?php endif;?>
                                </th>
                                <td><?php echo $user->name;?></td>
                                <td><?php echo $user->email;?></td>
                                <td><?php echo ($user->created);?></td>
                                <td>
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$update_project)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn bg-orange updatenotifications" id="" data-type="update_project"><i class="fa fa-save"></i> Save for Update Project</div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-9">
        <div class="card">
            <div class="card-header bg-navy">
                <h3 class="card-title">Create Sprint</h3>
                
            </div>
            <div class="card-body">
                <p class="notes">Define who will receive email notification when a Sprint is created</p>
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="create_sprint" class="table table-bordered table-hover process">
                        <thead>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED ON</th>
                            <th></th>
                        </thead>

                        <tbody>
                            <?php foreach ($users as $user):?>
                            <tr data-user='<?php echo $user->id;?>'>
                                <th class='text-center'>
                                    <?php if(!empty($user->photo)) :?>
                                    <img style='width:50px;' class='' src="uploads/users/<?php echo $user->photo;?>" alt="">
                                    <?php endif;?>
                                </th>
                                <td><?php echo $user->name;?></td>
                                <td><?php echo $user->email;?></td>
                                <td><?php echo ($user->created);?></td>
                                <td>
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$create_sprint)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn bg-navy updatenotifications" id="" data-type="create_sprint"><i class="fa fa-save"></i> Save for Create Sprint</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-9">
        <div class="card">
            <div class="card-header bg-purple">
                <h3 class="card-title">Update Sprint</h3>
                
            </div>
            <div class="card-body">
                <p class="notes">Define who will receive email notification when a Sprint is updated</p>
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="update_sprint" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED ON</th>
                            <th></th>
                        </thead>

                        <tbody>
                            <?php foreach ($users as $user):?>
                            <tr data-user='<?php echo $user->id;?>'>
                                <th class='text-center'>
                                    <?php if(!empty($user->photo)) :?>
                                    <img style='width:50px;' class='' src="uploads/users/<?php echo $user->photo;?>" alt="">
                                    <?php endif;?>
                                </th>
                                <td><?php echo $user->name;?></td>
                                <td><?php echo $user->email;?></td>
                                <td><?php echo ($user->created);?></td>
                                <td>
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$update_sprint)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn bg-purple updatenotifications" id="" data-type="update_sprint"><i class="fa fa-save"></i> Save for Update Sprint</div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-9">
        <div class="card">
            <div class="card-header bg-yellow">
                <h3 class="card-title">Create Task</h3>
                
            </div>
            <div class="card-body">
                <p class="notes">Define who will receive email notification when a Task is created</p>
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="create_task" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED ON</th>
                            <th></th>
                        </thead>

                        <tbody>
                            <?php foreach ($users as $user):?>
                            <tr data-user='<?php echo $user->id;?>'>
                                <th class='text-center'>
                                    <?php if(!empty($user->photo)) :?>
                                    <img style='width:50px;' class='' src="uploads/users/<?php echo $user->photo;?>" alt="">
                                    <?php endif;?>
                                </th>
                                <td><?php echo $user->name;?></td>
                                <td><?php echo $user->email;?></td>
                                <td><?php echo ($user->created);?></td>
                                <td>
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$create_task)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn bg-yellow updatenotifications" id="" data-type="create_task"><i class="fa fa-save"></i> Save for Create Task</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-9">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title">Update Task</h3>
                
            </div>
            <div class="card-body">
                <p class="notes">Define who will receive email notification when a Task is updated</p>
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="update_task" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED ON</th>
                            <th></th>
                        </thead>

                        <tbody>
                            <?php foreach ($users as $user):?>
                            <tr data-user='<?php echo $user->id;?>'>
                                <th class='text-center'>
                                    <?php if(!empty($user->photo)) :?>
                                    <img style='width:50px;' class='' src="uploads/users/<?php echo $user->photo;?>" alt="">
                                    <?php endif;?>
                                </th>
                                <td><?php echo $user->name;?></td>
                                <td><?php echo $user->email;?></td>
                                <td><?php echo ($user->created);?></td>
                                <td>
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$update_task)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn bg-info updatenotifications" id="" data-type="update_task"><i class="fa fa-save"></i> Save for Update Task</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-9">
        <div class="card">
            <div class="card-header bg-green">
                <h3 class="card-title">Note Added</h3>
                
            </div>
            <div class="card-body">
                <p class="notes">Define who will receive email notification when a note is added</p>
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="add_notes" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED ON</th>
                            <th></th>
                        </thead>

                        <tbody>
                            <?php foreach ($users as $user):?>
                            <tr data-user='<?php echo $user->id;?>'>
                                <th class='text-center'>
                                    <?php if(!empty($user->photo)) :?>
                                    <img style='width:50px;' class='' src="uploads/users/<?php echo $user->photo;?>" alt="">
                                    <?php endif;?>
                                </th>
                                <td><?php echo $user->name;?></td>
                                <td><?php echo $user->email;?></td>
                                <td><?php echo ($user->created);?></td>
                                <td>
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$add_notes)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn bg-green updatenotifications" id="" data-type="add_notes"><i class="fa fa-save"></i> Save for Create Note</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-9">
        <div class="card">
            <div class="card-header bg-danger">
                <h3 class="card-title">Note Deleted</h3>
                
            </div>
            <div class="card-body">
                <p class="notes">Define who will receive email notification when a Note is deleted</p>
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="delete_notes" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED ON</th>
                            <th></th>
                        </thead>

                        <tbody>
                            <?php foreach ($users as $user):?>
                            <tr data-user='<?php echo $user->id;?>'>
                                <th class='text-center'>
                                    <?php if(!empty($user->photo)) :?>
                                    <img style='width:50px;' class='' src="uploads/users/<?php echo $user->photo;?>" alt="">
                                    <?php endif;?>
                                </th>
                                <td><?php echo $user->name;?></td>
                                <td><?php echo $user->email;?></td>
                                <td><?php echo ($user->created);?></td>
                                <td>
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$delete_notes)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn bg-danger updatenotifications" id="" data-type="delete_notes"><i class="fa fa-save"></i> Save for Delete Note</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-9">
        <div class="card">
            <div class="card-header bg-teal">
                <h3 class="card-title">Customer Created</h3>
                
            </div>
            <div class="card-body">
                <p class="notes">Define who will receive email notification when a customer is created</p>
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="add_customers" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED ON</th>
                            <th></th>
                        </thead>

                        <tbody>
                            <?php foreach ($users as $user):?>
                            <tr data-user='<?php echo $user->id;?>'>
                                <th class='text-center'>
                                    <?php if(!empty($user->photo)) :?>
                                    <img style='width:50px;' class='' src="uploads/users/<?php echo $user->photo;?>" alt="">
                                    <?php endif;?>
                                </th>
                                <td><?php echo $user->name;?></td>
                                <td><?php echo $user->email;?></td>
                                <td><?php echo ($user->created);?></td>
                                <td>
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$add_customers)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn bg-teal updatenotifications" id="" data-type="add_customers"><i class="fa fa-save"></i> Save for Create Customer</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-9">
        <div class="card">
            <div class="card-header bg-danger">
                <h3 class="card-title">Customer Deleted</h3>
                
            </div>
            <div class="card-body">
                <p class="notes">Define who will receive email notification when a Customer is deleted</p>
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="delete_customers" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED ON</th>
                            <th></th>
                        </thead>

                        <tbody>
                            <?php foreach ($users as $user):?>
                            <tr data-user='<?php echo $user->id;?>'>
                                <th class='text-center'>
                                    <?php if(!empty($user->photo)) :?>
                                    <img style='width:50px;' class='' src="uploads/users/<?php echo $user->photo;?>" alt="">
                                    <?php endif;?>
                                </th>
                                <td><?php echo $user->name;?></td>
                                <td><?php echo $user->email;?></td>
                                <td><?php echo ($user->created);?></td>
                                <td>
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$delete_customers)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn bg-danger updatenotifications" id="" data-type="delete_customers"><i class="fa fa-save"></i> Save for Delete Customer</div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-5"></div>
    <div class="col-md-2">
        <div class="btn btn-block btn-outline-info" id='updateAll'><i class="fa fa-save"></i> Update All</div>
    </div>
</div>
