<div style='width:100%; text-align: center;'>
    <h3><?php echo strtoupper($title);?></h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th class='text-left'>Project Name</th>
                <td><?php echo $project->name;?></td>
            </tr>
            <tr>
                <th class='text-left'>Project Description</th>
                <td><?php echo $project->description;?></td>
            </tr>
            <tr>
                <th class='text-left'>Start Date</th>
                <td><?php echo $project->start_date;?></td>
            </tr>
            <tr>
                <th class='text-left'>End Date</th>
                <td><?php echo $project->end_date;?></td>
            </tr>
            <tr>
                <th class='text-left'>Customer</th>
                <td><?php echo $project->company_name;?></td>
            </tr>
            <tr>
                <th class='text-left'>Created By</th>
                <td><?php echo "{$project->author_name} / {$project->author_email}";?></td>
            </tr>
        </tbody>
    </table>
</div>

<div style='margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php echo $link;?>">
        <div class="label"><?php echo $link_label;?></div>
    </a>
</div>