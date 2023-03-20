<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\LeaderDetail;

class LeaderDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LeaderDetail::factory()->count(10)->create();
    }
}
