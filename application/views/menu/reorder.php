<div class="row">
    <div class="col-md-4">
        <p>Please drag the menu items vertically to re-order them.</p>
        <table id="reorder-menu" class="table bordered">
            <thead>
                <tr>
                    <th>Menu Item</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows as $row):?>
                <tr data-id="<?php echo $row->id;?>">
                    <td>
                        <i class="fa <?php echo $row->class;?>"></i> <?php echo $row->nom;?>
                        <div class='pull-right'><i class="fa fa-bars"></i></div>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>
