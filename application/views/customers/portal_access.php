<form id="save_customers" role="form" action="<?php echo base_url('customers/update_portal_access/'); ?>" method="post"
    autocomplete="off">

    <div class="row">
        <div class="col-md-12">
            <?php if(!empty($this->input->get("reason"))):?>
            <div class="alert alert-danger"><?php echo $this->input->get("reason");?></div>
            <?php endif;?>
            <input type="hidden" name="referer" value="<?php echo $this->input->get("referer");?>">
            <input type="hidden" name="uuid" value="<?php echo $customer->uuid;?>">
            <p>Define Portal Access for <?php echo $customer->company_name;?></p>
            <div class="form-group col-md-4">
                <label for="">Email <i class="fa fa-at"></i></label>
                <input type="email" class='form-control' name='email' value='<?php echo $customer->email;?>' readonly>
            </div>
            <div class="form-group col-md-4">
                <label for="">Password <i class="fa fa-lock"></i></label>
                <input type="password" class='form-control' name='password' value='' minlength='4' autofocus required>
            </div>
            <div class="form-group">
                <input type="checkbox" name="send_password" checked> Send password to customer?
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <a href="<?php echo base_url("customers/listing");?>">
                <div class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</div>
            </a>
            <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
        </div>
    </div>
</form>