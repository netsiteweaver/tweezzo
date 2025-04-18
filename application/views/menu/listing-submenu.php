<?php if($perms['can_add']): ?>
<div class="row">
  <div class="col-md-8">
      <a href="<?php echo base_url('/menu/add/'.$parent_name->controller); ?>"><button class="btn btn-xs btn-flat btn-success"><i class="fa fa-plus"></i> Add</button></a> 
  </div>
</div>
<?php endif; ?>
<div class="row">
  <div class="col-xs-12">
    <h5><label>Main Menu:</label> <?php echo $parent_name->nom; ?></h5>
    <div class="box">
      <div class="box-body table-responsive no-padding">      
        <table id="" class="table table-border">
          <thead>
            <tr>
              <th style='display:none'>ID</th>
              <th>Icon</th>
              <th>Resource Name</th>
              <th>Controller</th>
              <th>Action</th>
              <th>Normal</th>
              <th>Admin</th>
              <th>Root</th>
              <th>Order</th>
              <!-- <th>Sub Menu</th> -->
              <th>Operations</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($resources)): ?>
              <?php foreach($resources as $i => $product): ?>
              <tr>
                <td style='display:none'><?php echo $product->mid; ?></td>
                <td class="text-center"><span class="fa <?php echo $product->class; ?>" aria-hidden="true"></span></td>
                <td><?php echo $product->nom; ?></td>
                <td><?php echo $product->controller; ?></td>
                <td><?php echo $product->action; ?></td>
                <td class="text-center"><input type="checkbox" disabled <?php echo ($product->Normal==1)?'checked':''; ?>></td>
                <td class="text-center"><input type="checkbox" disabled <?php echo ($product->Admin==1)?'checked':''; ?>></td>
                <td class="text-center"><input type="checkbox" disabled <?php echo ($product->Root==1)?'checked':''; ?>></td>
                <td><?php echo $product->display_order; ?></td>
                <td class="actions">
                  <?php if($product->type != 'section'):?>
                    <?php if($perms['can_edit']): ?>
                    <a href='<?php echo base_url('/menu/edit/' . $product->mid . '/'.$this->uri->segment(3)); ?>'><button class="btn btn-sm btn-flat btn-primary"><i class='fa fa-edit'></i> Edit</button></a> 
                    <?php endif; ?>
                  <?php endif; ?>
                  
                  <?php if($perms['can_delete']): ?>
                  <a href='#' class='resource'><button data-id="<?php echo $product->mid; ?>" data-url="<?php echo base_url("menu/deleteAjax"); ?>" class="deleteAjax btn btn-sm btn-flat btn-danger"><i class='fa fa-trash'></i></button></a> 
                  <?php endif; ?>

                  <?php if($perms['can_sort']): ?>
                  <!--<a href="<?php echo base_url('/menu/sort_sub/'.$this->uri->segment(3)); ?>"><i class="fa fa-sort"></i></a>-->
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>  
            <?php endif; ?>
          </tbody>
        </table>
      </div><!--/col-->
      <div class="box-footer">
        <a href="<?php echo base_url("menu/listing"); ?>"><button class="btn btn-xs btn-flat btn-info"><i class="fa fa-chevron-circle-left"></i> Main Menu Listing</button></a>
      </div>
    </div>
  </div>
</div><!--/container-->
<!-- /Main -->
