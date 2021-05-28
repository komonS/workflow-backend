<?php
defined('BASEPATH') or exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true"); 
header('Access-Control-Allow-Headers: origin, content-type, accept');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
require(APPPATH . 'libraries/RestController.php');
require(APPPATH . 'libraries/Format.php');

use chriskacerguis\RestServer\RestController;

class Status extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('statusmodel');
    }


    public function status_get()
    {
        $result = $this->statusmodel->getStatus();
        $this->response($result, 200);
    }
}
