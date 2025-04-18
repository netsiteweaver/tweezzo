<div class="row">
    <div class="col-md-6">
        <p><strong class="red">CAUTION: This action is IRREVERSIBLE!</strong>. 
        <p>You can truncate <strong>all</strong> tables or <strong>select some</strong> tables to be truncated.</p>
        <p>Some tables requires their complimentary also to be deleted otherwise the system might not work properly.</p>
        <table id="tables-list" class="table table-bordered">
            <thead>
                <tr>
                    <th>TABLE</th>
                    <th>ROWS</th>
                    <th>
                        <input type="checkbox" name="" id="select-all">
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($all_tables as $table):?>
                <?php //if(stristr($table['table'],"_details")=== FALSE): ?>
                <tr>
                    <td><?php echo $table['table'];?></td>
                    <td><?php echo $table['rows'];?></td>
                    <td class='text-center'>
                        <input type="checkbox" class="select-table" id="" <?php echo ($table['rows']==0)?'disabled':'';?>>
                    </td>
                </tr>
                <?php //endif;?>
            <?php endforeach;?>
            </tbody>
        </table>
        <div class="btn btn-info" id="proceed"><i class="fa fa-database"></i> Proceed</div>
    </div>
</div>