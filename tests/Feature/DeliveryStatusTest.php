<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use RolfHaug\FrontSms\DeliveryStatus;
use RolfHaug\FrontSms\FrontMessage;
use Tests\TestCase;

class DeliveryStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_store_status_message()
    {
        $message = factory(FrontMessage::class)->create(['origid' => 1234]);

        $request = $this->post(route('sms.status.store'), [
            'origid' => $message->origid,
            'status' => -1
        ]);
        $request->assertOk();
        $status = DeliveryStatus::first();

        $this->assertNotNull($status, 'Status not created');
        $this->assertEquals($status->origid, 1234);
        $this->assertEquals($status->status, -1);
    }

    /** @test */
    public function it_does_not_store_status_message_if_origid_does_not_exists()
    {
        $request = $this->post(route('sms.status.store'), [
            'origid' => 111,
            'status' => -1
        ]);

        $request->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_marks_message_as_delivered()
    {
        $message = factory(FrontMessage::class)->create(['origid' => 1234]);

        $this->post(route('sms.status.store'), [
            'origid' => $message->origid,
            'status' => DeliveryStatus::RECEIVED_BY_RECIPIENT
        ]);

        $this->assertTrue($message->fresh()->isDelivered());
    }

    /** @test */
    public function it_marks_message_as_failed()
    {
        $message = factory(FrontMessage::class)->create(['origid' => 1234]);

        $this->post(route('sms.status.store'), [
            'origid' => $message->origid,
            'status' => DeliveryStatus::DELIVERY_FAILED
        ]);

        $this->assertTrue($message->fresh()->isFailed());
    }
}
