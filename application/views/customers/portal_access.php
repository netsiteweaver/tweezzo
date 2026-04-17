<input type="hidden" name="uuid" value="<?php echo $customer->uuid; ?>">
<input type="hidden" name="referer" value="<?php echo $this->input->get("referer"); ?>">

<div class="row">
    <div class="col-md-12">
        <a href="<?php echo base_url(!empty($this->input->get("referer")) ? $this->input->get("referer") : "customers/listing"); ?>">
            <div class="btn btn-warning mb-3"><i class="fa fa-chevron-left"></i> Back</div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <h3>Define User Access</h3>
        <p>Here you can define who can access the portal for your company, validating tasks and submitting notes. They will also be able to submit new tasks and create new other access for additional user.</p>
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($customer->company_name); ?></p>
        <div class="row">
            <div class="col-md-12">
                <div id="add-user-access" class="btn btn-info"><i class="fa fa-plus"></i><i class="fa fa-user"></i> Add User</div>
                <div id="manage-portal-password-standalone" class="btn btn-secondary"><i class="fa fa-key"></i> Manage Portal Passwords</div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <table id="existing_users" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th style='width:30px;'>Country</th>
                            <th style='width:60px;'>Admin</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($customer->access as $user):?>
                        <tr data-id="<?php echo $user->id;?>">
                            <td><input type="text" class="form-control userName" placeholder="Enter Name" value="<?php echo htmlspecialchars($user->name);?>" readonly></td>
                            <td><input type="text" class="form-control userPhone" placeholder="Enter Phone" value="<?php echo htmlspecialchars($user->phone_number1 ?? '');?>" readonly></td>
                            <td><input type="text" class="form-control userEmail" placeholder="Enter Email" value="<?php echo htmlspecialchars($user->email);?>" readonly></td>
                            <td>
                                <i class="flag flag-<?php echo $user->country_code;?>"></i>
                                <input type="text" class="form-control d-none" placeholder="mu?">
                            </td>
                            <td><?php echo !empty($user->admin) ? '<span class="badge badge-info">Yes</span>' : 'No'; ?></td>
                            <td>
                                <div class="btn btn-info btn-sm editUser" title="Edit"><i class="fa fa-edit"></i></div>
                                <div class="btn btn-danger btn-sm deleteUser" title="Remove access"><i class="fa fa-trash"></i></div>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Portal Access Password Management (Standalone) -->
<div class="modal fade" id="modalPortalPasswordStandalone" tabindex="-1" role="dialog" aria-labelledby="modalPortalPasswordStandaloneTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPortalPasswordStandaloneTitle">Manage Portal Access</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Customer: <span id="portal-customer-name-standalone"><?php echo htmlspecialchars($customer->company_name); ?></span></strong></p>
                <div id="portal-users-list-standalone">
                    <p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Display New Password (Standalone) -->
<div class="modal fade" id="modalNewPasswordStandalone" tabindex="-1" role="dialog" aria-labelledby="modalNewPasswordStandaloneTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalNewPasswordStandaloneTitle">Password Reset Successful</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>User:</strong> <span id="reset-user-name-standalone"></span></p>
                <p><strong>Email:</strong> <span id="reset-user-email-standalone"></span></p>
                <div class="alert alert-info">
                    <p class="mb-2"><strong>New Password:</strong></p>
                    <div class="input-group">
                        <input type="text" class="form-control" id="new-password-display-standalone" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="copy-password-btn-standalone">
                                <i class="fa fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>
                <p class="text-success"><i class="fa fa-check-circle"></i> The password has been emailed to the user.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-check"></i> Done</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add/Edit User Access -->
<div class="modal fade" id="addUserAccessModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUserAccessModalTitle"><i class="fa fa-unlock"></i> Grant User Access</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" class="access_id" value="">
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
        <div class="form-group password-group">
            <label for="">Password</label>
            <input type="text" class="password form-control" placeholder="Enter password (leave blank when editing to keep current)">
            <small class="form-text text-muted add-mode-hint">Required for new user</small>
        </div>
        <div class="form-group generated-password-option add-mode-only">
            <div class="form-check">
                <input type="checkbox" class="form-check-input generate_password" id="generateUserPassword" value="1">
                <label class="form-check-label" for="generateUserPassword">Generate password and send it by email</label>
            </div>
            <small class="form-text text-muted">When enabled, password field is optional.</small>
        </div>
        <div class="form-group">
            <label for="">Admin</label>
            <div class="form-check">
                <input type="checkbox" class="admin form-check-input" id="addUserAdmin" value="1">
                <label class="form-check-label" for="addUserAdmin">Portal admin (can add/remove other users)</label>
            </div>
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