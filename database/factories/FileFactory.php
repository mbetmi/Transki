<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
       
        return [
            'name' => $this->faker->sentence(6, true),
            'type' => $this->faker->sentence(6, true),
            'size' => $this->faker->numberBetween(100, 10000),
        ];
    }
}
