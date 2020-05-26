<?php

namespace RolfHaug\FrontSms;

use Illuminate\Database\Eloquent\Model;

class FrontMessage extends Model
{
    protected $guarded = [];

    protected $dates = ['sent_at', 'delivered_at'];

    /**
     * Get related message statuses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statuses()
    {
        return $this->hasMany(FrontMessageStatus::class, 'origid', 'origid');
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
     * Checks if the message property is unicode or not.
     *
     * @return bool
     */
    public function isUnicode()
    {
        return strlen($this->message) !== strlen(utf8_decode($this->message));
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
}
