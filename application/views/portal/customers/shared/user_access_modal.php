<style>
  .admin-user{background-color:#eee;}
</style>
<!-- Modal -->
<div class="modal fade" id="addUserAccessModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
                            <th><i class="bi bi-person me-1"></i>User</th>
                            <th><i class="bi bi-envelope me-1"></i>Email</th>
                            <th><i class="bi bi-globe me-1"></i>Country</th>
                            <th><i class="bi bi-shield-lock me-1"></i>Admin</th>
                            <th><i class="bi bi-patch-check me-1"></i>Validator</th>
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
                            <td><?php echo !empty($user->isAdmin) ? '<i class="bi bi-check-circle-fill text-success" title="Yes"></i>' : ''; ?></td>
                            <td><?php echo !empty($user->canValidate) ? '<i class="bi bi-check-circle-fill text-success" title="Yes"></i>' : ''; ?></td>
                          </tr>
                          <?php endforeach;?>
                        </tbody>
                      </table>
                      <p class="text-muted small mb-0">
                        <i class="bi bi-shield-lock me-1"></i><strong>Admin</strong>: can add, edit and remove other users.<br>
                        <i class="bi bi-patch-check me-1"></i><strong>Validator</strong>: can validate or reject tasks on the staging server.
                      </p>
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