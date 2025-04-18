<div class="no-print">
<?php if( (isset($pagination)) && (!empty($pagination)) ) echo $pagination;?>
</div>
<div class="row no-print mb-3">
    <div class="col-md-1">
        <label for="">Per Page</label>
        <select name="rows_per_page" id="rows_per_page" class="form-control monitor">
            <option value="10" <?php echo ($rpp=='10')?'selected':''?>>10 Rows</option>
            <option value="25" <?php echo ($rpp=='25')?'selected':''?>>25 Rows</option>
            <option value="50" <?php echo ($rpp=='50')?'selected':''?>>50 Rows</option>
            <option value="100" <?php echo ($rpp=='100')?'selected':''?>>100 Rows</option>
        </select>
    </div>
    <div class="col-md-2">
        <label for="">Date Range</label>
        <div class="input-group">
            <input type="text" class='form-control date_filter_input monitor' name=''>
            <div class="input-group-text date_filter cursor-pointer">
                <div class="fa fa-calendar"></div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <label for="user_filter">User</label>
        <select name="user_filter" id="" class="form-control user_filter monitor">
            <option value="">Select a User</option>
            <?php foreach ($users as $user):?>
            <option value="<?php echo $user->name;?>" <?php echo ($filter_user==$user->name)?'selected':''?>><?php echo $user->name;?></option>
            <?php endforeach;?>
        </select>
    </div>
    <div class="col-md-2">
        <label for="">IP</label>
        <select name="ip_filter" id="" class="form-control ip_filter monitor">
            <option value="">Select an IP</option>
            <?php foreach ($ips as $ip):?>
            <option value="<?php echo $ip->ip;?>" <?php echo ($filter_ip==$ip->ip)?'selected':''?>><?php echo $ip->ip;?></option>
            <?php endforeach;?>
        </select>
    </div>
    <div class="col-md-1">
        <label for="">Controller</label>
        <input type="text" name="controller_filter" class="form-control controller_filter monitor" value="<?php echo $filter_controller;?>" placeholder="Controller">
    </div>
    <div class="col-md-1">
        <label for="">Method</label>
        <input type="text" name="method_filter" class="form-control method_filter monitor" value="<?php echo $filter_method;?>" placeholder="Method">
    </div>
    <div class="col-md-1">
        <div style='margin-top:25px;' class="btn btn-success btn-outline btn-block proceed"><i class='fa fa-filter'></i> Apply</div>
    </div>
    <div class="col-md-1">
        <div style='margin-top:25px;' class="btn btn-warning btn-block reset"><i class='fa fa-undo'></i> Clear</div>
    </div>
    <div class="col-md-1 pull-right no-print">
        <div style='margin-top:25px;' class="btn btn-default btn-block print"><i class='fa fa-print'></i> Print</div>
    </div>
</div>
<div class="print-only">
    <input type="hidden" name="start_date" value="<?php echo isset($_SESSION['filter_start_date'])?$_SESSION['filter_start_date']:date('Y-m-d');?>">
    <input type="hidden" name="end_date" value="<?php echo isset($_SESSION['filter_end_date'])?$_SESSION['filter_end_date']:date('Y-m-d');?>">

    <div class="row">
        <div class="col-md-12">
            <h3 class='text-center'>Audit Trail Report</h3>
            <h6 class='text-center'>as at <?php echo date('Y-m-d @ H:i');?></h6>
        </div>
    </div>
    <?php if( (!empty($filter_start_date)) || (!empty($filter_end_date)) || (!empty($filter_ip)) || (!empty($filter_controller)) || (!empty($filter_method))):?>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-compact" style='border:2px solid #4c4c4c;'>
                <!-- <tr style='border-bottom:2px solid #4c4c4c;'>
                    <th colspan="5" >FILTERS APPLIED</th>
                </tr> -->
                <tr>
                    <th>DATE</th>
                    <th>USER</th>
                    <th>IP</th>
                    <th>CONTROLLER</th>
                    <th>METHOD</th>
                </tr>
                <tr>
                    <td><?php echo "{$filter_start_date} - {$filter_end_date}";?></td>
                    <td><?php echo $filter_user;?></td>
                    <td><?php echo $filter_ip;?></td>
                    <td><?php echo $filter_controller;?></td>
                    <td><?php echo $filter_method;?></td>
                </tr>
            </table>
        </div>
    </div>
    <?php endif;?>
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-compact">
            <tr>
                <th>#</th>
                <th>DATE</th>
                <th>USER</th>
                <th>IP</th>
                <th>CONTROLLER</th>
                <th>METHOD</th>
                <th class='no-print' style='width:100px;'>&nbsp;</th>
            </tr>

            <?php foreach($rows as $i => $row):?>
            <tr>
                <td style='width:50px;text-align:center;'><?php echo $i+1;?></td>
                <td><?php echo $row->date;?></td>
                <td><?php echo $row->name;?></td>
                <td><?php echo $row->ip;?></td>
                <td><?php echo $row->controller;?></td>
                <td><?php echo $row->method;?></td>
                <td class='no-print'>
                    <a href='<?php echo base_url('audittrail/view/'.$row->id.'?page='.$this->uri->segment(3,1));?>'><div class="btn btn-info btn-md"><i class="fa fa-eye"></i> View</div></a>
                </td>
            </tr>
            <?php endforeach;?>
        </table>
    </div>
</div>
<div class="row no-print">
    <div class="col-md-12">
        <?php echo $pagination;?>
    </div>
</div>