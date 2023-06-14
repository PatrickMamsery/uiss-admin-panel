<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_leads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('club_id')->default(1);
            $table->unsignedBigInteger('user_id');

            $table->index(["club_id"], 'fk_club_leads_clubs_idx');
            $table->index(["user_id"], 'fk_club_leads_users_idx');

            $table->foreign('club_id', 'fk_club_leads_clubs_idx')
                ->references('id')->on('clubs')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('user_id', 'fk_club_leads_users_idx')
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
        Schema::dropIfExists('club_leads');
    }
}
