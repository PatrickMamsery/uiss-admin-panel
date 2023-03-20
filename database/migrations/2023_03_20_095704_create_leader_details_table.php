<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leader_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('position_id');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->index(["user_id"], 'fk_leader_details_users1_idx');
            $table->index(["position_id"], 'fk_leader_details_positions1_idx');

            $table->nullableTimestamps();

            $table->foreign('user_id', 'fk_leader_details_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('position_id', 'fk_leader_details_positions1_idx')
                ->references('id')->on('positions')
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
        Schema::dropIfExists('leader_details');
    }
}
