<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $dueDate = fake()->dateTimeBetween($startDate, '+2 months');

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'creator_id' => User::factory(),
            'assignee_id' => User::factory(),
            'status' => fake()->randomElement(['Pendiente', 'En progreso', 'Completada']),
            'priority' => fake()->randomElement(['Baja', 'Media', 'Alta']),
            'start_date' => $startDate,
            'due_date' => $dueDate,
            'archived_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Pendiente',
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'En progreso',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Completada',
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'archived_at' => now(),
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn(array $attributes) => [
            'priority' => 'Alta',
        ]);
    }
}
