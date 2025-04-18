<div class="row">
    <div class="col-xs-12 col-sm-12">
      <table class="table">
        <tr>
          <td>Name</td>
          <td><?php echo $user_info->name; ?></td>
        </tr>
        <tr>
          <td>Job Title</td>
          <td><?php echo $user_info->job_title; ?></td>
        </tr>
        <tr>
          <td>Username</td>
          <td><?php echo $user_info->username; ?></td>
        </tr>
        <tr>
          <td>Level</td>
          <td><?php echo $user_info->user_level; ?></td>
        </tr>
      </table>
      <div class="row">
        <div class="col-sm-2 col-xs-2 pull-right text-right">
          <button type="button" class="update_permission btn btn-xs btn-flat btn-danger">Update Permissions</button>
        </div>
      </div>
      <?php if(!empty($resources)): ?>
      <div class="row">
        <div class="col-md-8"></div>
        <div class="col-md-4">
          <form>
            <div class="form-group">
              <input class='form-control' autofocus type="text" id="text_to_filter" value="" placeholder="Type text to filter">
            </div>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 table-responsive">
          <table id="permission_table" class="table table-condensed">
            <thead>
              <tr>
                <th data-user-id="<?php echo $this->uri->segment(3); ?>" style='display:none'>ID</th>
                <th rowspan='1'>Icon</th>
                <th rowspan='1'>Resource Name</th>
                <th rowspan='1'>Controller</th>
                <th rowspan='1'>Normal</th>
                <th rowspan='1'>Admin</th>
                <!-- <th rowspan='1'>Root</th> -->
                <th rowspan='1'>Action</th>
                <th colspan='1'>Allow</th>
              </tr>
            </thead>
            <tbody>
                <tr>
                  <td colspan='6'></td>
                  <td class='text-center'>
                    <input type="checkbox" style='width:20px;height:20px;' class='chkbox read' />
                  </td>
                </tr>
                <?php $displayed_items= array(); $skip=false;?>
                <?php foreach($resources as $resource): ?>
                <?php
                  if(!in_array($resource->id,$displayed_items)){
                    if(!empty($resource->id)){
                      array_push($displayed_items,$resource->id);
                    }
                  }else{
                    continue;
                  }
                ?>
                <tr class='itemRow <?php echo ($resource->type == "main")?'bg-blue':''; ?> <?php echo($resource->visible==0)?"bg-gray":""; ?>'>
                  <td class='info' data-menu-id="<?php echo $resource->id; ?>" data-permission-id="<?php echo $resource->permission_id; ?>" style='display:none'></td>
                  <?php if($resource->visible=='0'): ?>
                    <td class='text-center'><span class='fa fa-eye-slash grey'></span></td>
                  <?php elseif($resource->nom=='divider'): ?>
                    <td class='text-center'><span class='fa fa-minus grey'></span></td>
                  <?php else: ?>
                    <td class='text-center'><span class="fa <?php echo $resource->class; ?>"></span></td>
                  <?php endif; ?>
                  <?php if($resource->type == "main"): ?>
                    <td><i class="fa fa-plus-square" aria-hidden="true"></i> <span class='searchable'><?php echo $resource->nom; ?></span></td>
                  <?php elseif($resource->type == "sub"): ?>
                    <td><img src="<?php echo base_url('assets/images/sub-item.png'); ?>"> <span class=''><?php echo $resource->nom; ?></span></td>
                  <?php else: ?>
                    <td><img style="padding-left:10px;" src="<?php echo base_url('assets/images/sub-item.png'); ?>"> <span class='searchable'><?php echo $resource->nom; ?></span></td>
                  <?php endif; ?>
                  <td class='text-center'><span class='searchable controller'><?php echo $resource->controller; ?></span></td>
                  <td class='text-center'><span class='searchable controller'><input type='checkbox' <?php echo ($resource->Normal==1)?"checked":""; ?> disabled readonly></span></td>
                  <td class='text-center'><span class='searchable controller'><input type='checkbox' <?php echo ($resource->Admin==1)?"checked":""; ?> disabled readonly></span></td>
                  <!-- <td class='text-center'><span class='searchable controller'><input type='checkbox' <?php echo ($resource->Root==1)?"checked":""; ?> disabled readonly></span></td> -->
                  <td class='text-center'><span class='searchable action'><?php echo $resource->action; ?></span></td>
                  <td class='text-center'>
                    <input type="checkbox" style='width:20px;height:20px;' class='chkbox chkbox_read' <?php echo ($resource->read==1)?'checked':'';?>/>
                  </td>
                </tr>
                <?php endforeach; ?>  
              
            </tbody>
          </table>

        </div><!--/col-->
        
      </div><!--/row-->

      <div class="row">
        <div class='col-xs-12 col-sm-6 col-md-4'>
          <table class='table table-bordered'>
            <tr>
              <th colspan='2'>Legend</th>
            </tr>
            <tr>
              <td>
                <span class='fa fa-eye-slash grey'></span>
              </td>
              <td>
                <p>
                Finer grain permissions
                <p class='notes blue'>These items are not shown in the menu, but either allows or blocks some sections of the site.</p>
                </p>
              </td>
            </tr>
          </table>      
        </div>
      </div>

      <?php endif; ?> 
    </div>
</div>
<!-- /Main -->
