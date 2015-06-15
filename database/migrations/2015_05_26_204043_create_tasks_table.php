<?php

use App\Task;
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
            $table->integer('status')->default(Task::QUEUE);
            $table->integer('cloud_id_from')->unsigned();
            $table->string('path_from');
            $table->integer('cloud_id_to')->unsigned();
            $table->string('path_to');
            $table->integer('action')->default(Task::COPY);
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('path')->nullable();
            $table->integer('user_id')->unsigned();
			$table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cloud_id_from')->references('id')->on('clouds');
            $table->foreign('cloud_id_to')->references('id')->on('clouds');
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
