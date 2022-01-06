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

class Step extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('stepmodel');
    }

    public function step_post()
    {
        $step = $this->post('step'); // data is array
        /*
        example
        [
            {
                "name": "test name",
                "number" : 1,
                "workflowID" : 1
            },
            {
                "name": "test name",
                "number" : 2,
                "workflowID" : 1
            },
        ]
        */

        if (count($step) > 0) {
            foreach ($step as $row) {
                $arr = array(
                    "workFlowStepName"  => $row['name'],
                    "ruleID"            => 1,
                    "stepNumber"        => $row['number'],
                    "workflowID"        => $row['workflowID']
                );
                $id = $this->stepmodel->createStep($arr);
            }
            if ($id != "") {
                $result = array(
                    "status"    => "success",
                    "detail"    => "create step workflow compleated"
                );
            } else {
                $result = array(
                    "status"    => "error",
                    "detail"    => "can't create step workflow"
                );
            }
        } else {
            $result = array(
                "status"    => "error",
                "detail"    => "please insert step workflow"
            );
        }

        $this->response($result, 200);
    }

    public function step_get()
    {
        $workflowID = $this->get("workflowID");
        $workflowStepID = $this->get("workflowStepID");
        $stepNumber = $this->get('stepNumber');

        if ($workflowStepID != "") {
            $where = "workflowStepID = '$workflowStepID'";
        } else if ($stepNumber != "") {
            $where = "workflowID = '$workflowID' AND stepNumber = '$stepNumber'";
        } else {
            $where = "workflowID = '$workflowID'";
        }



        $result = $this->stepmodel->getStep($where);

        $this->response($result, 200);
    }

    public function step_put()
    {
        $workflowStepID = $this->put("workflowStepID");
        $workflowStepName = $this->put("name");
        $address = $this->put("address");

        $arr = array(
            "workflowStepName"  => $workflowStepName,
            "formAddress"       => $address
        );
        $where = "workflowStepID = '$workflowStepID'";

        $this->stepmodel->updateStep($arr, $where);

        $result = array(
            "status"    => "success",
            "detail"    => "update step workflow compleated"
        );

        $this->response($result, 200);
    }
}
