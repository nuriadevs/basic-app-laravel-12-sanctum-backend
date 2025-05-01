<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{

    protected static ?string $password;

    /**
     * Defines the state of the user's model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'first_surname' => $this->faker->lastName(),
            'second_surname' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'dni' => $this->faker->unique()->bothify('########?'),
            'postal_code' => $this->faker->postcode() ? substr($this->faker->postcode(), 0, 5) : '00000',
            'city' => $this->faker->city(),
            'address' => $this->faker->streetAddress(),
            'phone_number' => $this->faker->phoneNumber(),
            'birthdate' => $this->faker->date('Y-m-d', '2005-01-01'),
            'profile_picture' => null, 
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'customer',
            'is_active' => true,
        ];
    }


    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
