<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rfid' => fake()->creditCardNumber(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->lastName(),
            'last_name' => fake()->lastName(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'address' => fake()->address(),
            'date_of_birth' => fake()->date(),
            'email' => fake()->email(),
            'mobile_no' => fake()->phoneNumber(),
        ];
    }
}
