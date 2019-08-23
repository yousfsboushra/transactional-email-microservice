<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mailjet\Resources;

class Sendmail extends Controller
{
    public function sendmail(){
        $to = "yousefsboushra@gmail.com";
        $from = "yousefsboushra@gmail.com";
        $subject = "Time for Takeaway.com";
        $contentType = "html";
        $message = '<a href="https://www.takeaway.com">Takeaway.com</a> is a leading online food delivery marketplace, focused on connecting consumers and restaurants through its platform in 10 European countries and Israel. <a href="https://www.takeaway.com">Takeaway.com</a> offers an online marketplace where supply and demand for food delivery and ordering meet.';

        $mailservices = array(
            'sendgrid',
            'mailjet'
        );
        foreach($mailservices as $mailservice){
            if($this->{$mailservice}($to, $subject, $message, $from, $contentType)){
                return "Mail was sent successfully by ${mailservice}";
            }
        }
        return "Mail couldn't be sent by any mail service";
    }
    public function sendgrid($to, $subject, $message, $from, $contentType){
        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom($from);
        $email->setSubject($subject);
        $email->addTo($to);
        if($contentType === "html"){
            $email->addContent("text/html", $message);            
        }else{
            $email->addContent("text/plain", strip_tags($message));        
        }
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            return true;
        } catch (Exception $e) {
            error_log('SENDGRID: '. $e->getMessage());
        }
        return false;
    }

    public function mailjet($to, $subject, $message, $from, $contentType){
        $mj = new \Mailjet\Client(getenv('MAILJET_USERNAME'),getenv('MAILJET_PASSWORD'),true,['version' => 'v3.1']);
        $body = array(
            'Messages' => array(
                array(
                    'From' => array(
                        'Email' => $from
                    ),
                    'To' => array(
                        array(
                            'Email' => $to
                        )
                    ),
                    'Subject' => $subject
                )
            )
        );
        if($contentType === "html"){
            $body['Messages'][0]['HTMLPart'] = $message;
        }else{
            $body['Messages'][0]['TextPart'] = strip_tags($message);
        }
        
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
        $result = $response->getData();

        if(isset($result['Messages'][0]['Status'])){
            if($result['Messages'][0]['Status'] === "success"){
                return true;
            }else if(isset($result['Messages'][0]['Errors'][0]['ErrorMessage'])){
                error_log('MAILJET: '. $result['Messages'][0]['Errors'][0]['ErrorMessage']);
                return false;
            }
        }
        return false;
    }
}
