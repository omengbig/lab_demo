<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Student;
use App\Models\AttendanceSession;

class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'attendance_session_id' => AttendanceSessionFactory::new(),
            'status' => $this->faker->randomElement(['present', 'absent', 'late']),
        ];
    }
}
