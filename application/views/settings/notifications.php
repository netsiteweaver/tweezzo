<div class="row">
    <div class="col-md-12">
        <p>Notifications are automatically sent to concerned users, developers or customers when any of the events listed below are triggerred.</p>
         <!-- What is defined here is for the admins that still need to get notified, like project managers or team lead.</p> -->
    </div>
</div>

<div class="row"><div class="col-md-12 text-center bg-navy pt-2"><h3><i class="fa fa-list-ol"></i> PROJECTS</h3></div></div>
<div class="row"><!-- PROJECTS -->
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Project</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="create_projects" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$create_projects)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="create_projects"><i class="fa fa-save"></i> Save for Create Project</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Update Project</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="update_projects" class="table table-bordered table-hover process">
                        <thead>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$update_projects)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="update_projects"><i class="fa fa-save"></i> Save for Update Project</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Delete Project</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="delete_projects" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$delete_projects)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="delete_projects"><i class="fa fa-save"></i> Save for Update Project</div>
            </div>
        </div>
    </div>
</div>

<div class="row"><div class="col-md-12 text-center bg-yellow mt-5 pt-2"><h3><i class="fa fa-list-ul"></i> SPRINTS</h3></div></div>
<div class="row"><!-- SPRINTS -->
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Sprint</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="create_sprints" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$create_sprints)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="create_sprints"><i class="fa fa-save"></i> Save for Create Sprint</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Update Sprint</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="update_sprints" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$update_sprints)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="update_sprints"><i class="fa fa-save"></i> Save for Update Sprint</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Delete Sprint</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="delete_sprints" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$delete_sprints)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="delete_sprints"><i class="fa fa-save"></i> Save for Update Sprint</div>
            </div>
        </div>
    </div>
</div>

<div class="row"><div class="col-md-12 text-center bg-orange mt-5 pt-2"><h3><i class="fa fa-list"></i> TASKS</h3></div></div>
<div class="row"><!-- TASKS -->
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Task</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="create_tasks" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$create_tasks)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="create_tasks"><i class="fa fa-save"></i> Save for Create Task</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Update Task</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="update_tasks" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$update_tasks)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="update_tasks"><i class="fa fa-save"></i> Save for Update Task</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Delete Task</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="delete_tasks" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$delete_tasks)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="delete_tasks"><i class="fa fa-save"></i> Save for Update Task</div>
            </div>
        </div>
    </div>
</div>

<div class="row"><div class="col-md-12 text-center bg-purple mt-5 pt-2"><h3><i class="fa fa-users"></i> CUSTOMERS</h3></div></div>
<div class="row"><!-- CUSTOMERS -->
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Customer</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="create_customers" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$create_customers)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="create_customers"><i class="fa fa-save"></i> Save for Create Customer</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Update Customer</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="update_customers" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$update_customers)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="update_customers"><i class="fa fa-save"></i> Save for Create Customer</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Delete Customer</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="delete_customers" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                <div class="btn btn-default updatenotifications" id="" data-type="delete_customers"><i class="fa fa-save"></i> Save for Delete Customer</div>
            </div>
        </div>
    </div>
</div>

<div class="row"><div class="col-md-12 text-center bg-green mt-5 pt-2"><h3><i class="fa fa-users"></i> USERS / ADMINS</h3></div></div>
<div class="row"><!-- USERS / ADMINS -->
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Users</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="create_users" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$create_users)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="create_users"><i class="fa fa-save"></i> Save for Create Customer</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Update Users</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="update_users" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$update_users)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="update_users"><i class="fa fa-save"></i> Save for Create Customer</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Delete Users</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="delete_users" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$delete_users)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="delete_users"><i class="fa fa-save"></i> Save for Delete Customer</div>
            </div>
        </div>
    </div>
</div>

<div class="row"><div class="col-md-12 text-center bg-teal mt-5 pt-2"><h3><i class="fa fa-users"></i> DEVELOPERS</h3></div></div>
<div class="row"><!-- DEVELOPERS -->
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Developers</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="create_developers" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$create_developers)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="create_developers"><i class="fa fa-save"></i> Save for Create Customer</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Update Developers</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="update_developers" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$update_developers)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="update_developers"><i class="fa fa-save"></i> Save for Create Customer</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Delete Developers</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="delete_developers" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$delete_developers)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="delete_developers"><i class="fa fa-save"></i> Save for Delete Customer</div>
            </div>
        </div>
    </div>
</div>

<div class="row"><div class="col-md-12 text-center bg-red mt-5 pt-2"><h3><i class="fa fa-comments"></i> NOTES</h3></div></div>
<div class="row"><!-- NOTES -->
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Notes</h3>
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="create_notes" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                                    <input type="checkbox" class='pull-right' name="<?php echo $user->id;?>" <?php echo (in_array($user->id,$create_notes)) ? 'checked' : '';?>>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="btn btn-default updatenotifications" id="" data-type="create_notes"><i class="fa fa-save"></i> Save for Create Note</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Delete Notes</h3>
                
            </div>
            <div class="card-body">
                <form id="form" role="form" action="#<?php //echo base_url('settings/updatenotifications'); ?>" method="post">
                    <table id="delete_notes" class="table table-bordered table-hover process">
                        <thead>
                            <th>PHOTO</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CREATED</th>
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
                <div class="btn btn-default updatenotifications" id="" data-type="delete_notes"><i class="fa fa-save"></i> Save for Delete Note</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- <div class="col-md-5"></div> -->
    <div class="col-md-2">
        <div class="btn btn-block btn-info" id='updateAll'><i class="fa fa-save"></i> Update All</div>
    </div>
</div>
