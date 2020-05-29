<?php

namespace RolfHaug\FrontSms\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Validator;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use RolfHaug\FrontSms\Channels\SmsChannel;
use RolfHaug\FrontSms\Contracts\Smsable;
use RolfHaug\FrontSms\Exceptions\Front\InvalidMessageArgument;
use RolfHaug\FrontSms\FrontMessage;
use RolfHaug\FrontSms\Traits\Smsable as SmsableTrait;

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
     * The eloquent model of the created message.
     *
     * @var FrontMessage
     */
    public $model;

    /**
     * Create a new message instance.
     *
     * @param string $message
     */
    public function __construct($message = null)
    {
        if (! $this->from) {
            $this->from = config('front-sms.fromId', null);
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
            $this->to($notifiable);
        }

        $userId = null;
        $user = config('auth.providers.users.model');

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

        $this->model = FrontMessage::create($data);

        return $this->model;
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
     * Set recipient of the message.
     *
     * @param $to Notifiable|SmsableTrait
     * @return $this
     * @throws NumberParseException
     */
    public function to($to)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        $notifiable = null;

        if (is_object($to)) {
            $key = config('front-sms.notifiablePhoneKey');
            $notifiable = $to;
            $to = $notifiable->$key;
        }

        // Validate recipients number
        try {
            // Try and see if phone number includes country code (e.g. +4790012345)
            $parser = $phoneUtil->parse($to);
            $formatted = $phoneUtil->format($parser, PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            // Check if we got the notifiable and it uses the SmsableTrait
            if ($notifiable && in_array(SmsableTrait::class, class_uses($notifiable))) {
                $formatted = $notifiable->getFormattedPhone();
            } else {
                // Fallback to config
                $phone = $phoneUtil->parse($to, config('front-sms.defaultRegion'));
                $formatted = $phoneUtil->format($phone, PhoneNumberFormat::E164);
            }
        }

        $this->to = $formatted;

        return $this;
    }
}
