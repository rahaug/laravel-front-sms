<?php

namespace RolfHaug\FrontSms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use RolfHaug\FrontSms\FrontInboundMessage;

class FrontInboundMessageFactory extends Factory
{
    protected $model = FrontInboundMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from' => fake()->phoneNumber,
            'to' => fake()->phoneNumber,
            'keyword' => fake()->word,
            'message' => fake()->sentence,
            'counter' => 1,
            'files' => json_encode([]),
            'sent_at' => now(),
        ];
    }
}
