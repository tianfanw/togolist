<?php namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class SignupRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:6',
			'first_name' => 'required',
			'last_name' => 'required'
		];
	}

}
