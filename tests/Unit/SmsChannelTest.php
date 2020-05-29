<?php

namespace Tests\Unit;

use Mockery as m;
use RolfHaug\FrontSms\Channels\SmsChannel;
use RolfHaug\FrontSms\FrontClient;
use RolfHaug\FrontSms\FrontMessage;
use RolfHaug\FrontSms\Notifications\SmsNotification;
use Tests\TestCase;

class SmsChannelTest extends TestCase
{
    /** @test */
    public function it_sends_sms_and_sets_message_model()
    {
        $notification = new SmsNotification('Test message');
        $notifiable = $this->createUser();

        $channel = new SmsChannel(
            $client = m::mock(FrontClient::class)
        );

        $client->shouldReceive('push')
            ->once();

        $channel->send($notifiable, $notification);

        $this->assertInstanceOf(FrontMessage::class, $channel->getMessageModel());
    }

    /** @test */
    public function it_sets_get_message_model_as_null_by_default()
    {
        $channel = new SmsChannel();
        $this->assertNull($channel->getMessageModel());
    }
}
