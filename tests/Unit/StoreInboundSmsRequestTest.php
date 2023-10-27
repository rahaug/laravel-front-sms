<?php

namespace Tests\Unit;

use RolfHaug\FrontSms\Http\Requests\StoreInboundSms;
use Tests\TestCase;

class StoreInboundSmsRequestTest extends TestCase
{
    /** @test */
    public function it_has_correct_rules()
    {
        $requestRules = (new StoreInboundSms())->rules();

        $rules = [
            'id' => 'required',
            'to' => 'required',
            'from' => 'required',
            'text' => 'required',
            'sent' => 'required',
            'counter' => 'required',
            'keyword' => 'required',
            'files' => 'sometimes|array'
        ];

        foreach($rules as $key => $value) {
            $this->assertArrayHasKey($key, $requestRules);
            $this->assertEquals($value, $requestRules[$key]);
        }
    }
}