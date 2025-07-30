<?php

namespace Database\Factories;

use App\Infrastructure\Models\Event;
use App\Infrastructure\Models\Participant;
use App\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ParticipantFactory extends Factory
{
    protected $model = Participant::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'attended' => $this->faker->boolean(70), // 70% attendance rate
            'marked' => $this->faker->boolean(80), // 80% marked
        ];
    }

    public function attended(): static
    {
        return $this->state(fn (array $attributes) => [
            'attended' => true,
            'marked' => true,
        ]);
    }

    public function notAttended(): static
    {
        return $this->state(fn (array $attributes) => [
            'attended' => false,
            'marked' => true,
        ]);
    }

    public function unmarked(): static
    {
        return $this->state(fn (array $attributes) => [
            'marked' => false,
        ]);
    }
}
