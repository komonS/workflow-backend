<?php

/**
 * 
 */
class StatusModel extends CI_model
{



    public function getStatus()
    {
        $query = $this->db->select('*')
            ->from('WorkflowStatus')
            ->get();
        $result = $query->result();

        return $result;
    }
}
