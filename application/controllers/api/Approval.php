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

class Approval extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('approvalmodel');
        $this->load->model('flowmodel');
        $this->load->model('flowlogmodel');
    }


    public function create_post()
    {
        $approval = $this->post('approval');
        $flowID = $this->post('flowID');

        $id = 0;
        //$result = $approval;

        foreach ($approval as $row) {
            $arr = array(
                'approval'          => $row['approval'],
                'workFlowStepID'    => $row['positionID'],
                'approvalStatusID'  => 1,
                'flowID'            => $flowID,
                "approveDate"      => date("Y-m-d H:i:s")
            );
            $id = $this->approvalmodel->create($arr);
            //array_push($result,$arr);
        }
        if ($id != 0) {
            $result = array(
                "status"     => "success",
                "detail"    => "create approval success",
                "nextApproval"  => $approval[0]
            );
        } else {
            $result = array(
                "status"     => "error",
                "detail"    => "can't create approval",
                "flowID"    => $flowID,
                "approval"  => $approval
            );
        }

        $this->response($result, 200);
    }

    public function approval_get()
    {
        $flowID = $this->get('flowID');
        $approval = $this->get('approval');
        $status = $this->get('status');
        if ($approval != null) {
            if($status == "waiting"){
                $where = "FlowApproval.flowID = " . $flowID . " AND FlowApproval.approval = '$approval' AND FlowApproval.approvalStatusID = '2'";
            }else if($status == "approve"){
                $where = "FlowApproval.flowID = " . $flowID . " AND FlowApproval.approval = '$approval' AND FlowApproval.approvalStatusID = '3'";
            }else if($status == "ready"){
                $where = "FlowApproval.flowID = " . $flowID . " AND FlowApproval.approval = '$approval' AND FlowApproval.approvalStatusID = '1'";
            }else{
                $where = "FlowApproval.flowID = " . $flowID . " AND FlowApproval.approval = '$approval'";
            }
            
        } else {
            $where = "flowID = " . $flowID;
        }

        $result = $this->approvalmodel->selectData($where);
        $this->response($result, 200);
    }

    public function changestatus_post()
    {
        $flowID = $this->post("flowID");
        $flowApprovalID = $this->post("flowApprovalID");
        $status = $this->post("status");
        $comment = $this->post("comment");
        $userID = $this->post("userID");

        $nextApproval = "";

        $log = array();


        /// change status approval
        $arr = array(
            "approvalStatusID"  => $status,
            "comment"           => $comment,
            "approveDate"      => date("Y-m-d H:i:s")
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
                $nextApproval = $approval;
            } else {
                $arr = array(
                    "flowStatusID"  => 2
                );
                $where = "flowID = " . $flowID;
                $this->flowmodel->update($arr, $where); // change status flow to success
            }

            $log = array(
                "flowID"    => $flowID,
                "approval"  => $userID,
                "action"    => "approve",
                "actionDate"    => date("Y-m-d H:i:s"),
                "comment"   => $comment
            );
            $this->flowlogmodel->create($log);
        } else if ($status == "4") { //status = reject
            $arr = array(
                "flowStatusID"  => 3
            );
            $where = "flowID = " . $flowID;
            $this->flowmodel->update($arr, $where); // change status flow to cancel

            $log = array(
                "flowID"    => $flowID,
                "approval"  => $userID,
                "action"    => "reject",
                "actionDate"    => date("Y-m-d H:i:s"),
                "comment"   => $comment
            );
            $this->flowlogmodel->create($log);
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
            $log = array(
                "flowID"    => $flowID,
                "approval"  => $userID,
                "action"    => "rework",
                "actionDate"    => date("Y-m-d H:i:s"),
                "comment"   => $comment
            );
            $this->flowlogmodel->create($log);
        }

        $result = array(
            "status"        => "success",
            "detail"        => "update status completed",
            "nextApproval"  => $nextApproval
        );
        $this->response($result, 200);
    }

    public function changestatus_put() // for fixed promplam
    {
        $flowID = $this->put("flowID");
        $flowApprovalID = $this->put("flowApprovalID");
        $status = $this->put("status");
        $comment = $this->put("comment");

        $nextApproval = "";


        /// change status approval

        $arr = array(
            "approvalStatusID"  => $status,
            "comment"           => $comment,
            "flowApprovalID = " => $flowApprovalID
        );
        $where = "flowApprovalID = " . $flowApprovalID;

        $this->approvalmodel->update($arr, $where);

        $result = array(
            "status"        => "success",
            "detail"        => "update status completed",
            "data"          => $arr
        );
        $this->response($result, 200);
    }
}
