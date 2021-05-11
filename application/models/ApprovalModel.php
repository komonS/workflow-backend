<?php

/**
 * 
 */
class ApprovalModel extends CI_model
{

    public function create($arr)
    {
        $this->db->insert('FlowApproval', $arr);
        return $this->db->insert_id();
    }

    public function selectData($where = "")
    {
        if ($where != "") {
            $query = $this->db->select('*')
                ->from('FlowApproval')
                ->join('ApprovalStatus', 'FlowApproval.approvalStatusID = ApprovalStatus.approvalStatusID','left')
                ->where($where)
                ->get();
            $result = $query->result();
        } else {
            $query = $this->db->select('*')
                ->from('FlowApproval')
                ->join('ApprovalStatus', 'FlowApproval.approvalStatusID = ApprovalStatus.approvalStatusID','left')
                ->get();
            $result = $query->result();
        }

        return $result;
    }

    public function checkApprove($flowID)
    {
        $where = "flowID = '" . $flowID . "' AND approvalStatusID = '1'";
        $query = $this->db->select('TOP (1) *')
            ->from('FlowApproval')
            ->where($where)
            ->get();
        $result = $query->result();
        return $result;
    }

    public function update($arr, $where)
    {
        $this->db->set($arr);
        $this->db->where($where);
        $this->db->update('FlowApproval');
    }
}
