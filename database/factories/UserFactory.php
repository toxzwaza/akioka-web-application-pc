<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'emp_no' => fake()->unique()->numerify('####'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'gender_flg' => 0,
            'group_id' => 1,
            'position_id' => 1,
            'process_id' => 0,
            'is_admin' => 0,
            'dispatch_flg' => 0,
            'part_flg' => 0,
            'always_order_flg' => 0,
            'duty_flg' => 0,
            'cybozu_flg' => 0,
            'del_flg' => 0,
        ];
    }
}
