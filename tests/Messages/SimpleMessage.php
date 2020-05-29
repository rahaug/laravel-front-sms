<?php

namespace Tests\Messages;

use RolfHaug\FrontSms\Notifications\SmsNotification;

class SimpleMessage extends SmsNotification
{
    public function __construct($message = null)
    {
        $this->message = $message ?? 'This is a SMS message';
        $this->price = 0;
    }
}
