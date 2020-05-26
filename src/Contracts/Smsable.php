<?php

namespace RolfHaug\FrontSms\Contracts;

interface Smsable
{
    public function to($to);

    public function from($from);

    public function message($message);

    public function price(int $price);
}
