<!-- Modal Select Product -->
<div class="modal fade" id="modalSelectProduct" tabindex="-1" role="dialog" aria-labelledby="add-user-title" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
            <img src="../../../www/assets/images/svg/product-workspace-svgrepo-com.svg" width="20px" alt="client"> Select Product
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id='content'>
        <table id="products-table" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>STOCKREF</th>
              <th>NAME</th>
              <th>PRICE</th>
              <th>PHOTO</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default close-modal"><i class="fa fa-times"></i> Close</button>
        <!-- <button type="button" id="save_customer" class="btn btn-primary"><i class="fa fa-save"></i> Save</button> -->
      </div>
    </div>
  </div>
</div>