<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->string('reg_no')->nullable();
            $table->string('area_of_interest')->nullable();
            $table->unsignedInteger('university_id');
            $table->unsignedInteger('college_id');
            $table->unsignedInteger('department_id');
            $table->unsignedInteger('degree_programme_id');

            $table->index(["degree_programme_id"], 'fk_member_details_degree_programmes1_idx');
            $table->index(["university_id"], 'fk_member_details_universities1_idx');
            $table->index(["college_id"], 'fk_member_details_colleges1_idx');
            $table->index(["department_id"], 'fk_member_details_departments1_idx');
            $table->index(["user_id"], 'fk_member_details_users1_idx');

            $table->nullableTimestamps();

            $table->foreign('user_id', 'fk_member_details_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('university_id', 'fk_member_details_universities1_idx')
                ->references('id')->on('universities')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('college_id', 'fk_member_details_colleges1_idx')
                ->references('id')->on('colleges')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('department_id', 'fk_member_details_departments1_idx')
                ->references('id')->on('departments')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('degree_programme_id', 'fk_member_details_degree_programmes1_idx')
                ->references('id')->on('degree_programmes')
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
        Schema::dropIfExists('member_details');
    }
}
