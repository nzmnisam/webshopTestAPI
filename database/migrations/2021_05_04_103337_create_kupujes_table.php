<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKupujesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kupuje', function (Blueprint $table) {
            $table->bigInteger('UserID')->unsigned();
            $table->bigInteger('ProductID')->unsigned();
            $table->date('date');
            $table->timestamps();

            $table->foreign('UserID')->references('id')->on('users');
            $table->foreign('ProductID')->references('id')->on('products');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kupujes');
    }
}
