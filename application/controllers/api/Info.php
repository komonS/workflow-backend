<?php
defined('BASEPATH') or exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Headers: origin, content-type, accept');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
require(APPPATH . 'libraries/RestController.php');
require(APPPATH . 'libraries/Format.php');

use chriskacerguis\RestServer\RestController;

class Info extends RestController
{
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('approvalmodel');
        $this->load->model('workflowmodel');
        $this->load->model('flowmodel');
    }

    public function workflowinfo_get()
    {
        $all = $this->workflowmodel->countWorkflow();
        $active = $this->workflowmodel->countWorkflow("workflowStatusID = 1");
        $inactive = $this->workflowmodel->countWorkflow("workflowStatusID = 0");

        $result = array(
            "all"           => $all[0]->count,
            "active"        => $active[0]->count,
            "inactive"      => $inactive[0]->count
        );

        $this->response($result, 200);
    }

    public function flowinfo_get()
    {
        $workflowID = $this->get("workflowID");
        $all = $this->flowmodel->countFlow("workflowID = '$workflowID'");
        $active = $this->flowmodel->countFlow("(flowStatusID = 1 OR flowStatusID = 4) AND workflowID = $workflowID");
        $finish = $this->flowmodel->countFlow("(flowStatusID = 2 OR flowStatusID = 3) AND workflowID = $workflowID");

        $result = array(
            "all"           => $all[0]->count,
            "active"        => $active[0]->count,
            "finish"      => $finish[0]->count
        );

        $this->response($result, 200);
    }
}
