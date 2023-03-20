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
