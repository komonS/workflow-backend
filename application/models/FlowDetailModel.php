<?php 

/**
 * 
 */
class FlowDetailModel extends CI_model
{
	
	public function changeStatusFlow($fdID,$data)
	{
		$where = "fdID = '$fdID'";
        $this->db->set($data);
        $this->db->where($where);
        $this->db->update('FlowDetail');
	}

	public function getFlowDetail($fdID)
	{
		$where = "FlowDetail.fdID = '$fdID'";
      	$query = $this->db->select('*')
                 ->from('FlowDetail')
                 ->join('FlowApproval','FlowApproval.fdID = FlowDetail.fdID','inner')
                 ->where($where)
                 ->get();
      	$result = $query->result();
      	return $result;
	}

  public function getFlowStatusNow($fdID)
  {
        $where = "FlowDetail.fdID = '$fdID' AND FlowDetail.fdStatus = 'waiting'";
        $query = $this->db->select('*')
                 ->from('FlowDetail')
                 ->join('FlowApproval','FlowApproval.fdID = FlowDetail.fdID','inner')
                 ->where($where)
                 ->get();
        $result = $query->result();
        return $result;
  }

  public function getAllFlow()
  {
    $query = $this->db->select('*')
             ->from('FlowDetail')
             ->join('Flow','Flow.flID = FlowDetail.flID','inner')
             ->get();
    $result = $query->result();
    return $result;
  }
}