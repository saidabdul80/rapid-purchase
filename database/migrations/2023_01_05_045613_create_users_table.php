<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('salute');
            $table->string('first_name');
            $table->string('other_name')->nullable();
            $table->string('last_name');
            $table->string('email');        
            $table->string('ussd');        
            $table->string('phone_number')->unique();
            $table->string('address');
            $table->enum('user_type', ['owner','customer', 'user'])->default('user');
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('customers');
    }
};
