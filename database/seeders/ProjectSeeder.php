<?php

namespace Database\Seeders;

use App\Models\project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        project::create([
            'user_id' => 3,
            'file_project' => 'projects/project_1.zip',
            'link_github' => 'https://github.com/user1/project1',
            'nama_project' => 'Project 1',
        ]);

        project::create([
            'user_id' => 4,
            'file_project' => 'projects/project_2.zip',
            'link_github' => 'https://github.com/user2/project2',
            'nama_project' => 'Project 2',
        ]);

        project::create([
            'user_id' => 5,
            'file_project' => 'projects/project_3.zip',
            'link_github' => 'https://github.com/user3/project3',
            'nama_project' => 'Project 3',
        ]);

        project::create([
            'user_id' => 3,
            'file_project' => null,
            'link_github' => 'https://github.com/user1/project4',
            'nama_project' => 'Project 4',
        ]);
    }
}
