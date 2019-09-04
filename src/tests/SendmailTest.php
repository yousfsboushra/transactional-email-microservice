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
}
