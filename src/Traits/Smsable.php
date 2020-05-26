<?php

namespace RolfHaug\FrontSms\Traits;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

trait Smsable
{
    /**
     * Get country code.
     *
     * @return |null
     */
    public function getCountryCode()
    {
        return $this->country_code ?? null;
    }

    /**
     * Get phone number formatted as E164 format.
     *
     * @return string
     * @throws \libphonenumber\NumberParseException
     */
    public function getFormattedPhone()
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        $key = config('front-sms.notifiablePhoneKey');

        $phone = $phoneUtil->parse($this->$key, $this->getCountryCode());

        return  $phoneUtil->format($phone, PhoneNumberFormat::E164);
    }
}
