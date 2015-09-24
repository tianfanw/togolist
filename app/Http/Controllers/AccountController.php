<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AccountController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
	}
	/**
	 * Show mylist
	 *
	 * @return Response
	 */
	public function mylist()
	{
		return view('account.mylist');
	}

	/**
	 * Show mylocation
	 *
	 * @return Response
	 */
	public function mylocation()
	{
		return view('account.mylocation');
	}

	/**
	 * Show profile
	 *
	 * @return Response
	 */
	public function profile()
	{
		return view('account.profile');
	}

	/**
	 * Show message
	 *
	 * @return Response
	 */
	public function message()
	{
		return view('account.message');
	}

}
