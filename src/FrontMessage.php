<?php

namespace RolfHaug\FrontSms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RolfHaug\FrontSms\Database\Factories\FrontMessageFactory;

class FrontMessage extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'received_by_operator' => 'boolean',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime'
    ];


    protected static function newFactory()
    {
        return FrontMessageFactory::new();
    }

    /**
     * Get related message statuses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statuses()
    {
        return $this->hasMany(DeliveryStatus::class, 'origid', 'origid');
    }

    /**
     * Checks if the message is confirmed delivered or not.
     *
     * @return bool
     */
    public function isDelivered()
    {
        return $this->delivered_at && $this->delivered_at->isPast();
    }

    /**
     * Checks if the message delivery failed. Returns true if delivery failure is confirmed.
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->failed_at && $this->failed_at->isPast();
    }

    /**
     * Checks if the message property is unicode or not.
     *
     * @return bool
     */
    public function isUnicode()
    {
        return mb_detect_encoding($this->message, ['ASCII', 'UTF-8']) === 'UTF-8';
    }

    /**
     * Is the message received by the operator.
     *
     * @return bool
     */
    public function isReceivedByOperator()
    {
        return (bool) $this->received_by_operator;
    }

    /**
     * Mark message as sent.
     *
     * @param $origId
     * @return $this
     */
    public function markAsSent($origId)
    {
        $this->update([
            'origid' => $origId,
            'sent_at' => now()
        ]);

        return $this;
    }

    /**
     * Mark message as delivered to recipient.
     *
     * @return $this
     */
    public function markAsDelivered()
    {
        $this->update(['delivered_at' => now()]);

        return $this;
    }

    /**
     * Mark message as failed to deliver to recipient.
     *
     * @return $this
     */
    public function markAsFailed()
    {
        $this->update(['failed_at' => now()]);

        return $this;
    }

    /**
     * Mark message as received by operator.
     *
     * @return $this
     */
    public function markAsReceivedByOperator()
    {
        $this->update(['received_by_operator' => true]);
        return $this;
    }
}
