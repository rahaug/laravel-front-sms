<?php

namespace RolfHaug\FrontSms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use RolfHaug\FrontSms\DeliveryStatus;

class DeliveryStatusFactory extends Factory
{

    protected $model = DeliveryStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'origid' => fake()->randomNumber(4),
            'status' => fake()->randomElement([DeliveryStatus::RECEIVED_BY_OPERATOR, DeliveryStatus::RECEIVED_BY_RECIPIENT, DeliveryStatus::DELIVERY_FAILED])
        ];
    }
}
