<?php namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Custom validators
		Validator::extend('alpha_space', function($attribute, $value)
		{
			return preg_match('/^[\pL\s]+$/u', $value);
		});
		Validator::extend('alpha_num_space', function($attribute, $value)
		{
			return preg_match('/^[\pL\pN\s]+$/u', $value);
		});
		Validator::extend('valid_charset', function($attribute, $value)
		{
			return preg_match('/^[\pL\pN\s-_.&]+$/u', $value);
		});
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'App\Services\Registrar'
		);
	}

}
