<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'message_id'=> $this->faker->numberBetween(1, 100),
            'file_id'=>$this->faker->numberBetween(1, 100),
        ];
    }
}
