<!-- Modal -->
<div class="modal fade" id="modalSelectSupplier" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id='content'>
                <div class="row">
                    <div class="col-md-12">

                        <input class="form-control searchSupplier" onkeyup="Filter('searchSupplier','suppliersTable')" placeholder="START TYPING..." id="searchSupplier">

                        <table id="suppliersTable" class="table table-bordered table-hover">
                            <thead>
                                <tr class='text-center bg-navy'>
                                    <th>COMPANY</th>
                                    <th>NAME</th>
                                    <th>PHONE</th>
                                    <th>EMAIL</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-navy" data-dismiss="modal">
                    <div class="fa fa-times"></div> Cancel
                </button>
            </div>
        </div>
    </div>
</div>
