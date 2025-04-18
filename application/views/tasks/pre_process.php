<div class="row">
    <div class="col-md-4">
        <table id='fields_match' class="table table-bordered">
            <thead>
                <tr>
                    <th>FIELD NAME</th>
                    <th>IMPORTED COLUMN</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($fields as $field):?>
                <tr>
                    <td><?php echo $field;?></td>
                    <td>
                        <select name="column_select" class='column_select form-control'>
                            <option value=''>Select</option>
                            <?php foreach($headers as $header):?>
                            <option value='<?php echo $header;?>'><?php echo $header;?></option>
                            <?php endforeach;?>
                            <option value='manual'>Manual</option>
                        </select>
                        <input type="text" class="form-control d-none" name="">
                    </td>
                    <td></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>

</div>

<div class="row">
    <div class="col-md-2">
        <button class="btn btn-success" id="proceed">Import</button>
    </div>
</div>