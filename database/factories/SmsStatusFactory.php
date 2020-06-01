<?php

namespace RolfHaug\FrontSms\Database\Factories;

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use RolfHaug\FrontSms\DeliveryStatus;

/** @var Factory $factory */
$factory->define(DeliveryStatus::class, function (Faker $faker) {
    return [
        'origid' => $faker->randomNumber(4),
        'status' => $faker->randomElement([DeliveryStatus::RECEIVED_BY_OPERATOR, DeliveryStatus::RECEIVED_BY_RECIPIENT, DeliveryStatus::DELIVERY_FAILED])
    ];
});
