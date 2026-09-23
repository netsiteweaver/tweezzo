<style>
    .info-box-number {
        position: absolute;
        right: 10px;
        top: 0px;
        font-size: 2em;
        float: right;
        background-color: #000;
        color: #fff;
        padding: 0px 15px;
        border-radius: 5px;
        box-shadow: 5px 5px 5px #ccc;
    }

    /* Online Users Dashboard Styles */
    .user-list {
        max-height: 300px;
        overflow-y: auto;
    }
    .online-user-item {
        padding: 8px;
        margin-bottom: 5px;
        background: #f4f4f4;
        border-radius: 4px;
        display: flex;
        align-items: center;
    }
    .online-user-item img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 10px;
    }
    .online-user-item .user-info {
        flex: 1;
    }
    .online-user-item .user-name {
        font-weight: bold;
        font-size: 14px;
    }
    .online-user-item .user-email {
        font-size: 11px;
        color: #666;
    }
    .online-user-item .user-status {
        color: #28a745;
        font-size: 10px;
    }

    #dashboard-blocks .table tbody {
        display: block;
        max-height: 250px;
        overflow-y: auto;
    }
    #dashboard-blocks .table thead,
    #dashboard-blocks .table tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    /* --- Customisable blocks --- */
    .dashboard-block {
        position: relative;
    }
    .dashboard-block-tools {
        display: none;
        position: absolute;
        top: 2px;
        right: 12px;
        z-index: 20;
    }
    .dashboard-block-tools .btn {
        padding: 1px 7px;
        line-height: 1.4;
    }
    #dashboard-blocks.edit-mode .dashboard-block-tools {
        display: block;
    }
    #dashboard-blocks.edit-mode .dashboard-block {
        outline: 1px dashed #aaa;
        outline-offset: -4px;
        cursor: move;
    }
    #dashboard-blocks.edit-mode .dashboard-block-body {
        pointer-events: none;
    }
    /* Hidden blocks vanish normally, and come back faded while customising. */
    .dashboard-block.is-hidden {
        display: none;
    }
    #dashboard-blocks.edit-mode .dashboard-block.is-hidden {
        display: block;
        opacity: .40;
    }
    .dashboard-row-break {
        height: 0;
    }
    #dashboard-blocks.edit-mode .dashboard-row-break {
        height: 14px;
        border-top: 2px dashed #3c8dbc;
        margin: 4px 0 10px;
    }
    .dashboard-block.has-break .dashboard-block-break {
        background-color: #3c8dbc;
        border-color: #3c8dbc;
        color: #fff;
    }
    .dashboard-block-placeholder {
        border: 2px dashed #3c8dbc;
        background: #f0f7fb;
        margin-bottom: 1rem;
    }
</style>

<div class="row">
    <div class="col-12 text-right mb-2">
        <div class="btn-group">
            <button type="button" id="dashboard-customise-toggle" class="btn btn-sm btn-default">
                <i class="fas fa-th-large"></i> Customise
            </button>
            <button type="button" id="dashboard-customise-reset" class="btn btn-sm btn-default" style="display:none;">
                <i class="fas fa-undo"></i> Reset to default
            </button>
        </div>
        <span id="dashboard-customise-status" class="text-muted small ml-2"></span>
    </div>
</div>

<div class="row"
     id="dashboard-blocks"
     data-save-url="<?php echo base_url('ajax/dashboard/savePreferences');?>"
     data-reset-url="<?php echo base_url('ajax/dashboard/resetPreferences');?>">
<?php foreach($blocks as $block):?>
    <div class="<?php echo $block['width'];?> dashboard-block<?php echo $block['visible'] ? '' : ' is-hidden';?><?php echo $block['row_break'] ? ' has-break' : '';?>"
         data-block-key="<?php echo $block['key'];?>">
        <div class="dashboard-block-tools">
            <button type="button" class="btn btn-xs btn-default dashboard-block-break"
                    title="<?php echo $block['row_break'] ? 'Do not start a new row here' : 'Start a new row after this block';?>">
                <i class="fas fa-level-down-alt"></i>
            </button>
            <button type="button" class="btn btn-xs btn-default dashboard-block-toggle"
                    title="<?php echo $block['visible'] ? 'Hide this block' : 'Show this block';?>">
                <i class="fas <?php echo $block['visible'] ? 'fa-eye-slash' : 'fa-eye';?>"></i>
            </button>
        </div>
        <div class="dashboard-block-body"><?php echo $block['html'];?></div>
    </div>
    <?php /* Flex row break. Hidden blocks must not leave a phantom break behind. */ ?>
    <?php if($block['row_break']):?>
    <div class="w-100 dashboard-row-break<?php echo $block['visible'] ? '' : ' is-hidden';?>"></div>
    <?php endif;?>
<?php endforeach;?>
</div>
