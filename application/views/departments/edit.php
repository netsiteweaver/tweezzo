<form id="update_departments" role="form" action="<?php echo base_url('departments/update/'); ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" id="uuid" name="uuid" value="">
    <div class="panel panel-info">
       
        <div class="panel-body">
            <div class="col-md-3">
            <input type="hidden" id="uuid" name="uuid" value="<?php echo $department->uuid; ?>">
            <input type="hidden" name="id" value="<?php echo $department->id; ?>">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control required onFocusInput" name="name"  autofocus placeholder="" value="<?php echo $department->name; ?>">
                    <p class="help-block onFocusNotes">Please enter Name.</p>

                </div>  
                
            </div>

            <div class="col-md-3">
                <div class="form-group">
                   <label>Address</label>
                    <input type="text" class="form-control" name="address" autofocus placeholder="" value="<?php echo $department->address; ?>">
                </div>  
                
            </div>

     
        </div>


        <div class="panel-body">
            <div class="col-md-3">
                <div class="form-group">
                <label>Phone</label>
                    <input type="text" class="form-control onFocusInput" name="phone" autofocus placeholder=""value="<?php echo $department->phone; ?>">
                </div>  

            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="email" name="email" autofocus placeholder="" value="<?php echo $department->email; ?>"> 
                </div>  
                
            </div>

     
        </div>



        <div class="panel-body">

        <div class="col-md-2">
            <button type="submit"  class="btn btn-primary btn-block">Update</button>
        </div>

        </div> 

        </div>
    </div>
   
</form>