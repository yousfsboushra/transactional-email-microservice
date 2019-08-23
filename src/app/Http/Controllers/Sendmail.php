<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Sendmail extends Controller
{
    public function sendmail(){
        echo "welcome to send mail";
        $this->sendgrid();
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
}
