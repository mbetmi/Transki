<?php

namespace Database\Factories;

use App\Models\Employee; 
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmployeeFactory extends Factory
{
    public function definition()
    { 
        $password = bcrypt($this->faker->password);
        return [
            'name' => $this->faker->sentence(6, true), 
            'password' => $password, 
            'department_name' => $this->faker->sentence(3, true), 
            'type' => $this->faker->sentence(6, true), 
        ];
    }
}
