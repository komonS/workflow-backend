<?php


class JobModel extends CI_model
{
    public function getJob($where = "")
    {
        if ($where != "") {
            $query = $this->db->select('*')
                ->from('FlowApproval')
                ->join("Flow", "Flow.flowID =  FlowApproval.flowID")
                ->join("WorkFlows", "WorkFlows.workflowID = Flow.workflowID")
                ->join("WorkFlowStep", "WorkFlows.workflowID = WorkFlowStep.workflowID")
                ->where($where)
                ->get();
            $result = $query->result();
        } else {
            $query = $this->db->select('*')
                ->from('FlowApproval')
                ->join("Flow", "Flow.flowID =  FlowApproval.flowID")
                ->join("WorkFlows", "WorkFlows.workflowID = Flow.workflowID")
                ->join("WorkFlowStep", "WorkFlows.workflowID = WorkFlowStep.workflowID")
                ->get();
            $result = $query->result();
        }
        return $result;
    }
}
