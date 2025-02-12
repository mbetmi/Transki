<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        //  return [
        //     'sender_id' => $this->faker->numberBetween(1, 100),
        //     'receiver_id' => $this->faker->numberBetween(1, 100),
        //     'object' => $this->faker->sentence(6, true),
        // ];
        return [
            'sender_id' => User::inRandomOrder()->first()->id,  // Prend un employee aléatoire
            'receiver_id' => User::inRandomOrder()->first()->id,  // Prend un autre employee aléatoire
            'object' => $this->faker->sentence(6, true),
        ];
    }
}

