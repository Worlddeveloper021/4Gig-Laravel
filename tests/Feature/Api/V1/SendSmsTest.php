<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use Aloha\Twilio\LoggingDecorator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendSmsTest extends TestCase
{
    use RefreshDatabase;

    /* @test */
    // public function it_can_send_sms()
    // {
    //     $this->withoutExceptionHandling();

    //     $psrLogger = app()->make(\Psr\Log\LoggerInterface::class);
    //     $twilio = new LoggingDecorator($psrLogger, app()->make(\Aloha\Twilio\Manager::class));

    //     // dd($twilio->message('+18594146471', 'Hello World !!!!')); // we should send sms to a verified phone number in trial mode
    // }
}
