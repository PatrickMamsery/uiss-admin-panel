<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colleges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('university_id');
            $table->string('name')->nullable();

            $table->index(["university_id"], 'fk_colleges_universities1_idx');

            $table->nullableTimestamps();

            $table->foreign('university_id', 'fk_colleges_universities1_idx')
                ->references('id')->on('universities')
                ->onUpdate('no action')
                ->onDelete('no action');
        });

        // insert some data
        DB::table('colleges')->insert([
            [
                'name' => 'College of Education',
                'university_id' => 1,
            ], 
            [
                'name' => 'College of Information and Communication Technologies',
                'university_id' => 1,
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('colleges');
    }
}
