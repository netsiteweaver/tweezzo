<?php 

function getNextStage($currentStageId)
{
    $ci = & get_instance();
    return $ci->db->query("SELECT id,name,abbr,stage_color,foreground_color 
                        FROM `stages` 
                        WHERE sequence > (
                            SELECT sequence 
                            FROM stages 
                            WHERE id = '".$currentStageId."'
                        ) order by sequence limit 1")->row();
}