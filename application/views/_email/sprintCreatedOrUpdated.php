<div style='width:100%; text-align: center;'>
    <h3><?php echo strtoupper($title);?></h3>
</div>
<div style="margin:0px auto;max-width:800px;">
    <table align="center" border="1" cellpadding="10" cellspacing="0" role="presentation" style="width:100%;">
        <tbody>
            <tr>
                <th class='text-left'>Sprint Name</th>
                <td><?php echo $sprint->name;?></td>
            </tr>
            <tr>
                <th class='text-left'>Project</th>
                <td><?php echo $sprint->project_name;?></td>
            </tr>
            <tr>
                <th class='text-left'>Customer</th>
                <td><?php echo $sprint->company_name;?></td>
            </tr>
            <tr>
                <th class='text-left'>Created By</th>
                <td><?php echo "{$sprint->author_name} / {$sprint->author_email}";?></td>
            </tr>
        </tbody>
    </table>
</div>

<div style='margin:30px auto; max-width:800px;'>
    <a class='btn' href="<?php echo $link;?>">
        <div class="label"><?php echo $link_label;?></div>
    </a>
</div>