<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mailjet\Resources;

class Sendmail extends Controller
{
    public function sendmail(){
        echo "welcome to send mail";
        // $this->sendgrid();
        $this->mailjet();
    }
    public function sendgrid(){
        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("test@example.com", "Example User");
        $email->setSubject("Sending with SendGrid is Fun");
        $email->addTo("yousefsboushra@gmail.com", "Yousef");
        $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
        $email->addContent(
            "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }

    public function mailjet(){
        $mj = new \Mailjet\Client('db239066c296aee297f7e64a14465c24','ad89564bd124fc9c448e99a1ecf42f56',true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
            [
                'From' => [
                'Email' => "yousefsboushra@gmail.com",
                'Name' => "Yousef"
                ],
                'To' => [
                [
                    'Email' => "yousefsboushra@gmail.com",
                    'Name' => "Yousef"
                ]
                ],
                'Subject' => "Greetings from Mailjet.",
                'TextPart' => "My first Mailjet email",
                'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
                'CustomID' => "AppGettingStartedTest"
            ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && var_dump($response->getData());
    }
}
