<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tasks', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('status')->default(\App\Task::OPEN);
            $table->integer('cloudIdFrom');
            $table->string('pathFrom');
            $table->integer('cloudIdTo');
            $table->string('pathTo');
            $table->integer('action')->default(\App\Task::COPY);// move or copy
            $table->dateTime('start');
            $table->dateTime('end');
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
        Schema::dropIfExists('tasks');
	}

}
