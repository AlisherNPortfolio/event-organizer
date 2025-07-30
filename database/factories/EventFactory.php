<?php

namespace Database\Factories;

use App\Infrastructure\Models\Event;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('now', '+3 months');
        $endTime = (clone $startTime)->modify('+' . $this->faker->numberBetween(1, 8) . ' hours');
        $isFree = $this->faker->boolean(60); // 60% chance of being free

        return [
            'id' => Str::uuid(),
            'organizer_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraphs(3, true),
            'address' => $this->faker->address(),
            'start_time' => $startTime,
            'end_time' => $this->faker->boolean(80) ? $endTime : null,
            'min_participants' => $this->faker->numberBetween(2, 10),
            'max_participants' => $this->faker->numberBetween(15, 50),
            'price' => $isFree ? 0 : $this->faker->numberBetween(10000, 100000),
            'currency' => 'UZS',
            'is_free' => $isFree,
            'images' => ['/images/default-event.jpg'],
            'status' => $this->faker->randomElement(['upcoming', 'ongoing', 'completed']),
        ];
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'upcoming',
            'start_time' => $this->faker->dateTimeBetween('now', '+1 month'),
        ]);
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => 0,
            'is_free' => true,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $this->faker->numberBetween(10000, 100000),
            'is_free' => false,
        ]);
    }
}
