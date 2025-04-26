<div class="row">
    <div class="col-md-8"></div>
    <div class="col-md-4">
        <button type="button" id="openUserModal" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userSelectModal">
        Select User
        </button>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="userSelectModal" tabindex="-1" aria-labelledby="userSelectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select a User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group user-list" id="userList" tabindex="0">
          <li class="list-group-item" data-user-id="1">John Doe</li>
          <li class="list-group-item" data-user-id="2">Jane Smith</li>
          <li class="list-group-item" data-user-id="3">Alice Johnson</li>
          <li class="list-group-item" data-user-id="4">Bob Lee</li>
        </ul>
      </div>
      <div class="modal-footer">
        <div class="btn btn-warning" data-bs-dismiss="modal" data-dismiss="modal"><i class="fa fa-times"></i><i class="bi bi-x"></i> Cancel</div>
      </div>
    </div>
  </div>
</div>