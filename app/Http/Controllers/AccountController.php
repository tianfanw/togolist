<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
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
		$loc_lists = Auth::user()->locationlists;
		$loc_lists_info = array();
		foreach($loc_lists as $loc_list) {
			array_push($loc_lists_info, $loc_list->info());
		}
		return view('account.mylist')->with('loc_lists', $loc_lists_info);
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
