<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Sendmail;

class SendCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mail {--f=} {--r=*} {--c=} {--s=} {--m=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Mail';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $inputs = $this->collectInputs();

        $mailer = new Sendmail();
        $res = $mailer->cliEntry($inputs['recipients'], $inputs['subject'], $inputs['message'], $inputs['from'], $inputs['contentType']);
        if(isset($res['status']) && $res['status'] === "500"){
            $this->error($res['response']['error']);
        }else{
            $this->info($this->error($res['response']['message']));
        }
    }

    private function collectInputs(){
        $inputs = array();
        $arguments = $this->arguments();
        $from = $this->option('f');
        if(empty($from)){
            $from = $this->ask('What is the from email address?');
        }
        $inputs['from'] = $from;

        $recipients = $this->option('r');
        if(empty($recipients)){
            $recipientsString = $this->ask('What are the recipients email addresses(comma separated)?');
            if(!empty($recipientsString)){
                $recipients = explode(",", $recipientsString);
            }
        }
        $inputs['recipients'] = $recipients;

        $contentType = $this->option('c');
        if(empty($contentType)){
            $contentType = $this->choice('What is the message type?', ['Text', 'HTML'], 0);
        }
        $inputs['contentType'] = $contentType;

        $subject = $this->option('s');
        if(empty($subject)){
            $subject = $this->ask('What is the subject?');
        }
        $inputs['subject'] = $subject;

        $message = $this->option('m');
        if(empty($message)){
            $message = $this->ask('What is the message?');
        }
        $inputs['message'] = $message;

        return $inputs;
    }
}
