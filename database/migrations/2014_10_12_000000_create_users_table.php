<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			// User credentials
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('password', 60);
			$table->string('email_confirm_token')->nullable();
			$table->string('password_reset_token')->nullable();
//			$table->string('facebook_credentials');
			$table->rememberToken();
			$table->timestamps();

			// User profile
			$table->string('last_name', 100)->nullable();
			$table->string('first_name', 100)->nullable();
			$table->string('avatar')->nullable();
			$table->string('bio')->nullable();

			// User system info
			$table->string('last_login_ip')->nullable(); // nullable for the time being
			$table->enum('status', ['unactivated', 'incomplete', 'activated', 'frozen'])->default('unactivated');
			$table->enum('type', ['general', 'admin', 'super'])->default('general');

			// User counters
			$table->smallInteger('saved_location_count', false, true)->default(0);
			$table->smallInteger('liked_list_count', false, true)->default(0);
			$table->smallInteger('shared_list_count', false, true)->default(0);
			$table->smallInteger('new_message_count', false, true)->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
