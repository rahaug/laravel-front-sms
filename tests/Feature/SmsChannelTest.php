<?php

namespace Tests\Feature;

use Mockery as m;
use RolfHaug\FrontSms\Channels\SmsChannel;
use RolfHaug\FrontSms\FrontClient;
use RolfHaug\FrontSms\Notifications\SmsNotification;
use Tests\TestCase;

class SmsChannelTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        // Set SMS Sender name
        $this->app->config->set('front-sms.fromId', 'Testsender');
    }

    /** @test */
    public function it_sends_sms()
    {
        // Mock client
        // Ref: https://github.com/laravel/nexmo-notification-channel/blob/2.0/tests/Channels/NexmoSmsChannelTest.php

        $notification = new SmsNotification('Test message');
        $notifiable = $this->createUser();

        $channel = new SmsChannel(
            $client = m::mock(FrontClient::class)
        );

        $client->shouldReceive('push')
            ->once();

        // Make sure origid is set to the message.

        $channel->send($notifiable, $notification);
    }
}
