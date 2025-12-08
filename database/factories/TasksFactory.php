<?php

namespace Database\Factories;
use App\Models\Categories;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tasks>
 */
class TasksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id_category = Categories::whereNull('deleted_at')->inRandomOrder()->value('id');


        return [
            'title' => fake()->sentence(3), 
            'body' => fake()->paragraph(), 
            'priority' => fake()->randomElement([1, 2, 3]), 
            'status' => fake()->randomElement(['Published', 'archived']), 
            'date_of_completion' => fake()->dateTimeBetween('today', '+30 days')->format('Y-m-d'),
            'category'=>$id_category
        ];
    }
}
