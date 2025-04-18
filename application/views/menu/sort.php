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
  <div class="row">
    <div class="col-md-12">
      <p>Please re-arrange the items as you would like it to appear. Click on <strong>Save Changes</strong> when done. </p>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">

      <button class='save_changes' style='display:none;'>Save Changes</button><br /><br />

      <ul id="sortable" class="re-arrange">
        <?php foreach($resources['main'] as $i => $product): ?>
        <li id='<?php echo $product->mid; ?>' class="ui-state-default <?php echo ($product->nom=="divider")?"resource_divider":"";?>" style='position:relative;'>
          <!-- <span class="ui-icon ui-icon-arrowthick-2-n-s"></span> -->
          <!-- <span class='fa <?php echo $product->class; ?>'></span> -->
          <span class='ui-icon ui-icon-arrowthick-2-n-s'></span>
          
          <!-- <a href='<?php echo base_url($this->uri->segment(1).'/resources/submenu/'.$product->mid); ?>'><?php echo $product->nom; ?></a> -->
          <p><?php echo $product->nom; ?></p>
          <span class='menu_id'><?php echo $product->mid; ?></span>
        </li>
        <?php endforeach; ?>
      </ul>

    </div><!--/col-->
  </div><!--/row-->
</div><!--/container-->
<!-- /Main -->

<!--
font-size:0;
height:5px;
-->