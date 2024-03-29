<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['first_name', 'last_name', 'email', 'password', 'bio'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	/**
	 * Accessor to get user's fullname.
	 *
	 * @return string
	 */
	public function getUsernameAttribute() {
		return $this->first_name.' '.$this->last_name;
	}

	/**
	 * Get all the lists created by the user.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function locationLists() {
		return $this->hasMany('App\LocationList');
	}

	/**
	 * Get locations saved by the user.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public  function locations() {
		return $this->belongsToMany('App\Location')->withTimestamps();
	}

	/**
	 * Return user info.
	 *
	 * @return array
	 */
	public function info() {
		return array(
			"id" => $this->id,
			"name" => $this->getUsernameAttribute(),
//			"avatar_url"
		);
	}
}
