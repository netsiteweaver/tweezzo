<div class="row">
    <div class="col-md-6 pull-right text-right">
        <label for="enable_sorting">
        <input type="checkbox" id='enable_sorting' checked> Enable to Drag rows vertically to re-order
        </label>
    </div>
</div>
    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="box">
                <div class="box-body table-responsive no-padding">
                    <table id="resources" class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Controller</th>
                                <th>Method</th>
                                <th>Display Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($resources['main'] as $line): ?>
                                <?php if($line->visible=='0') continue;?>
                            <tr data-id="<?php echo $line->mid;?>">
                               <td><?php echo $line->nom; ?></td>
                               <td><?php echo $line->controller; ?></td>
                               <td><?php echo $line->action; ?></td>
                                <td class="display_order"><?php echo $line->display_order;?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
    
                    </table>
                </div>
            </div>
        </div>
    </div>
    