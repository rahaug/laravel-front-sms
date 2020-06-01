<?php

namespace Tests\Unit;

use RolfHaug\FrontSms\DeliveryStatus;
use RolfHaug\FrontSms\FrontMessage;
use Tests\TestCase;

class DeliveryStatusTest extends TestCase
{
    /** @test */
    public function it_has_received_by_operator_status()
    {
        $this->assertEquals(0, DeliveryStatus::RECEIVED_BY_OPERATOR);
    }

    /** @test */
    public function it_has_received_by_receipient_status()
    {
        $this->assertEquals(4, DeliveryStatus::RECEIVED_BY_RECIPIENT);
    }

    /** @test */
    public function it_has_delivery_failed_status()
    {
        $this->assertEquals(5, DeliveryStatus::DELIVERY_FAILED);
    }

    /** @test */
    public function it_has_sms_relationship()
    {
        $status = factory(DeliveryStatus::class)->create();

        $sms = factory(FrontMessage::class)->create(['origid' => $status->origid]);

        $this->assertNotNull($status->message);
        $this->assertInstanceOf(FrontMessage::class, $status->message);
        $this->assertEquals($sms->id, $status->message->id);
    }

    /** @test */
    public function it_has_is_delivered_helper_method()
    {
        $status = factory(DeliveryStatus::class)->create([
            'status' => DeliveryStatus::RECEIVED_BY_RECIPIENT
        ]);

        $this->assertTrue($status->isDelivered());
    }

    /** @test */
    public function it_has_is_received_by_operator_helper_method()
    {
        $status = factory(DeliveryStatus::class)->create([
            'status' => DeliveryStatus::RECEIVED_BY_OPERATOR
        ]);

        $this->assertTrue($status->isReceivedByOperator());
    }

    /** @test */
    public function it_has_is_failed_helper_method()
    {
        $status = factory(DeliveryStatus::class)->create([
            'status' => DeliveryStatus::DELIVERY_FAILED
        ]);

        $this->assertTrue($status->isFailed());
    }
}
