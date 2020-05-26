<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Notification;
use RolfHaug\FrontSms\Notifications\SmsNotification;
use Tests\Messages\DynamicMessage;
use Tests\TestCase;

class SmsNotificationTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        // Set SMS Sender name
        $this->app->config->set('front-sms.fromId', 'Testsender');
    }

    /** @test */
    public function it_can_send_sms_through_user()
    {
        Notification::fake();
        $user = $this->createUser();

        $user->notify(new SmsNotification('This is a test message'));

        Notification::assertSentTo($user, SmsNotification::class, function (SmsNotification $notification) use ($user) {
            $sms = $notification->toSms($user);
            return $sms->message === 'This is a test message'
                && $sms->user_id === $user->id
                && $sms->to === $user->phone;
        });
    }

    /** @test */
    public function it_sets_default_settings()
    {
        $this->app->config->set(
            'front-sms.fromId',
            'My Sender'
        );

        $user = $this->createUser();
        $notification = new SmsNotification('This is a test message');

        $sms = $notification->toSms($user);

        $this->assertEquals('My Sender', $sms->from);
        $this->assertEquals($user->phone, $sms->to);
        $this->assertEquals('This is a test message', $sms->message);
        $this->assertEquals(0, $sms->price);
        $this->assertEquals($user->id, $sms->user_id);
    }

    /** @test */
    public function it_can_set_message_through_method()
    {
        $user = $this->createUser();

        $notification = (new SmsNotification)->message('Custom message');
        $sms = $notification->toSms($user);

        $this->assertEquals('Custom message', $sms->message);
    }

    /** @test */
    public function it_can_set_from_field_through_message()
    {
        $user = $this->createUser();

        $notification = (new SmsNotification('Test message'))->from('Batman');
        $sms = $notification->toSms($user);

        $this->assertEquals('Batman', $sms->from);
    }

    /** @test */
    public function it_automatically_sets_to_field_when_given_user()
    {
        $user = $this->createUser();

        $notification = new SmsNotification('Test message');
        $sms = $notification->toSms($user);

        $this->assertEquals($user->phone, $sms->to);
    }

    /** @test */
    public function it_can_set_to_field_through_method()
    {
        $user = $this->createUser();

        $notification = (new SmsNotification('Test message'))->to('99887766');
        $sms = $notification->toSms($user);

        $this->assertEquals('99887766', $sms->to);
    }

    /** @test */
    public function it_can_set_price_through_method()
    {
        $user = $this->createUser();

        $notification = (new SmsNotification('Premium CPA message'))->price(1000);
        $sms = $notification->toSms($user);

        $this->assertEquals(1000, $sms->price);
    }

    /** @test */
    public function it_compiles_messages_before_sending()
    {
        $user = $this->createUser(['name' => 'Bruce Wayne']);

        $notification = (new DynamicMessage)->message('Welcome %s!');
        $sms = $notification->toSms($user);

        $this->assertEquals('Welcome Bruce Wayne!', $sms->message);
    }
}
