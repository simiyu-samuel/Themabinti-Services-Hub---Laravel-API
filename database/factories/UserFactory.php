<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
// Generate a random 7-digit number
        $phoneNumberSuffix = $this->faker->numerify('#######');
        // Prepend with a common Kenyan mobile prefix
        $kenyanPrefixes = ['070', '071', '072', '074', '075', '076', '079', '078']; // Common Kenyan prefixes
        $randomPrefix = $this->faker->randomElement($kenyanPrefixes);

        // Convert to 254 format: remove leading 0, prepend 254
        $fullPhoneNumber = '254' . substr($randomPrefix, 1) . $phoneNumberSuffix;


        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $fullPhoneNumber, // <--- ADD THIS LINE
            'email_verified_at' => now(), // Often verified by default for factory users
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'user_type' => 'customer', // Default factory users to customer
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
