<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
        protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=>$this->faker->sentence(3),
            'description'=> $this->faker->paragraph(),
            'status'=>"pending",
            'start_date'=>now(),
            'end_date'=>now()->addDays(10),
            'creator_id'=>33
        ];
    }
}