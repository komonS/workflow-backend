<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Bangkok");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Headers: origin, content-type, accept');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
require(APPPATH . 'libraries/RestController.php');
require(APPPATH . 'libraries/Format.php');

require(APPPATH . 'libraries/phpmailer/class.phpmailer.php');
require(APPPATH . 'libraries/phpmailer/class.pop3.php');
require(APPPATH . 'libraries/phpmailer/class.smtp.php');

use chriskacerguis\RestServer\RestController;

class Mail extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('email');
    }

    public function sendMail_post()
    {
        //$mailform = $this->post('mailform');
        $mailto = $this->post('mailto');
        $body = $this->post('body');
        $subject = $this->post('subject');
        $cc = $this->post('cc');

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";
        $mail->IsSMTP();
        $mail->Host = "smtp.office365.com";
        $mail->Port = 25;
        $mail->SMTPSecure = "tls";
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication
        $mail->Username = "capex.notification@siamchem.com";
        //Password to use for SMTP authentication
        $mail->Password = "Si@mch2564";
        $mail->SetFrom('capex.notification@siamchem.com', 'CapEx Notification');
        //$mail->AddReplyTo("Noreply@siamchem.com");

        $mail->IsHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body . '<br><br><br>';
        $mail->AddAddress($mailto);

        /* check cc and loop for send cc */

        if(count($cc) > 0){
            foreach ($cc as $row ) {
                $mail->AddCC($row['mail'],$row['name']);
            }
        }


        $re = $mail->Send();

        if (!$re) {
            $result = array(
                'status'    => 'error'
            );
        } else {
            $result = array(
                'status'    => 'success'
            );
        }

        $this->response($result, 200);
    }
}
