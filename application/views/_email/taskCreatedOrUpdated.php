<div style='width:100%; text-align: center;'>
    <h3><?php echo strtoupper($title);?></h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th class='text-left'>CUSTOMER</th>
                <td><?php echo $projectInfo->customerName;?></td>
            </tr>
            <tr>
                <th class='text-left'>PROJECT</th>
                <td><?php echo $projectInfo->projectName;?></td>
            </tr>
            <tr>
                <th class='text-left'>SPRINT</th>
                <td><?php echo $projectInfo->sprintName;?></td>
            </tr>
            <tr>
                <th class='text-left'>TASK NUMBER</th>
                <td><?php echo $data['task_number'];?></td>
            </tr>
            <tr>
                <th class='text-left'>SECTION</th>
                <td><?php echo $data['section'];?></td>
            </tr>
            <tr>
                <th class='text-left'>TASK NAME</th>
                <td><?php echo $data['name'];?></td>
            </tr>
            <tr>
                <th class='text-left'>TASK DESCRIPTION</th>
                <td><?php echo nl2br($data['description']);?></td>
            </tr>
            <tr>
                <th class='text-left'>STAGE</th>
                <td>
                    <span class="stage-button stage-button-<?php echo $data['stage'];?>" ><?php echo strtoupper(str_replace("_"," ",$data['stage']));?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div style='margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php echo $link;?>">
        <div class="label"><?php echo $link_label;?></div>
    </a>
</div>