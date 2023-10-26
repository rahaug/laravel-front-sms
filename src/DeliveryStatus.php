<?php

namespace RolfHaug\FrontSms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RolfHaug\FrontSms\Database\Factories\DeliveryStatusFactory;

/**
 * @property mixed status
 */
class DeliveryStatus extends Model
{
    use HasFactory;

    protected $guarded = [];

    const RECEIVED_BY_OPERATOR = 0; // Not delivered yet

    const RECEIVED_BY_RECIPIENT = 4; // Successfully Delivered

    const DELIVERY_FAILED = 5; // Not delivered

    protected static function newFactory()
    {
        return DeliveryStatusFactory::new();
    }

    /**
     * Related SMS Message
     * .
     * @return FrontMessage
     */
    public function message()
    {
        return $this->belongsTo(FrontMessage::class, 'origid', 'origid');
    }

    /**
     * Helper method to determine if message was successfully delivered to the recipient.
     *
     * @return bool
     */
    public function isDelivered()
    {
        return $this->status === self::RECEIVED_BY_RECIPIENT;
    }

    /**
     * Helper method to determine if message was received by the network operator.
     *
     * @return bool
     */
    public function isReceivedByOperator()
    {
        return $this->status === self::RECEIVED_BY_OPERATOR;
    }

    /**
     * Helper method to determine if message was not delivered to the recipient.
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === self::DELIVERY_FAILED;
    }
}
