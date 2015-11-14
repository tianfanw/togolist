<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('labels', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('name')->unique();
			$table->integer('count')->unsigned()->default(1);
		});

		Schema::create('label_location_list', function(Blueprint $table)
		{
			$table->integer('location_list_id')->unsigned()->index();
			$table->foreign('location_list_id')->references('id')->on('location_lists')->onDelete('cascade');

			$table->integer('label_id')->unsigned()->index();
			$table->foreign('label_id')->references('id')->on('labels')->onDelete('cascade');
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
		Schema::drop('label_location_list');
		Schema::drop('labels');
	}

}
