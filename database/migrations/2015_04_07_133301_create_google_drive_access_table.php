<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleDriveAccessTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('google_access', function(Blueprint $table) {
            $table->increments('id');
            $table->string('access_token');
            $table->string('token_type');
            $table->string('expires_in');
            $table->string('created');
            $table->unsignedInteger('user_id');
            $table->string('uid');//google's user id
            $table->string('name')->default('Google Drive');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists('google_access');
	}

}
