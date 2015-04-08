<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDropboxAccessTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dropbox_access', function(Blueprint $table) {
            $table->increments('id');
            $table->string('access_token');
            $table->unsignedInteger('user_id');
            $table->string('uid');//dropbox's user id
            $table->string('name')->default('Dropbox');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('dropbox_access');
	}

}
