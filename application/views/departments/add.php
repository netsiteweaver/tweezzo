<form id="save_departments" role="form" action="<?php echo base_url('departments/save/'); ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" id="uuid" name="uuid" value="">
    <div class="panel panel-info">
       
        <div class="panel-body">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control required onFocusInput" name="name"  autofocus placeholder="" value="">
                    <p class="help-block onFocusNotes">Please enter Name.</p>
                </div>  
                
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" class="form-control" name="address" autofocus placeholder="">
                </div>  
                
            </div>

     
        </div>


        <div class="panel-body">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" class="form-control onFocusInput" name="phone" autofocus placeholder="" value="">
               
                </div>  
                
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="email" name="email" autofocus placeholder="">
                </div>  
                
            </div>

     
        </div>

        <div class="panel-body">

        <div class="col-md-2">
            <button type="submit"  class="btn btn-primary btn-block">Save</button>
        </div>

        </div> 

    </div>
   
</form>