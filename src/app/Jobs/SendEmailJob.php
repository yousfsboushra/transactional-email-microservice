<?php

namespace App\Jobs;
use App\Models\Email;
use App\Http\Controllers\Sendmail;

class SendEmailJob extends Job
{
    private $emailId;
    private $recipients;
    private $subject;
    private $message;
    private $from;
    private $contentType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emailId, $recipients, $subject, $message, $from, $contentType)
    {
        $this->emailId = $emailId;
        $this->recipients = $recipients;
        $this->subject = $subject;
        $this->message = $message;
        $this->from = $from;
        $this->contentType = $contentType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = Email::find($this->emailId);
        $mailer = new Sendmail();
        $res = $mailer->executeSendmail($this->recipients, $this->subject, $this->message, $this->from, $this->contentType);
        if($res['status'] == "error"){
            $email->status = "failed";
        }else{
            $email->status = "sent";
            $email->sent_by = $res['service'];
        }
        $email->save();
        echo $res['response']['message'];
    }
}
