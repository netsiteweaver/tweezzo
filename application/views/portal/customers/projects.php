<div class="row table-responsive">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr class='text-center'>
                    <th>PROJECT NAME</th>
                    <!-- <th>DESCRIPTION</th> -->
                    <th>START DATE</th>
                    <th>END DATE</th>
                    <th>CREATED</th>
                    <th># OF SPRINTS</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($projects as $project):?>
                <tr>
                    <td><?php echo $project->name;?></td>
                    <!-- <td><?php echo $project->description;?></td> -->
                    <td><?php echo $project->start_date;?></td>
                    <td><?php echo $project->end_date;?></td>
                    <td><?php echo "by {$project->createdBy} on {$project->created_on}";?></td>
                    <td class='text-center'><?php echo $project->sprints_count;?></td>
                    <td>
                        <a href="portal/customers/sprints?project_id=<?php echo $project->id;?>"><div class="btn btn-outline-secondary" style="color:#fff; background-color: var(--customersPortalBackground)"><i class="bi bi-eye"></i> View Sprints</div></a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>