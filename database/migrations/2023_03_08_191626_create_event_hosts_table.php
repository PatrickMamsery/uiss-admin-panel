<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventHostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_hosts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('event_id');
            $table->unsignedBigInteger('user_id');

            $table->index(["event_id"], 'fk_event_hosts_events1_idx');

            $table->index(["user_id"], 'fk_event_hosts_users1_idx');

            $table->foreign('event_id', 'fk_event_hosts_events1_idx')
                ->references('id')->on('events')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('user_id', 'fk_event_hosts_users1_idx')
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
        Schema::dropIfExists('event_hosts');
    }
}
