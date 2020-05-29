<?php

namespace Tests\Unit;

use RolfHaug\FrontSms\Notifications\SmsNotification;
use Tests\TestCase;
use Tests\User;

class DummyMessageTest extends TestCase
{
    /** @test */
    public function it_extends_sms_notification_class()
    {
        $stub = $this->getStubClass();
        $this->assertTrue(is_subclass_of($stub, SmsNotification::class));
    }

    /** @test */
    public function it_has_a_default_message_with_an_argument()
    {
        $stub = $this->getStubClass();
        $this->assertObjectHasAttribute('message', $stub);
        $this->assertStringContainsString('%s', $stub->message);
    }

    /** @test */
    public function it_has_a_default_get_message_method_that_compiles_the_dynamic_argument()
    {
        $stub = $this->getStubClass();

        $notifiable = (new User)->fill(['name' => 'Rolf']);
        $message = $stub->getMessage($notifiable);

        $this->assertStringContainsString('Rolf', $message);
    }

    protected function getStubClass()
    {
        require_once __DIR__.'/../../stubs/DummyClass.php';

        return new \DummyNamespace\DummyClass;
    }
}
