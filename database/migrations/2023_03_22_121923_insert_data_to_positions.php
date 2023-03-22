<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertDataToPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('positions', function (Blueprint $table) {
            //
        });

        DB::table('positions')->insert([
            ['title' => 'Vice Chairperson'],
            ['title' => 'Secretary'],
            ['title' => 'Chairperson'],
            ['title' => 'Treasurer'],
            ['title' => 'Project Manager'],
            ['title' => 'Social Networks Manager'],
            ['title' => 'IT Member'],
            ['title' => 'Events Manager'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('positions', function (Blueprint $table) {
            //
        });

        DB::table('positions')->truncate();
    }
}
