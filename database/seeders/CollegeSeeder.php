<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\College;

class CollegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        College::factory()->count(10)->create();
    }
}
