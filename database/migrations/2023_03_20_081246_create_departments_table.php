<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('college_id');

            $table->index(["college_id"], 'fk_departments_colleges1_idx');

            $table->nullableTimestamps();

            $table->foreign('college_id', 'fk_departments_colleges1_idx')
                ->references('id')->on('colleges')
                ->onDelete('no action')
                ->onUpdate('no action');
        });

        // insert some data
        DB::table('departments')->insert([
            'name' => 'Department of Computer Science and Engineering',
            'college_id' => 2,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}
