<?php

/**
 * 
 */
class FlowLogModel extends CI_model
{

    public function create($arr)
    {
        $this->db->insert('FlowLog', $arr);
       // return $this->db->insert_id();
    }

    public function getFlowLog($where = "1=1")
    {
        $query = $this->db->select('*')
            ->from('FlowLog')
            ->where($where)
            ->get();
        $result = $query->result();
        return $result;
    }
}
