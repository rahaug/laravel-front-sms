<?php

namespace DummyNamespace;

use RolfHaug\FrontSms\Notifications\SmsNotification;

class DummyClass extends SmsNotification
{
    public $message = 'Hi %s, today was a good day!';

    public function getMessage($notifiable)
    {
        return vsprintf($this->message, [
            $notifiable->name,
        ]);
    }
}
