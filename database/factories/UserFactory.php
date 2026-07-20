<?php

namespace Database\Factories;

use App\Models\User;
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

     /**
         * Default User Settings By Role
         * Role: Mahasiswa => 'cdmi' => true, 'cdmi_complete' => false, 'dmti' => true, 'dmti_complete' => false
         * Role: Karyawan => 'dmti' => true, 'dmti_complete' => false
         * Role: Dokter, Psikiater, Admin
         */
        public function definition()
        {
            $role = $this->faker->randomElement(['Mahasiswa', 'Dokter', 'Psikiater', 'Karyawan']);
    
            $cdmi = $role === 'Mahasiswa' ? true : null;
            $cdmi_complete = $role === 'Mahasiswa' ? false : null;

            $dmti = $role === 'Mahasiswa' ? true : null;
            $dmti_complete = $role === 'Mahasiswa' ? false : null;
            $dmti = $role === 'Karyawan' ? true : null;
            $dmti_complete = $role === 'Karyawan' ? false : null;
    
            return [
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'role' => $role,
                // 'email_verified_at' => now(),
                'password' => static::$password ??= Hash::make('password'),
                // 'cdmi' => $cdmi,
                // 'cdmi_complete' => $cdmi_complete,
                // 'dmti' => $dmti,
                // 'dmti_complete' => $dmti_complete,
                // 'remember_token' => Str::random(10),
                // 'kesehatan_token' => User::generateUniqueToken(),
                // 'bimbingan_token' => User::generateUniqueTokenBimbingan(),
                // 'konsultasi_token' => User::generateUniqueTokenKonsultasi(),
                // 'kesehatan_token_expired_at' => User::generateExpiredToken(),
                // 'bimbingan_token_expired_at' => User::generateExpiredToken(),
                // 'konsultasi_token_expired_at' => User::generateExpiredToken(),
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
