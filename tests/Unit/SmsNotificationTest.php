<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Notification;
use libphonenumber\NumberParseException;
use RolfHaug\FrontSms\FrontMessage;
use RolfHaug\FrontSms\Notifications\SmsNotification;
use Tests\Article;
use Tests\Messages\DynamicMessage;
use Tests\Messages\MultiDependencyMessage;
use Tests\TestCase;

class SmsNotificationTest extends TestCase
{
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
                && $sms->to === $user->getFormattedPhone();
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
        $this->assertEquals($user->getFormattedPhone(), $sms->to);
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

        $this->assertEquals($user->getFormattedPhone(), $sms->to);
    }

    /** @test */
    public function it_can_set_to_field_through_method()
    {
        $user = $this->createUser();

        $notification = (new SmsNotification('Test message'))->to('+4799887766');
        $sms = $notification->toSms($user);

        $this->assertEquals('+4799887766', $sms->to);
    }

    /** @test */
    public function it_throws_if_setting_to_field_through_method_and_it_does_not_have_country_code()
    {
        $this->expectException(NumberParseException::class);
        (new SmsNotification('Test message'))->to('99887766');
    }

    /** @test */
    public function it_gets_country_code_from_user_when_using_smsable_trait()
    {
        $user = $this->createUser(['country_code' => 'NO', 'phone' => 90012345]);

        $notification = (new SmsNotification('Test message'));
        $sms = $notification->toSms($user);

        $this->assertEquals('+4790012345', $sms->to);
    }

    /** @test */
    public function it_gets_country_code_from_config_when_no_user_or_country_code_is_given()
    {
        $this->app['config']->set('front-sms.defaultRegion', 'NO');
        $notification = (new SmsNotification('Test message'))->to('90012345');

        $this->assertEquals('+4790012345', $notification->to);
    }

    /** @test */
    public function it_sets_to_field_from_user_through_method()
    {
        $user = $this->createUser();

        $notification = (new SmsNotification('Test message'))->to($user);
        $sms = $notification->toSms($user);

        $this->assertEquals($user->getFormattedPhone(), $sms->to);
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

    /** @test */
    public function it_stores_eloquent_model_of_message_as_property()
    {
        $user = $this->createUser(['name' => 'Bruce Wayne']);

        $notification = (new SmsNotification('Test message'));
        $sms = $notification->toSms($user);
        $this->assertInstanceOf(FrontMessage::class, $sms);
    }

    /** @test */
    public function it_can_send_messages_with_multiple_dependencies()
    {
        $user = $this->createUser();
        $article = (new Article)->fill(['title' => 'My article title', 'link' => 'https://website.com/blogg/my-article-title']);

        $notification = (new MultiDependencyMessage(null, $article));
        $sms = $notification->toSms($user);

        $this->assertEquals(
            "Hi {$user->name}, you have a new comment on your \"{$article->title}\" article. Read it here: {$article->link}",
            $sms->message
        );
    }
}
