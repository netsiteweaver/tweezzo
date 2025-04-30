<!-- Modal -->
<div class="modal fade" id="addUserAccessModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Add User Access</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Password</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                      <p>Exting Users</p>
                      <table class="table">
                        <thead>
                          <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach($user_access as $user):?>
                          <tr>
                            <td><?php echo $user->userName;?></td>
                            <td><?php echo $user->userEmail;?></td>
                            <td>
                              <?php if(!$user->isAdmin):?>
                              <i class="bi-trash"></i>
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
                <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i>
                    Close</button>
                <button type="button" class="btn btn-info create-user-access"><i class="bi bi-save"></i> Create User
                    Access</button>
            </div>
        </div>
    </div>
</div>