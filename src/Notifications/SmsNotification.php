<?php

namespace RolfHaug\FrontSms\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Validator;
use RolfHaug\FrontSms\Channels\SmsChannel;
use RolfHaug\FrontSms\Contracts\Smsable;
use RolfHaug\FrontSms\Exceptions\Front\InvalidMessageArgument;
use RolfHaug\FrontSms\FrontMessage;

class SmsNotification extends Notification implements ShouldQueue, Smsable
{
    use Queueable;

    /**
     * The phone number or sender name the message should be sent from.
     * @var string
     */
    public $from;

    /**
     * The recipient of the message.
     * @var
     */
    public $to;

    /**
     * The message content.
     * @var string
     */
    public $message;

    /**
     * The cost of receiving the message in Norwegian cents.
     *
     * CPA Feature must be enabled by front.
     *
     * @var int
     */
    public $price = 0;

    /**
     * Create a new message instance.
     *
     * @param string $message
     */
    public function __construct($message = null)
    {
        if (! $this->from) {
            $this->from = config('front-sms.fromId');
        }

        if (! $this->message && $message) {
            $this->message = $message;
        }
    }

    public function via($notifiable)
    {
        return [SmsChannel::class];
    }

    public function getMessage($notifiable)
    {
        return $this->message;
    }

    /**
     * Save the entire notification in the database.
     *
     * @param $notifiable
     * @return FrontMessage
     */
    public function toSms($notifiable)
    {
        if (! $this->to) {
            $key = config('front-sms.notifiable_phone_key');
            $this->to($notifiable->$key);
        }

        $userId = null;
        $user = config('auth.model');

        if ($notifiable instanceof $user) {
            $userId = $notifiable->getAuthIdentifier();
        }

        $data = [
            'user_id' => $userId,
            'to' => $this->to,
            'from' => $this->from,
            'message' => $this->getMessage($notifiable),
            'price' => $this->price
        ];

        $validator = Validator::make($data, [
            'to' => 'required|numeric',
            'from' => 'required',
            'message' => 'required',
            'price' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            throw new InvalidMessageArgument($validator->errors());
        }

        return FrontMessage::create($data);
    }

    /**
     * Set the phone number or sender name the message should be sent from.
     *
     * @param $from
     * @return $this
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Set the message content.
     * @param $message
     * @param array $data
     * @return $this
     */
    public function message($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set message price (in Norwegian cents) for CPA messages.
     *
     * Must be enabled by Front first.
     *
     * @param int $price
     * @return $this
     */
    public function price(int $price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Set receipient of the message.
     *
     * @param $to
     * @return $this
     */
    public function to($to)
    {
        $this->to = $to;

        return $this;
    }
}
