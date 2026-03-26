<style>
  .admin-user{background-color:#eee;}
</style>
<!-- Modal -->
<div class="modal fade" id="addUserAccessModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">View Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                      <table id='existing_users' class="table">
                        <thead>
                          <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Country</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach($user_access as $user):?>
                          <tr data-id='<?php echo $user->id;?>' class='<?php echo ($user->isAdmin) ? 'admin-user' : '';?>'>
                            <td><?php echo $user->userName;?></td>
                            <td><?php echo $user->userEmail;?></td>
                            <td>
                              <?php if (!empty($user->country_code)): ?>
                                <i class="flag flag-<?php echo htmlspecialchars($user->country_code); ?>"></i>
                              <?php endif; ?>
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
            </div>
        </div>
    </div>
</div>