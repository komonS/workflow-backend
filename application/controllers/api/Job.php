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

class Job extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('approvalmodel');
        $this->load->model('flowmodel');
        $this->load->model('jobmodel');
    }

    public function job_get()
    {
        $userID = $this->get("userID");
        $where = "FlowApproval.approval = '$userID' AND FlowApproval.approvalStatusID = '2' AND WorkFlowStep.stepNumber = FlowApproval.workFlowStepID";

        $result = $this->jobmodel->getJob($where);
        $this->response($result, 200);
    }
}
