<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Category;
use App\Models\Task;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Task::class;

    public function definition()
    {
        return [
            //User::factory() を使うとTaskを作るときに自動でUserも作る。
            //'user_id' => User::factory(),
            'user_id' => User::all()->random()->id,
            // または既存レコードから取るなら
            'category_id' => Category::all()->random()->id,
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'priority' => $this->faker->randomElement([1, 2, 3]),
        ];
    }
}
