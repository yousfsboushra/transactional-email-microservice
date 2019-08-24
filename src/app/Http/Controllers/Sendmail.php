<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mailjet\Resources;
use App\Models\Email;

class Sendmail extends Controller
{
    // JSON API entry function
    public function apiEntry(Request $request){
        $recipients = (!empty($request->recipients))? $request->recipients : array();
        $from = (!empty($request->from))? $request->from : "";
        $subject = (!empty($request->subject))? $request->subject : "";
        $contentType = (!empty($request->contentType))? $request->contentType : "";
        $message = (!empty($request->message))? $request->message : "";

        $this->addEmail($recipients, $subject, $message, $from, $contentType);
    }

    // CLI entry function
    public function cliEntry($recipients, $subject, $message, $from, $contentType){
        $this->addEmail($recipients, $subject, $message, $from, $contentType);
    }

    // Add email to the database
    private function addEmail($recipients, $subject, $message, $from, $contentType){
        $email = new Email();
        $email->recipients = implode(",", $recipients);
        $email->from = $from;
        $email->subject = $subject;
        $email->content_type = $contentType;
        $email->message = $message;
        return $email->save();
    }

    // public function executeSendmail($recipients, $subject, $message, $from, $contentType){
    //     $mailservices = array(
    //         'sendgrid',
    //         'mailjet'
    //     );
    //     foreach($mailservices as $mailservice){
    //         if($this->{$mailservice}($recipients, $subject, $message, $from, $contentType)){
    //             return array('response' => array("message" => "Mails were sent successfully by ${mailservice}"), 'status' => '200');
    //         }
    //     }
    //     return array('response' => array("error" => "Mails couldn't be sent by any mail service"), 'status' => '500');
    // }
    
    // Send mail via sendgrid
    private function sendgrid($recipients, $subject, $message, $from, $contentType){
        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom($from);
        $email->setSubject($subject);
        foreach($recipients as $recipient){
            $email->addTo($recipient);
        }
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

    // Send mail via mailjet
    private function mailjet($recipients, $subject, $message, $from, $contentType){
        $mj = new \Mailjet\Client(getenv('MAILJET_USERNAME'),getenv('MAILJET_PASSWORD'),true,['version' => 'v3.1']);
        $body = array(
            'Messages' => array(
                array(
                    'From' => array(
                        'Email' => $from
                    ),
                    'Subject' => $subject
                )
            )
        );
        foreach($recipients as $recipient){
            $body['Messages'][0]['To'][] = array(
                'Email' => $recipient
            );
        }
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
