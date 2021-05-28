<?php

/**
 * 
 */
class WorkFlowModel extends CI_model
{
    public function create($arr)
    {
        $this->db->insert('WorkFlows', $arr);
        return $this->db->insert_id();
    }

    public function update($arr, $where)
    {
        $this->db->set($arr);
        $this->db->where($where);
        $this->db->update('WorkFlows');
    }


    public function delelte($where)
    {
        $this->db->where($where);
        $this->db->delete('WorkFlows');
    }

    public function selectData($where = "")
    {
        if ($where != "") {
            $query = $this->db->select('*')
                ->from('WorkFlows')
                ->join("WorkflowStatus", "WorkflowStatus.workflowStatusID = WorkFlows.workflowStatusID", "inner")
                ->where($where)
                ->get();
            $result = $query->result();
        } else {
            $query = $this->db->select('*')
                ->from('WorkFlows')
                ->join("WorkflowStatus", "WorkflowStatus.workflowStatusID = WorkFlows.workflowStatusID", "inner")
                ->get();
            $result = $query->result();
        }

        return $result;
    }

    public function countWorkflow($where = "")
    {
        if ($where != "") {
            $query = $this->db->select('COUNT(*) AS count')
                ->from('WorkFlows')
                ->where($where)
                ->get();
            $result = $query->result();
            return $result;
        } else {
            $query = $this->db->select('COUNT(*) AS count')
                ->from('WorkFlows')
                ->get();
            $result = $query->result();
            return $result;
        }
    }
}
