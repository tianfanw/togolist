<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use Validator;
use App\Location;
use App\LocationList;
use Illuminate\Http\Request;

class LocationController extends Controller {

	public function __construct()
	{
		$this->middleware('auth', ['except' => 'index' ]);
	}


	/**
	 * Save location.
	 *
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\Response
	 */
	public function store(Request $request)
	{
		$location = null;
		if($request->id) {
			$location = Location::find($request->id);
		} else if($request->place_id) {
			$location = Location::where('place_id', $request->place_id)->first();
			if(!$location) {
				$validator = Validator::make($request->all(), [
					'name' => 'required',
					'address' => 'required',
					'lat' => 'required',
					'lng' => 'required',
				]);
				if (!$validator->fails()) {
					$location = new Location;
					$location->place_id = $request->place_id;
					$location->name = $request->name;
					$location->address = $request->address;
					$location->lat = $request->lat;
					$location->lng = $request->lng;
					$location->save();
				}
			}
		}

		if($location) {
			if(!Auth::user()->locations()->find($location->id)) {
				Auth::user()->locations()->attach($location->id);
			}
			if($request->ajax()) {
				return response()->json([
					'error' => false,
					'id' => $location->id,
					'flash_message' => [
						'error' => false,
						'message' => $this->getSaveSuccessMessage($location->name),
						'is_important' => false,
					]]);
			} else {
				session()->flash('message', $this->getSaveSuccessMessage($location->name));
				session()->flash('is_important', false);
				return redirect($this->redirectPath());
			}
		} else {
			if($request->ajax()) {
				return response()->json([
					'error' => true,
					'flash_message' => [
						'error' => true,
						'message' => $this->getSaveFailedMessage(),
						'is_important' => false,
					]]);
			} else {
				session()->flash('message', $this->getSaveFailedMessage());
				session()->flash('is_important', false);
				return redirect($this->redirectPath());
			}
		}
	}

	public function destroy($id, Request $request) {
		$location = Location::find($id);
		if($location) {
			Auth::user()->locations()->detach($id);
			if($request->ajax()) {
				return response()->json([
					'error' => false,
					'flash_message' => [
						'error' => false,
						'message' => $this->getDeleteSuccessMessage($location->name),
						'is_important' => false,
					]]);
			} else {
				session()->flash('message', $this->getDeleteSuccessMessage($location->name));
				session()->flash('is_important', false);
				return redirect($this->redirectPath());
			}
		} else {
			if($request->ajax()) {
				return response()->json([
					'error' => true,
					'flash_message' => [
						'error' => true,
						'message' => $this->getDeleteFailedMessage(),
						'is_important' => false,
					]]);
			} else {
				session()->flash('message', $this->getDeleteFailedMessage());
				session()->flash('is_important', false);
				return redirect($this->redirectPath());
			}
		}
	}

	/**
	 * Get locations.
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function index(Request $request) {
		if($request->list_id) {
			$loc_list = LocationList::find($request->list_id);
			if($loc_list) {
				$locations = $loc_list->locations;
				$loc_info = array();
				foreach($locations as $location) {
					array_push($loc_info, $location->info());
				}
				return response()->json($loc_info);
			} else {
				return response()->json([]);
			}
		} else if(Auth::check()){
			$locations = Auth::user()->locations;
			$loc_info = array();
			foreach($locations as $location) {
				array_push($loc_info, $location->info());
			}
			return response()->json($loc_info);
		} else {
			return response()->json([]);
		}

	}

	protected function getSaveSuccessMessage($name) {
		return 'Location "'.$name.'" has been successfully saved!';
	}

	protected function getSaveFailedMessage() {
		return 'Failed to save the location.';
	}

	protected function getDeleteSuccessMessage($name) {
		return 'Location "'.$name.'" has been successfully deleted!';
	}

	protected function getDeleteFailedMessage() {
		return 'Failed to delete the location.';
	}

	/**
	 * Get redirect path.
	 *
	 * @return string
	 */
	public function redirectPath()
	{
		if (property_exists($this, 'redirectPath'))
		{
			return $this->redirectPath;
		}

		return property_exists($this, 'redirectTo') ? $this->redirectTo : '/mylist';
	}

}
