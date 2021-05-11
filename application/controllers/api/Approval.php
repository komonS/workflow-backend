<?php
defined('BASEPATH') or exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
require(APPPATH . 'libraries/RestController.php');
require(APPPATH . 'libraries/Format.php');

use chriskacerguis\RestServer\RestController;

class Approval extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('approvalmodel');
        $this->load->model('flowmodel');
    }


    public function create_post()
    {
        $approval = $this->post('approval');
        $flowID = $this->post('flowID');

        $id = 0;
        foreach ($approval as $row) {
            $arr = array(
                'approval'          => $row['approval'],
                'workFlowStepID'    => $row['stepID'],
                'approvalStatusID'  => 1,
                'flowID'            => $flowID
            );
            $id = $this->approvalmodel->create($arr);
            //array_push($result,$arr);
        }
        if ($id != 0) {
            $result = array(
                "status"     => "success",
                "detail"    => "create approval success"
            );
        } else {
            $result = array(
                "status"     => "error",
                "detail"    => "can't create approval                                                                                                                                                                                                                                                             "
            );
        }

        $this->response($result, 200);
    }

    public function approval_get()
    {
        $flowID = $this->get('flowID');
        $where = "flowID = " . $flowID;
        $result = $this->approvalmodel->selectData($where);
        $this->response($result, 200);
    }

    public function changestatus_post()
    {
        $flowID = $this->post("flowID");
        $flowApprovalID = $this->post("flowApprovalID");
        $status = $this->post("status");


        /// change status approval
        $arr = array(
            "approvalStatusID" => $status
        );
        $where = "flowApprovalID = " . $flowApprovalID;

        $this->approvalmodel->update($arr, $where);



        /// check condition for update data in relation table
        if ($status == "3") { //status = approve
            $approval = $this->approvalmodel->checkApprove($flowID); // get approval have status = 1

            if (count($approval) > 0) { //change status next approval to 2 (waiting status)
                $arr = array(
                    "approvalStatusID" => 2
                );
                $where = "flowApprovalID = '" . $approval[0]->flowApprovalID . "'";
                $this->approvalmodel->update($arr, $where);
            }
        } else if ($status == "4") { //status = reject
            $arr = array(
                "flowStatusID"  => 3
            );
            $where = "flowID = " . $flowID;
            $this->flowmodel->update($arr, $where); // change status flow to cancel

        } else if ($status == "5") { //status = rework
            $arr = array(
                "approvalStatusID" => 1
            );
            $where = "flowID = " . $flowID;
            $this->approvalmodel->update($arr, $where); // change status all approval to 1 (ready)

            $arr = array(
                "flowStatusID"  => 4
            );
            //$where = "flowID = " . $flowID;
            $this->flowmodel->update($arr, $where); // change status flow to rework
        }

        $result = array(
            "status"    => "success"
        );
        $this->response($result, 200);
    }
}
