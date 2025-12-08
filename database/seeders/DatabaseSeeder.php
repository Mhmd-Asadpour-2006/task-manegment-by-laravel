<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categories;
use App\Models\Tasks;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Categories::factory(10)->create();

        Tasks::factory(10)->create()->each(function ($task) {
    
            $user_id = User::where('role', 'user')->whereNull('deleted_at')->inRandomOrder()->value('id');
            $user = $user = User::whereNull('deleted_at')->where('id',$user_id)->where('role','user')->first();
            $user->tasks()->attach($task->id);

            $assignee_ids = User::where('role', 'user')
                        ->whereNull('deleted_at')
                        ->inRandomOrder()
                        ->take(rand(1,2))
                        ->pluck('id')
                        ->toArray();

            $task->users()->sync($assignee_ids);

        });

    }
}
