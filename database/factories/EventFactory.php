<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{

    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'category_id' => fake()->randomElement([1, 2, 3, 4]),
            'user_id' => fake()->numberBetween(12, 21),
            'title' => fake()->sentence(),
            'description' => fake()->text(1000),
            'start' => fake()->date('d-m-Y', 'now'),
            'finish' => fake()->date('d-m-Y', '+2 months'),
            'type' => fake()->randomElement(['открытая', 'закрытая']),
            'rate' => fake()->numberBetween(1, 5),

        ];
    }
}
