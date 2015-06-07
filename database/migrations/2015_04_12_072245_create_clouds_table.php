<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCloudsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('clouds', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->integer('type');//dropbox or yandex disk or google drive
            $table->string('access_token');
            $table->string('token_type')->nullable();
            $table->string('expires_in')->nullable();
            $table->string('created')->nullable();
            $table->string('uid');//cloud's user id
            $table->string('name')->unique();
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
        Schema::dropIfExists('clouds');
	}

}
