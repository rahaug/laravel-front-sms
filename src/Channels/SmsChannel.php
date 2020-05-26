<?php

namespace RolfHaug\FrontSms\Channels;

use RolfHaug\FrontSms\Contracts\Smsable;
use RolfHaug\FrontSms\FrontClient;

class SmsChannel
{
    private $client;

    public function __construct(FrontClient $client = null)
    {
        $this->client = $client;
    }

    public function send($notifiable, Smsable $notification)
    {
        $message = $notification->toSms($notifiable);

        $this->client->push($message);

        // set origid from $this->client->getResponse
    }
}
