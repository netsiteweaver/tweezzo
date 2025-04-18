<?php //var_dump($resources); ?>
<script>
var productOrder="";
$(function() {
  $( "#sortable" ).sortable({
    update: function(event, ui){
      $('.save_changes').fadeIn(250);
      productOrder = $(this).sortable('toArray')
    }
  });
  $( "#sortable" ).disableSelection();
});
</script>
<div class="container">
    <div class="col-md-12">
      <p>Please re-arrange the items as you would like it to appear. Click on <strong>Save Changes</strong> when done. </p>
    </div>
  <div class="row">
    <div class="col-md-4">

      <button class='save_changes btn btn-pos btn-pos-primary' style='display:none;'>Save My Changes</button><br /><br />

      <ul id="sortable">
        <?php foreach($resources as $i => $product): ?>
        <li id='<?php echo $product->mid; ?>' class="ui-state-default  <?php echo ($product->nom=="divider")?"resource_divider":"";?>">
            <span class="<?php echo ($product->nom!="divider")?"ui-icon ui-icon-arrowthick-2-n-s":"";?>"></span><a href='<?php echo base_url($this->uri->segment(1).'/resources/submenu/'.$product->mid); ?>'><?php echo $product->nom; ?></a><span class='menu_id'><?php echo $product->mid; ?></span>
        </li>
        <?php endforeach; ?>
      </ul>
    </div><!--/col-->
  </div><!--/row-->
</div><!--/container-->
<!-- /Main -->