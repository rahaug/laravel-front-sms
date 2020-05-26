<?php

namespace RolfHaug\FrontSms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed status
 */
class FrontMessageStatus extends Model
{
    protected $guarded = [];
    const RECEIVED_BY_OPERATOR = 0; // Not delivered yet
    const RECEIVED_BY_RECIPIENT = 4; // Successfully Delivered
    const DELIVERY_FAILED = 5; // Not delivered

    /**
     * Related SMS Message
     * .
     * @return FrontMessage
     */
    public function sms()
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
