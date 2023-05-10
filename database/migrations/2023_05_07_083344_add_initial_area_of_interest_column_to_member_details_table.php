<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInitialAreaOfInterestColumnToMemberDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_details', function (Blueprint $table) {
            $table->string('initial_area_of_interest')->after('area_of_interest')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_details', function (Blueprint $table) {
            $table->dropColumn('initial_area_of_interest');
        });
    }
}
