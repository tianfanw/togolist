<?php namespace App\Http\Requests;

use Auth;
use App\Http\Requests\Request;
use Log;
class ListRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return Auth::check();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$list_id = $this->route('list');
		if($list_id) {
			return [
				'name' => 'required|min:3|max:30|valid_charset|unique:location_lists,name,'.$list_id,
				'category' => 'required|in:'.implode(",",config('constants.list_categories')),
				'private' => 'required|boolean',
			];
		} else {
			return [
				'name' => 'required|min:3|max:30|valid_charset|unique:location_lists',
				'category' => 'required|in:'.implode(",",config('constants.list_categories')),
				'private' => 'required|boolean',
			];
		}
	}

}
