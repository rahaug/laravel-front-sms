<?php

namespace RolfHaug\FrontSms\Database\Factories;

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use RolfHaug\FrontSms\FrontMessageStatus;

/** @var Factory $factory */
$factory->define(FrontMessageStatus::class, function (Faker $faker) {
    return [
        'origid' => $faker->randomNumber(4),
        'status' => $faker->randomElement([FrontMessageStatus::RECEIVED_BY_OPERATOR, FrontMessageStatus::RECEIVED_BY_RECIPIENT, FrontMessageStatus::DELIVERY_FAILED])
    ];
});
