<?php

namespace Tests\Unit;

use Illuminate\Support\Collection;
use RolfHaug\FrontSms\FrontMessage;
use RolfHaug\FrontSms\FrontMessageStatus;
use Tests\TestCase;

class FrontMessageTest extends TestCase
{
    /** @test */
    public function it_has_statuses_relationship()
    {
        $origId = 123;
        $sms = factory(FrontMessage::class)->create(['origid' => $origId]);

        factory(FrontMessageStatus::class)->create(['origid' => $origId]);

        $sms = $sms->fresh();

        $this->assertNotNull($sms->statuses);

        $this->assertInstanceOf(Collection::class, $sms->statuses);
        $this->assertCount(1, $sms->statuses);
    }

    /** @test */
    public function it_has_is_delivered_helper_method()
    {
        $sms = factory(FrontMessage::class)->create();

        $this->assertFalse($sms->isDelivered());

        $sms->update([
            'delivered_at' => now()->subDays(2)
        ]);

        $this->assertTrue($sms->fresh()->isDelivered());
    }

    /** @test */
    public function it_has_an_is_unicode_helper_method()
    {
        $notUnicodeSms = factory(FrontMessage::class)->create(['message' => 'Not unicode']);

        $this->assertFalse($notUnicodeSms->isUnicode());

        $unicodeSms = factory(FrontMessage::class)->create(['message' => 'Unicode message 👌']);

        $this->assertTrue($unicodeSms->isUnicode());
    }

    /** @test */
    public function it_marks_lesson_as_sent_and_sets_orig_id()
    {
        $sms = factory(FrontMessage::class)->create();
        $this->assertNull($sms->sent_at);

        $sms->markAsSent(1234);

        $sms = $sms->fresh();
        $this->assertNotNull($sms->sent_at);
        $this->assertEquals(1234, $sms->origid);
    }
}
