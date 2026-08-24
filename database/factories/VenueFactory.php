<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Venue>
 */
class VenueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = ['Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Cần Thơ', 'Hải Phòng'];

        return [
            'name'        => fake()->company() . ' Sports',
            'description' => fake()->paragraph(),
            'address'     => fake()->streetAddress(),
            'ward'        => 'Phường ' . fake()->numberBetween(1, 20),
            'district'    => 'Quận ' . fake()->numberBetween(1, 12),
            'city'        => fake()->randomElement($cities),
            'phone'       => fake()->numerify('02########'),
            'email'       => fake()->companyEmail(),
            'status'      => 'active',
            'amenities'   => ['wifi', 'parking'],
            'rating_avg'  => 0,
        ];
    }
}
