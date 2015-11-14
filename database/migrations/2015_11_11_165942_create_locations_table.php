<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('locations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('name');
			$table->string('place_id')->unique();
			$table->string('address');
			$table->double('lat');
			$table->double('lng');
		});

		Schema::create('location_location_list', function(Blueprint $table)
		{
			$table->integer('location_id')->unsigned()->index();
			$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');

			$table->integer('location_list_id')->unsigned()->index();
			$table->foreign('location_list_id')->references('id')->on('location_lists')->onDelete('cascade');
			$table->timestamps();
		});

		Schema::create('location_user', function(Blueprint $table)
		{
			$table->integer('location_id')->unsigned()->index();
			$table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');

			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
		Schema::drop('location_location_list');
		Schema::drop('location_user');
		Schema::drop('locations');
	}

}
