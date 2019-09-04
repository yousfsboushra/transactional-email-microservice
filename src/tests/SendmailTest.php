<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class SendmailTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    
    public function testApiGetMethod()
    {
        $response = $this->call('GET', '/sendmail');
        $this->assertEquals(405, $response->status());
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testApiWithoutInputs()
    {
        $this->post('/sendmail', [])
        ->seeJson([
            "recipients" => ["The recipients field is required."],
            "from" => ["The from field is required."],
            "subject" => ["The subject field is required."],
            "contentType" => ["The content type field is required."],
            "message" => ["The message field is required."]
        ]);
    }

    public function testApiWithRecipientsOnly()
    {
        $this->post('/sendmail', [
            "recipients" => [
                "yousefsboushra@gmail.com"
            ]
        ])
        ->seeJsonEquals([
            "from" => ["The from field is required."],
            "subject" => ["The subject field is required."],
            "contentType" => ["The content type field is required."],
            "message" => ["The message field is required."]
        ]);
    }

    public function testApiWithRecipientsAndFromOnly()
    {
        $this->post('/sendmail', [
            "recipients" => [
                "yousefsboushra@gmail.com"
            ],
            "from" => "yousefsboushra@gmail.com",
        ])
        ->seeJsonEquals([
            "subject" => ["The subject field is required."],
            "contentType" => ["The content type field is required."],
            "message" => ["The message field is required."]
        ]);
    }

    public function testApiWithRecipientsFromAndSubjectOnly()
    {
        $this->post('/sendmail', [
            "recipients" => [
                "yousefsboushra@gmail.com"
            ],
            "from" => "yousefsboushra@gmail.com",
            "subject" => "Time for Takeaway.com",
        ])
        ->seeJsonEquals([
            "contentType" => ["The content type field is required."],
            "message" => ["The message field is required."]
        ]);
    }

    public function testApiWithRecipientsFromSubjectAndTypeOnly()
    {
        $this->post('/sendmail', [
            "recipients" => [
                "yousefsboushra@gmail.com"
            ],
            "from" => "yousefsboushra@gmail.com",
            "subject" => "Time for Takeaway.com",
            "contentType" => "html",
        ])
        ->seeJsonEquals([
            "message" => ["The message field is required."]
        ]);
    }

    public function testApiWithAllFields()
    {
        $this->post('/sendmail', [
            "recipients" => [
                "yousefsboushra@gmail.com"
            ],
            "from" => "yousefsboushra@gmail.com",
            "subject" => "Time for Takeaway.com",
            "contentType" => "html",
            "message" => "<a href=\"https://www.takeaway.com\">Takeaway.com</a> is a leading online food delivery marketplace, focused on connecting consumers and restaurants through its platform in 10 European countries and Israel. <a href=\"https://www.takeaway.com\">Takeaway.com</a> offers an online marketplace where supply and demand for food delivery and ordering meet."
        ])
        ->seeJsonEquals([
            "response" => [
                "message" => "Mail was added to the queue",
                "status" => "success"
            ]
        ]);
    }

}
