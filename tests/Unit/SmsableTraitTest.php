<?php

namespace Tests\Unit;

use RolfHaug\FrontSms\Traits\Smsable;
use Tests\TestCase;
use Tests\User;

class SmsableTraitTest extends TestCase
{
    /** @test */
    public function it_ensures_user_is_using_trait()
    {
        $user = new User;
        $this->assertContains(Smsable::class, class_uses($user));
    }

    /** @test */
    public function it_has_a_get_country_code_method_that_returns_null()
    {
        $user = new User;
        $this->assertNull($user->getCountryCode());
    }

    /** @test */
    public function it_returns_country_code_if_column_or_accessor_exists()
    {
        $user = new User;
        $user->country_code = 'NO';
        $this->assertEquals('NO', $user->getCountryCode());
    }

    /** @test */
    public function it_returns_country_code_from_config_if_not_set_on_user()
    {
        $this->app['config']->set('front-sms.defaultRegion', 'NO');
        $user = new User;
        $this->assertNull($user->country_code);
        $this->assertEquals('NO', $user->getCountryCode());
    }

    /** @test */
    public function it_has_a_get_formatted_phone_method()
    {
        $user = new User;
        $user->phone = 90012345;
        $user->country_code = 'NO';

        $this->assertEquals('+4790012345', $user->getFormattedPhone());
    }
}
