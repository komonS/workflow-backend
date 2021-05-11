<?php
defined('BASEPATH') or exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
require(APPPATH . 'libraries/RestController.php');
require(APPPATH . 'libraries/Format.php');

use chriskacerguis\RestServer\RestController;

class Flow extends RestController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('flowmodel');
		$this->load->model('workflowmodel');
		$this->load->model('approvalmodel');
	}

	public function create_post()
	{
		$workflowID = $this->post("workflowID");
		$start = $this->post('start');
		$end = $this->post('end');
		$requester = $this->post('requester');

		/// check workflow data
		$where = "workflowID = " . $workflowID;
		$workflow = $this->workflowmodel->selectData($where);



		/// If there is data, create a workflow. 
		if (count($workflow) > 0) {
			$arr = array(
				'workflowID'	=> $workflowID,
				'startFlow'		=> $start,
				'endFlow'		=> $end,
				'requester'		=> $requester,
				'flowStatusID'	=> 1
			);

			$flowID = $this->flowmodel->create($arr);
			if ($flowID != "") {
				$result = array(
					"status" 		=> "success",
					"detail"		=> "create flow success",
					"workflowName" 	=> $workflow[0]->workflowName,
					"flowID"		=> $flowID
				);
			} else {
				$result = array(
					"status" 		=> "error",
					"detail"		=> "can't create flow ",
					"workflowName" 	=> $workflow[0]->workflowName,
					"flowID"		=> $flowID
				);
			}
		} else {
			$result = array(
				"status" => "error",
				"detail" => "Can't find workflowID, Please checking workflowID agian."
			);
		}

		$this->response($result, 200);
	}

	public function startflow_post()
	{
		$flowID = $this->post('flowID');

		$approval = $this->approvalmodel->checkApprove($flowID);

		if (count($approval) > 0) {
			$arr = array(
				"approvalStatusID" => 2
			);
			$where = "flowApprovalID = '" . $approval[0]->flowApprovalID . "'";
			$this->approvalmodel->update($arr, $where);

			$result = array(
				"status"	=> "success",
				"approval"	=>  $approval[0]->approval
			);
		} else {
			$result = array(
				"status"	=> "success",
				"approval"	=>  "null"
			);
		}



		$this->response($result, 200);
	}


	///ยังไม่ได้ใช้งานฟังก์ชั่นนี้
	protected function startflow($flowID)
	{
		$approval = $this->approvalmodel->checkApprove($flowID);

		if (count($approval) > 0) {
			$arr = array(
				"approvalStatusID" => 2
			);
			$where = "flowApprovalID = '" . $approval[0]->flowApprovalID . "'";
			$this->approvalmodel->update($arr, $where);
		}

		$result = array(
			"status"	=> "success",
			"approval"	=>  $approval[0]->approval
		);

		return $result;
	}
}
