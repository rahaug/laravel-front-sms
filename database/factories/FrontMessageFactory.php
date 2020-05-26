<?php

namespace RolfHaug\FrontSms\Database\Factories;

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use RolfHaug\FrontSms\FrontMessage;

/** @var Factory $factory */
$factory->define(FrontMessage::class, function (Faker $faker) {
    return [
        'from' => $faker->firstName,
        'to' => $faker->phoneNumber,
        'message' => $faker->sentence,
    ];
});
