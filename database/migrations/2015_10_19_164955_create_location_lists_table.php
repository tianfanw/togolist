<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('location_lists', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->timestamps();

			// List info
			$table->string('name', 30)->unique();
			$table->string('category', 20);
			$table->string('description', 500)->nullable();
			$table->string('reference1')->nullable();
			$table->string('reference2')->nullable();
			$table->string('reference3')->nullable();
			$table->string('reference4')->nullable();
			$table->string('reference5')->nullable();
			$table->boolean('private')->default(false);

			// List counters
			$table->integer('like_count')->default(0);
			$table->integer('view_count')->default(0);
			$table->integer('share_count')->default(0);

			// Cascade deletion on user deletion
			$table->foreign('user_id')
				->references('id')
				->on('users')
				->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('location_lists');
	}

}
