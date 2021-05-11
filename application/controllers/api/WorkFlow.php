<?php
defined('BASEPATH') or exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
require(APPPATH . 'libraries/RestController.php');
require(APPPATH . 'libraries/Format.php');

use chriskacerguis\RestServer\RestController;

class WorkFlow extends RestController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('workflowmodel');
    }

    public function workflow_post()
    {
        $name = $this->post('name');
        $status = $this->post('status');

        $arr = array(
            'workflowName' => $name,
            "workflowStatusID" => $status
        );
        $id = $this->workflowmodel->create($arr);

        if ($id != "") {
            $result = array(
                "status"        => "success",
                "detail"        => "create workflow success",
                "workflowID"    => $id
            );
        } else {
            $result = array(
                "status"        => "error",
                "detail"        => "can'not create workflow"
            );
        }

        $this->response($result, 200);
    }

    public function workflow_get()
    {
        $workflowID = $this->get('workflowID');
        if ($workflowID != "") {
            $where = "workflowID = " . $workflowID;
            $result = $this->workflowmodel->selectData($where);
        } else {
            $result = $this->workflowmodel->selectData();
        }
        $this->response($result, 200);
    }

    public function workflow_put()
    {
        $name = $this->put('name');
        $status = $this->put('status');
        $workflowID = $this->put('workflowID');

        $arr = array(
            'workflowName'        => $name,
            'workflowStatusID'    => $status
        );
        $where = "workflowID = " . $workflowID;

        $this->workflowmodel->update($arr, $where);
        $result = array(
            "status" => "success",
            "detail" => "update workflow completed"
        );
        $this->response($result, 200);
    }

    public function workflow_delete()
    {
        $workflowID = $this->delete('workflowID');
        $where = "workflowID = " . $workflowID;
        $this->workflowmodel->delete($where);

        $result = array(
            "status" => "success",
            "detail" => "delete workflow completed"
        );
        $this->response($result, 200);
    }
}
