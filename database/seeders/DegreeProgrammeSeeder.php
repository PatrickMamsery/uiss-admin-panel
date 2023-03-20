<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\DegreeProgramme;

class DegreeProgrammeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DegreeProgramme::factory()->count(10)->create();
    }
}
