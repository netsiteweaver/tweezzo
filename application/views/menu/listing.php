<?php if($perms['can_add']): ?>
<div class="row">
  <div class="col-md-8">
      <a href="<?php echo base_url('/menu/add/'); ?>"><button class="btn btn-xs btn-flat btn-success"><i class="fa fa-plus"></i> Add</button></a> 
  </div>
  <div class="col-md-4">
    <div class="checkbox">
      <label for="hide-non-visible-items">
        <input type="checkbox" class="hide-non-visible-items"  id="hide-non-visible-items">
        <i class="fa fa-eye-slash"></i> Hide Non Visible Items
      </label>
    </div>
    
  </div>
</div>
<?php endif; ?>
<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-body table-responsive no-padding">      
        <table id="" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th style='display:none'>ID</th>
              <th>Icon</th>
              <th>Resource Name</th>
              <th>Visible</th>
              <th>Controller</th>
              <th>Action</th>
              <th>Normal</th>
              <th>Admin</th>
              <th>Root</th>
              <th>Display<br />Order</th>
              <!-- <th>Parent Menu</th> -->
              <th>Sub Menu</th>
              <th>Operations</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($resources['main'])): ?>
              <?php foreach($resources['main'] as $i => $product): ?>
              <tr class="<?php echo($product->visible==0)?'text-italic grey':''; ?>">
                <td style='display:none'><?php echo $product->mid; ?></td>
                <td class='text-center'><span class="fa <?php echo $product->class; ?>" aria-hidden="true"></span></td>
                <td class="<?php echo($product->visible==0)?'text-italic grey':''; ?>"><?php echo $product->nom; ?></td>
                <td class='text-center <?php echo($product->visible==0)?'grey':''; ?>'><i class='fa <?php echo ($product->visible==1)?'fa-eye':'fa-eye-slash'; ?>'></i></td>
                <td class="<?php echo($product->visible==0)?'text-italic grey':''; ?>"><?php echo (empty($resources['sub'][$product->mid]))?$product->controller:''; ?></td>
                <td class="<?php echo($product->visible==0)?'text-italic grey':''; ?>"><?php echo (empty($resources['sub'][$product->mid]))?$product->action:''; ?></td>
                <td class="text-center"><input type="checkbox" disabled <?php echo ($product->Normal==1)?'checked':''; ?>></td>
                <td class="text-center"><input type="checkbox" disabled <?php echo ($product->Admin==1)?'checked':''; ?>></td>
                <td class="text-center"><input type="checkbox" disabled <?php echo ($product->Root==1)?'checked':''; ?>></td>
                <td class='text-center <?php echo($product->visible==0)?'text-italic grey':''; ?>'><?php echo $product->display_order; ?></td>
                <td><?php 
                if(!empty($resources['sub'][$product->mid])){
                  echo "<a href='".base_url('menu/submenu/'.$product->mid)."'>Sub Menu</a>";
                }
                ?></td>
                <td class="actions">
                <?php if($product->type != 'section'):?>
                  <?php if($perms['can_edit']): ?>
                  <a href='<?php echo base_url("menu/edit/".$product->mid.DIRECTORY_SEPARATOR.$this->uri->segment(4)); ?>'><button class="btn btn-sm btn-flat btn-primary"><i class='fa fa-edit'></i> Edit</button></a> 
                  <?php endif; ?>
                <?php endif; ?>

                  <?php if($perms['can_delete']): ?>
                  <a href='#' class='resource'><button data-id="<?php echo $product->mid; ?>" data-url="<?php echo base_url("menu/deleteAjax"); ?>" class="deleteAjax btn btn-sm btn-flat btn-danger"><i class='fa fa-trash'></i></button></a> 
                  <?php endif; ?>

                </td>
              </tr>
              <?php endforeach; ?>  
            <?php endif; ?>
          </tbody>
        </table>
    </div><!--/col-->
  </div>
</div><!--/row-->

<div class="row hidden">
  <div class='col-md-6'>
    <table class='table table-bordered'>
      <tr>
        <td><i class='fa fa-eye'></i></td>
        <td>A visible menu item is shown in the navigation bar, provided permissions has been granted.</td>
      </tr>

      <tr>
        <td><i class='fa fa-eye-slash'></i></td>
        <td>A non-visible menu item will not be shown in navigation bar, even if permission is granted. It is used for sections or parts of the pages.</td>
      </tr>
    </table>  
  </div>
</div>  
  
</div><!--/container-->
<!-- /Main -->
