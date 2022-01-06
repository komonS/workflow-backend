<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Bangkok");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Headers: origin, content-type, accept');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
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
		$this->load->model('flowlogmodel');
	}

	public function create_post()
	{
		$workflowID = $this->post("workflowID");
		$start = $this->post('start');
		$end = $this->post('end');
		$requester = $this->post('requester');
		$flowName = $this->post('flowName');

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
				'flowStatusID'	=> 1,
				'flowName'		=> $flowName
			);

			$flowID = $this->flowmodel->create($arr);
			
			if ($flowID != "") {
				$log = array(
					"flowID"    => $flowID,
					"approval"  => $requester,
					"action"    => "create",
					"actionDate"    => date("Y-m-d H:i:s"),
					"comment"   => ""
				);
				$this->flowlogmodel->create($log);

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
		$userID = $this->post('userID');

		$approval = $this->approvalmodel->checkApprove($flowID);

		$log = array(
			"flowID"    => $flowID,
			"approval"  => $userID,
			"action"    => "start",
			"actionDate"    => date("Y-m-d H:i:s"),
			"comment"   => ""
		);
		$this->flowlogmodel->create($log);

		if (count($approval) > 0) {
			$arr = array(
				"approvalStatusID" => 2
			);
			$where = "flowApprovalID = '" . $approval[0]->flowApprovalID . "'";
			$this->approvalmodel->update($arr, $where);

			$data = array(
				"flowStatusID"	=> 1
			);
			$where = "flowID = '$flowID'";

			$this->flowmodel->update($data, $where);

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


	public function flow_get()
	{
		$status = $this->get("status");
		$workflowID = $this->get("workflowID");
		$flowID = $this->get('flowID');
		/*  
			status 1 is flow active
			status 2 is flow success
			status 3 is flow one
			stauts other is all flow
		*/
		if ($status == 1) {
			$where = "(Flow.flowStatusID = 1 OR Flow.flowStatusID = 4) AND Flow.workflowID = $workflowID";
			$result = $this->flowmodel->selectData($where);
		} else if ($status == 2) {
			$where = "(Flow.flowStatusID = 2 OR Flow.flowStatusID = 3) AND Flow.workflowID = $workflowID";
			$result = $this->flowmodel->selectData($where);
		} else if ($status == 3) {
			$where = "Flow.flowID = '$flowID'";
			$result = $this->flowmodel->selectData($where);
		} else if ($status == "one") {
			$where = "Flow.flowID = '$flowID'";
			$result = $this->flowmodel->selectData($where);
		} else {
			$where = "workflowID = $workflowID";
			$result = $this->flowmodel->selectData($where);
		}
		$this->response($result, 200);
	}

	public function flowuser_get()
	{
		$workflowID = $this->get("workflowID");
		$userID = $this->get("userID");

		$where = "workflowID = $workflowID" . " AND requester = '$userID'";
		$result = $this->flowmodel->selectData($where);

		$this->response($result, 200);
	}

	public function detail_get()
	{
		$flowID = $this->get('flowID');
		$where = "Flow.flowID = '$flowID'";
		$result = $this->flowmodel->getDetail($where);
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
