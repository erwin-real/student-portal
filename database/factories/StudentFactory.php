<?php

namespace Database\Factories;

use App\Models\Level;
use App\Models\Member;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'level_id' => Level::factory(),
            'section_id' => Section::factory(),
            'student_no' => fake()->numberBetween(10000, 100000),
            'guardian' => fake()->name(),
            'mobile' => fake()->phoneNumber(),
        ];
    }
}
