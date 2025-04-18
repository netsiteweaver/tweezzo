<!-- Modal Select Customer Add -->
<div class="modal fade" id="modalAddClient" style='z-index:10000' tabindex="-1" role="dialog" aria-labelledby="add-user-title" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-user"></i> Add Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php $this->load->view("shared/customers_add");?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
        <button type="button" id="quick_save_customer" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
      </div>
    </div>
  </div>
</div>