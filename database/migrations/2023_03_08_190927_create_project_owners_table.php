<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_owners', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->index(["project_id"], 'fk_project_owners_projects1_idx');
            $table->index(["user_id"], 'fk_project_owners_users1_idx');

            $table->foreign('project_id', 'fk_project_owners_projects1_idx')
                ->references('id')->on('projects')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('user_id', 'fk_project_owners_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');


            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_owners');
    }
}
