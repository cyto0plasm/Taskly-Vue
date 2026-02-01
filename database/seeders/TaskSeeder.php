<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 2; // the ID of the user you want to assign tasks to

        Task::factory()
            ->count(50)
            ->create([
                'creator_id' => $userId
            ]);
    }
}
