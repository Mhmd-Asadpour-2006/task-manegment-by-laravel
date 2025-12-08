<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Categories>
 */
class CategoriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user_id = User::where('role', 'user')->whereNull('deleted_at')->inRandomOrder()->value('id');

        $word_categories = ['Work','Personal','Urgent','Home','Study','Shopping','Finance','Health','Travel','Fitness','Projects',
        'Business','Ideas','Meetings','Learning','Chores','Family','Technology','Goals','Events','Planning','Hobbies','Daily'
        ,'Notes','Important','Office','Research','Time','Communication'];
        return [
            'title'=>fake()->randomElement($word_categories),
            'user_id'=>$user_id
        ];
    }
}
