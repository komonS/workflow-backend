<?php

/**
 * 
 */
class StepModel extends CI_model
{



    public function createStep($arr)
    {
        $this->db->insert('WorkFlowStep', $arr);
        return $this->db->insert_id();
    }

    public function getStep($where)
    {
        $query = $this->db->select('*')
            ->from('WorkFlowStep')
            ->where($where)
            ->get();
        $result = $query->result();
        return $result;
    }

    public function updateStep($arr,$where)
    {
        $this->db->where($where);
        $this->db->update("WorkFlowStep",$arr);
    }
}
