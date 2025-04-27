<style>
  .modal.zoom-out {
    transition: opacity 0.3s ease-out, transform 0.3s ease-out;
  }

  .modal:not(.show) {
    opacity: 0;
    transform: scale(0.3);
  }
  .list-title {
    text-align: right;
    background-color:rgb(228, 228, 228);
    color:rgb(112, 112, 112);
    padding: 3px 6px;
    font-style: italic;
  }

</style>
<!-- Modal -->
<div class="modal fade zoom-out" id="userSelectModal" tabindex="-1" aria-labelledby="userSelectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">@who ?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group user-list" id="userList" tabindex="0"></ul>
      </div>
      <div class="modal-footer">
        <div class="btn btn-warning" data-bs-dismiss="modal" data-dismiss="modal"><i class="fa fa-times"></i><i class="bi bi-x"></i> Cancel</div>
      </div>
    </div>
  </div>
</div>