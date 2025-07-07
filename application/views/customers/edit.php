<form id="save_customers" role="form" action="<?php echo base_url('customers/update/'); ?>" method="post" autocomplete="off">
    <input type="hidden" name="uuid" value="<?php echo $this->uri->segment(3);?>">
    <div class="row">
        <div class="col-md-12">

            <input type="hidden" name="referer" value="<?php echo $this->input->get("referer");?>">
            <div class="row">
                <!-- <div class="col-md-12"> -->
                <!-- <div class="row"> -->
                <?php foreach($fields as $f):?>
                <div class="<?php echo (strtolower(trim($f['type'])) == 'hidden') ? '' : 'col-lg-3 col-md-4 col-sm-6';?>">
                    <?php if( in_array( (strtolower(trim($f['type']))),['text','number','email','date','password'] ) ):?>
                    <div class="form-group">
                        <label class="<?php echo ($f['required']) ? 'asterisk' :'';?>"><?php echo $f['label'];?></label>
                        <input type="<?php echo $f['type'];?>" class="form-control <?php echo ($f['required']) ? 'required' :'';?>" name="<?php echo $f['field_name'];?>" id="<?php echo $f['field_name'];?>" placeholder="<?php echo $f['placeholder'];?>" value="<?php echo $f['value'];?>" <?php echo ($f['required']) ? 'required' :'';?>>
                    </div>
                    <?php elseif(strtolower(trim($f['type'])) == 'textarea'):?>
                    <div class="form-group">
                        <label class="<?php echo ($f['required']) ? 'asterisk' :'';?>">Remarks</label>
                        <textarea name="remarks" id="remarks" cols="30" rows="3" class="form-control" placeholder=""></textarea>
                    </div>
                    <?php elseif(strtolower(trim($f['type'])) == 'select'):?>
                    <div class="form-group">
                        <label class="<?php echo ($f['required']) ? 'asterisk' :'';?>"><?php echo $f['label'];?></label>
                        <select name="<?php echo $f['field_name'];?>" id="<?php echo $f['field_name'];?>" class="form-control <?php echo ($f['required']) ? 'required' :'';?>" <?php echo ($f['required']) ? 'required' :'';?>>
                            <option value="">Select <?php echo $f['label'];?></option>
                            <?php foreach($f['options'] as $i => $o):?>
                            <option value="<?php echo $o['id'];?>" <?php echo (isset($f['value'])) ? (($f['value']==$o['id']) ? 'selected' : '') : '';?>> <?php echo $o['name'];?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <?php elseif(strtolower(trim($f['type'])) == 'hidden'):?>
                    <input type="hidden" id="<?php echo $f['field_name'];?>" name="<?php echo $f['field_name'];?>" value="<?php echo $f['value'];?>">
                    <?php endif;?>
                </div>
                <?php endforeach;?>
                <!-- </div> -->
                <!-- </div> -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <a href="<?php echo base_url("customers/listing");?>">
                <div class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</div>
            </a>
            <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
        </div>
    </div>
</form>

<hr>
<div class="row">
    <div class="col-md-8">
        <h3>Define User Access</h3>
        <p>Here you can define who can access the portal for your company, validating tasks and submitting notes. They will also be able to submit new tasks and create new other access for additional user.</p>
        <div class="row">
            <div class="col-md-12">
                <!-- <a href=""> -->
                    <div id="add-user-access" class="btn btn-info "><i class="fa fa-plus"></i><i class="fa fa-user"></i> Add User</div>
                <!-- </a> -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table id="existing_users" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th style='width:30px;'>Country</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class='sample d-none'>
                            <td><input type="text" class="form-control" placeholder="Enter Name"></td>
                            <td><input type="text" class="form-control" placeholder="Enter Phone"></td>
                            <td><input type="text" class="form-control" placeholder="Enter Email"></td>
                            <td><input type="text" class="form-control" placeholder="mu?"></td>
                            <td>
                                <div class="btn btn-info"><i class="fa fa-edit"></i></div>
                                <div class="btn btn-success"><i class="fa fa-save"></i></div>
                                <div class="btn btn-danger"><i class="fa fa-trash"></i></div>
                            </td>
                        </tr>
                        <?php foreach($customer->access as $user):?>
                        <tr data-id="<?php echo $user->id;?>">
                            <td><input type="text" class="form-control userName" placeholder="Enter Name" value="<?php echo $user->name;?>" readonly></td>
                            <td><input type="text" class="form-control userPhone" placeholder="Enter Phone" value="<?php echo $user->phone_number1;?>" readonly></td>
                            <td><input type="text" class="form-control userEmail" placeholder="Enter Email" value="<?php echo $user->email;?>" readonly></td>
                            <td>
                                <i class="flag flag-<?php echo $user->country_code;?>"></i>
                                <input type="text" class="form-control d-none" placeholder="mu?">
                            </td>
                            <td>
                                <div class="btn btn-danger deleteUser"><i class="fa fa-trash"></i></div>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addUserAccessModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-unlock"></i> Grant User Access</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="">Name</label>
            <input type="text" class="name form-control required" placeholder="Enter user's name">
        </div>
        <div class="form-group">
            <label for="">Email</label>
            <input type="email" class="email form-control required" placeholder="Enter user's email">
        </div>
        <div class="form-group">
            <label for="">Phone</label>
            <input type="text" class="phone form-control" placeholder="[OPTIONAL] Enter phone number">
        </div>
        <div class="form-group">
            <label for="">Password</label>
            <input type="text" class="password form-control required" placeholder="Enter password">
        </div>
        <div class="form-group">
            <label for="">Country</label>
            <select name="country_code" id="" class="country_code form-control">
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
        <button type="button" class="btn btn-primary save"><i class="fa fa-save"></i> Save</button>
      </div>
    </div>
  </div>
</div>