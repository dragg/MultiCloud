<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoogleAccess extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('users', function(Blueprint $table)
        {
            $table->string('accessTokenGoogle')->nullable();
            $table->string('token_type_google')->nullable();
            $table->string('expires_in_google')->nullable();
            $table->string('created_google')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        if (Schema::hasColumn('users', 'accessTokenGoogle'))
        {
            Schema::table('users', function($table)
            {
                $table->dropColumn('accessTokenGoogle');
            });
        }

        if (Schema::hasColumn('users', 'token_type_google'))
        {
            Schema::table('users', function($table)
            {
                $table->dropColumn('token_type_google');
            });
        }

        if (Schema::hasColumn('users', 'expires_in_google'))
        {
            Schema::table('users', function($table)
            {
                $table->dropColumn('expires_in_google');
            });
        }

        if (Schema::hasColumn('users', 'created_google'))
        {
            Schema::table('users', function($table)
            {
                $table->dropColumn('created_google');
            });
        }
	}

}
