<?php

namespace Tests\Messages;

use RolfHaug\FrontSms\Notifications\SmsNotification;

class DynamicMessage extends SmsNotification
{
    public $message = "Hi %s, welcome to us! We've sent you an email to %s. Remember to check your spam!";

    public function getMessage($notifiable)
    {
        return vsprintf($this->message, [
            $notifiable->name,
            $notifiable->email
        ]);
    }
}
