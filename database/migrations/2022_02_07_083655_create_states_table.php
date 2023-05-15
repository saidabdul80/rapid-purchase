<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
            Schema::create('states', function (Blueprint $table) {
                $table->id('id');
                $table->bigInteger('country_id')->unsigned();                 
                $table->string('name');                                
                $table->string('slogan')->nullable();
                $table->timestamps();
            });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();        
        Schema::dropIfExists('states');
        Schema::enableForeignKeyConstraints();
    }
}
