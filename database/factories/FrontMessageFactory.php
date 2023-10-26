<?php

namespace RolfHaug\FrontSms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use RolfHaug\FrontSms\FrontMessage;

class FrontMessageFactory extends Factory
{
    protected $model = FrontMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from' => fake()->firstName,
            'to' => fake()->phoneNumber,
            'message' => fake()->sentence,
        ];
    }
}
