<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use RolfHaug\FrontSms\FrontInboundMessage;
use Tests\TestCase;

class InboundMessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_validates_payload()
    {
        $response = $this->postJson(route('sms.inbound.store'), []);
        $this->assertEquals(422, $response->status());
    }

    /** @test */
    public function it_can_store_inbound_message()
    {
        $request = $this->postJson(route('sms.inbound.store'), [
            'id' => '1234',
            'to' => '+4759595959',
            'from' => '+4799999999',
            'text' => 'Today was a good day!',
            'sent' => now()->toIso8601ZuluString(),
            'counter' => '1',
            'keyword' => 'TODAY',
            'files' => [],
        ]);

        $request->assertOk();
        $message = FrontInboundMessage::first();

        $this->assertNotNull($message, 'Inbound message was not saved');
        $this->assertEquals(1234, $message->id);
        $this->assertEquals('+4759595959', $message->to);
        $this->assertEquals('+4799999999', $message->from);
        $this->assertEquals('Today was a good day!', $message->message);
        $this->assertEquals(1, $message->counter);
        $this->assertEquals('TODAY', $message->keyword);
        $this->assertNull($message->files);
        $this->assertNotNull($message->sent_at);
    }
}