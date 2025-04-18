<form action="<?php echo base_url('suppliers/save'); ?>" method="post">
<input type="hidden" name="uuid" value="">
<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="panel panel-info">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Company Name</label>
                        <input type="text" name="company_name" class="form-control required" value="" required autofocus>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Contact Person</label>
                        <input type="text" name="full_name" class="form-control required" value="" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Address</label>
                        <textarea name="address" id="" rows="4" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="text" name="email" class="form-control required" value="" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Phone Number</label>
                        <input type="text" name="phone_number" minlength="7" class="form-control" value="">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <a href="<?php echo base_url("suppliers/listing");?>"><div class="btn btn-warning"><div class="fa fa-chevron-left"></div> Back</div></a>
                    <button type="submit"  class="btn btn-info"><div class="fa fa-save"></div> Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
</form>