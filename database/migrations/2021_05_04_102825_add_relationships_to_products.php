<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipsToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('deo')->unsigned()->nullable();
            $table->bigInteger('slican')->unsigned()->nullable();

            $table->foreign('deo')->references('id')->on('products');
            $table->foreign('slican')->references('id')->on('products');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('deo');
            $table->dropForeign('slican');

            $table->dropColumn('deo');
            $table->dropColumn('slican');

        });
    }
}
