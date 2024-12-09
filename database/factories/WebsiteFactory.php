<?php

namespace Database\Factories;

use App\Enums\WaitUntil;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Website>
 */
class WebsiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => $this->faker->url(),
            'ignore_robots_txt' => $this->faker->boolean(),
            'wait_until' => $this->faker->randomElement(WaitUntil::cases()),
            'skip_ignored_paths' => $this->faker->boolean(),
            'page_timeout' => 15000,
            'max_concurrent_pages' => 10,
            'hike_user_agent' => $this->faker->boolean(),
        ];
    }
}
