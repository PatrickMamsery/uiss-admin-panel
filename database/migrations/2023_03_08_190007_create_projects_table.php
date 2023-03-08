<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();

            $table->unsignedInteger('category_id')->nullable();

            $table->index(["category_id"], 'fk_projects_categories1_idx');

            $table->nullableTimestamps();

            $table->foreign('category_id', 'fk_projects_categories1_idx')
                ->references('id')->on('project_categories')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
