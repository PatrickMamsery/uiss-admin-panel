<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDegreeProgrammesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('degree_programmes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('department_id');

            $table->index(["department_id"], 'fk_degree_programmes_departments1_idx');
            $table->nullableTimestamps();

            $table->foreign('department_id', 'fk_degree_programmes_departments1_idx')
                ->references('id')->on('departments')
                ->onDelete('no action')
                ->onUpdate('no action');
        });

        // insert some data
        DB::table('degree_programmes')->insert([
            [
                'name' => 'Bachelor of Science in Computer Engineering and IT',
                'department_id' => 1,
            ],
            [
                'name' => 'Bachelor of Science in Computer Science',
                'department_id' => 1,
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
        Schema::dropIfExists('degree_programmes');
    }
}
