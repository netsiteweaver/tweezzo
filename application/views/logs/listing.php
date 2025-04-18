<div class="row">
    <div class="col-md-2">
        <fieldset>
            <label for="">Rows Per Page</label>
        
            <select name="" id="rows_per_page" class='form-control'>
                <option value="10" <?php echo($rows_per_page==10)?'selected':'';?>>10 Rows</option>
                <option value="25" <?php echo($rows_per_page==25)?'selected':'';?>>25 Rows</option>
                <option value="50" <?php echo($rows_per_page==50)?'selected':'';?>>50 Rows</option>
                <option value="100" <?php echo($rows_per_page==100)?'selected':'';?>>100 Rows</option>
            </select>
        </fieldset>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php echo $pagination;?>
    </div>
</div>
<div class="row">
    <div class="col-md-8 col-sm-10 col-xs-12">
        <div class="box">
            <div class="box-body table-responsive no-padding">
                <table id="logs-listing" class="table table-bordered">
                    <thead class='inverted'>
                        <tr>
                            <th>DATE</th>
                            <th>USER</th>
                            <th>ACTION</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>
                                <select name="filter_user" class='form-control' id="filter_user">
                                    <option value=""><?php echo (empty($filter_user))?'Filter User':'Clear Filter';?></option>
                                    <?php foreach($logs['users'] as $user):?>
                                        <option value='<?php echo $user->id;?>' <?php echo ($filter_user==$user->id)?'selected':'';?>><?php echo "$user->name ($user->ct entries)";?></option>
                                    <?php endforeach;?>
                                </select>
                            </td>
                            <td></td>
                            <td>
                                <!--<select name="filter_ip" class='form-control' id="filter_ip">
                                    <option value="">Filter IP</option>
                                    <?php //foreach($logs['ips'] as $ip):?>
                                        <option value='<?php //echo $ip->ip;?>'><?php //echo "$ip->ip ($ip->ct entries)";?></option>
                                    <?php //endforeach;?>
                                </select>-->
                            </td>
                        </tr>
                        <?php if(!empty($logs['rows'])) foreach($logs['rows'] as $log):?>
                        <tr>
                            <td><?php echo date_format(date_create($log->created_on),'d F Y H:i:s');?></td>
                            <td><?php echo $log->name;?></td>
                            <td><?php echo "$log->controller/$log->method";?></td>
                            <td><?php echo $log->ip;?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php echo $pagination;?>
    </div>
</div>