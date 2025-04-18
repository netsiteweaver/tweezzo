<!-- Modal Select Customer -->
<div class="modal fade" id="modalSelectClient" tabindex="-1" role="dialog" aria-labelledby="add-user-title" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style='max-width:80%;'>
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
            <img src="../../../www/assets/images/svg/user-plus-svgrepo-com.svg" width="20px" alt="client"> Select Customer
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body table-responsive" id='content'>
        
        <div class="row">
          <div class="col-md-12">
            <div class="row">
              <div class="col-8 col-md-8">
                <input type="text" class="form-control" placeholder="Enter at least 2 characters to start searching" id="modalSearchClients">
              </div>
              <div class="col-4 col-md-4">
                <div class="btn btn-info pull-right" id="addClient" data-toggle="modal" data-target="#modalAddClient"><i class="fa fa-user"></i> Add Customer</div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <!-- <div id="modal-client-overlay" class="hidden"><p>Please wait ...</p></div> -->
                <table id="select_customer" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>NAME</th>
                      <th>PHONE 1</th>
                      <th>PHONE 2</th>
                      <th>EMAIL</th>
                      <th>ADDRESS</th>
                      <th>CERTIFICATE ISSUE DATE</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
        <!-- <button type="button" id="save_customer" class="btn btn-primary"><i class="fa fa-save"></i> Save</button> -->
      </div>
    </div>
  </div>
</div>