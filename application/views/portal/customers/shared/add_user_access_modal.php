<style>
  #addUserAccessModal .modal-body #existing_users {
    cursor: pointer;
  }
</style>
<!-- Modal -->
<div class="modal fade" id="addUserAccessModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Create or Manage User Access</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="hidden" name="access_id">
                            <input name="name" type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Email</label>
                            <input name="email" type="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Password</label>
                            <input name="password" type="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Confirm Password</label>
                            <input name="confirm_password" type="password" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                      <p class='label-existing-users'><?php echo count($user_access) . " Existing User".( count($user_access)>1?'s':'' );?> <i>(max 5 users)</i></p>
                      <p class='text-muted small'>Click on a user to edit.</p>
                      <table id='existing_users' class="table">
                        <thead>
                          <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Country</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach($user_access as $user):?>
                          <tr data-id='<?php echo $user->id;?>' class='<?php echo ($user->isAdmin) ? 'text-bold' : '';?>'>
                            <td><?php echo $user->userName;?></td>
                            <td><?php echo $user->userEmail;?></td>
                            <td>
                              <?php if (!empty($user->country_code)): ?>
                                <i class="flag flag-<?php echo htmlspecialchars($user->country_code); ?>"></i>
                              <?php endif; ?>
                            </td>
                            <td class=''>
                              <i class="remove-user bi bi-trash"></i>
                              <?php if($user->isAdmin):?>
                                <img class="admin-crown" src="assets/images/crown.png" style='width:16px;' alt="">  
                              <?php endif;?>
                            </td>
                          </tr>
                          <?php endforeach;?>
                        </tbody>
                      </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>
                    Close</button>
                    <?php if(count($user_access)<5):?>
                <button type="button" class="btn btn-info create-user-access"><i class="bi bi-save"></i> Create User
                    Access</button>
                <button type="button" class="btn btn-primary update-user-access d-none"><i class="bi bi-arrow-repeat"></i> Update User</button>
                    <?php endif;?>
            </div>
        </div>
    </div>
</div>