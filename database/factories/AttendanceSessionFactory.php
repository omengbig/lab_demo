<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class AttendanceSessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by_user_id' => User::factory(),
            'week_number' => $this->faker->numberBetween(1, 14),
            'session_date' => $this->faker->date(),
        ];
    }
}
