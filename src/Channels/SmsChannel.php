<?php

namespace RolfHaug\FrontSms\Channels;

use RolfHaug\FrontSms\Contracts\Smsable;
use RolfHaug\FrontSms\FrontClient;
use RolfHaug\FrontSms\FrontMessage;

class SmsChannel
{
    private $client;

    /**
     * Eloquent model of the message.
     * @var
     */
    private $message;

    public function __construct(FrontClient $client = null)
    {
        $this->client = $client;
    }

    public function send($notifiable, Smsable $notification)
    {
        $this->message = $notification->toSms($notifiable);

        $this->client->push($this->message);
    }

    /**
     * Return the message model.
     *
     * @return null|FrontMessage
     */
    public function getMessageModel()
    {
        return $this->message;
    }
}
