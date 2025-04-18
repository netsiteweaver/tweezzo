<div style='margin:30px auto; max-width:800px;'>
    <table style='width:100%;margin-bottom:20px;'>
        <tbody>
            <tr>
                <td style='padding: 5px 10px; font-size: 18px;'>Dear <?php echo $addressee;?></td>
            </tr>
            <tr>
                <th style='padding: 5px 10px; font-size: 18px;'>A note has been added.</th>
            </tr>
        </tbody>
    </table>
    <table style='width:100%;margin-bottom:20px;'>
        <tbody>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Customer</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo $taskDetails->company_name;?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Project</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo $taskDetails->project_name;?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Sprint</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo $taskDetails->sprint_name;?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Task Number</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo $taskDetails->task_number;?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Section</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo $taskDetails->section;?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Task</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo $taskDetails->name;?></td>
            </tr>
            <tr>
                <th style='width: 150px; text-align: left; border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'>Task Description</th>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo nl2br($taskDetails->description);?></td>
            </tr>
        </tbody>
    </table>
    <br>
    <p>The following note has been added by <b><?php echo $userEmail;?></b></p>
    <table style='width:100%;margin-bottom:20px;'>
        <tbody>
            <tr>
                <td style='border:1px solid #CCC; padding: 5px 10px; font-size: 18px;'><?php echo nl2br($notes);?></td>
            </tr>
        </tbody>
    </table>
</div>
<!-- <div style='margin:30px auto; max-width:600px;'>
    <a class='btn' href="<?php //echo $link;?>">
        <div class="label"><?php //echo $link_label;?></div>
    </a>
</div> -->

