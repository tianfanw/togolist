<?php namespace App\Http\Controllers;

use Log;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use File;
use App\User;
use App\LocationList;
use App\Location;
use App\Label;
use App\Photo;
use App\Http\Requests\ListRequest;

class ListController extends Controller {

	public function __construct()
	{
		$this->middleware('auth', ['except' => ['index', 'show'] ]);
	}

	/**
	 * Parse request and reconstruct an array of locations
	 * @param Request $request
	 * @return array
	 */
	protected function parseLocations(Request $request) {
		$locations = array();
		foreach ($request->all() as $key => $value)
		{
			$key_array = explode('-', $key, 3);
			if(count($key_array) == 3) {
				$place_id = $key_array[2];
				if(!array_key_exists($place_id, $locations)) {
					$locations[$place_id] = array();
					$locations[$place_id]["photos"] = array();
					$locations[$place_id]["deleted_photo_ids"] = array();
					$locations[$place_id]["delete"] = false;
				}
				if($key_array[0] == "location") {
					switch($key_array[1]) {
						case "name":
							$locations[$place_id]["name"] = $value;
							break;
						case "address":
							$locations[$place_id]["address"] = $value;
							break;
						case "lat":
							$locations[$place_id]["lat"] = floatval($value);
							break;
						case "lng":
							$locations[$place_id]["lng"] = floatval($value);
						case "delete":
							$locations[$place_id]["delete"] = ($value == 1);
						default:
							break;
					}
				} else if($key_array[0] == "photo") {
					$locations[$place_id]["photos"][$key_array[1]] = $value;
				} else if($key_array[0] == "photos" && $key_array[1] == "deleted") {
					$locations[$place_id]["deleted_photo_ids"] = array_filter(explode(',', $value));
				}
			}
		}

		$ret = array();
		foreach($locations as $place_id => $location) {
			$loc_info = array_merge(array('place_id' => $place_id), $location);
			// Add other validation rules here...
			$validator = Validator::make($loc_info, [
				'place_id' => 'required',
				'name' => 'required',
				'address' => 'required',
				'lat' => 'required',
				'lng' => 'required',
			]);

			// Note: It's possible to return errors upon validation fails, but we probably don't want
			// to do so, simply discarding those info is just fine
			if (!$validator->fails()) {
				array_push($ret, $loc_info);
			}
		}
		return $ret;
	}

	/**
	 * Save list locations and photos
	 * @param $locations
	 * @param $loc_list
	 */
	protected function saveLocations($locations, $loc_list) {
		$location_count = $loc_list->location_count;
		foreach($locations as $location_info) {
			// Retrieve the location or create one if not exist
			$location = Location::where('place_id', $location_info['place_id'])->first();
			if(!$location && $location_info['delete']) continue;

			if(!$location) {
				$location = new Location;
				$location->place_id = $location_info['place_id'];
				$location->name = $location_info['name'];
				$location->address = $location_info['address'];
				$location->lat = $location_info['lat'];
				$location->lng = $location_info['lng'];
				$location->save();
			}

			if($location_info['delete']) {
				// Detach location from list
				if($loc_list->locations()->find($location->id)) {
					$loc_list->locations()->detach($location->id);
					$location_count -= 1;
				}
			} else {
				// Attach the location to the list if not attached
				if (!$loc_list->locations()->find($location->id)) {
					$loc_list->locations()->attach($location->id);
					$location_count += 1;
				}
				// Attach the location to the user if not attached
				if (!Auth::user()->locations()->find($location->id)) {
					Auth::user()->locations()->attach($location->id);
				}
			}

			// Count photos associated with the location AND the current user
			$photo_count =
				Photo::where('user_id', Auth::user()->id)->where('location_id', $location->id)->count();

			// Delete photos
			foreach($location_info['deleted_photo_ids'] as $photo_id) {
				$photo = Photo::find($photo_id);
				if($photo && $photo->user_id == Auth::user()->id && $photo->location_id == $location->id) {
					$photo->delete();
					$photo_count--;
				}
			}

			if($photo_count >= config('constants.max_photo_allowed')) continue; // Photo count reaches max

			// Upload and save photos
			foreach($location_info['photos'] as $photo_file) {
				$photo_file_name = time().$photo_file->getClientOriginalName();
				if( $photo_file->move('upload/photos', $photo_file_name) ) {
					// Photo file successfully uploaded
					$photo = new Photo;
					$photo->file_dir = "upload/photos/".$photo_file_name;
					$photo->user_id = Auth::user()->id;
					$photo->location_id = $location->id;
					$photo->save();
					$photo_count++;
					if ($photo_count == config('constants.max_photo_allowed')) break;
				}
			}
		}
		$loc_list->location_count = $location_count;
		$loc_list->save();
	}

	/**
	 * Save list labels
	 * @param $labels
	 * @param $loc_list
	 */
	protected function saveLabels($labels, $loc_list) {
		// Detach deleted labels
		$old_labels = $loc_list->labels;
		foreach($old_labels as $old_label) {
			if(!in_array($old_label->name, $labels)) {
				$loc_list->labels()->detach($old_label->id);
				$old_label->count -= 1;
				$old_label->save();
			}
		}

		// Create and save labels
		foreach ($labels as $label_name) {
			$label = Label::where('name', $label_name)->first();
			if (!$label) {
				$label = new Label;
				$label->name = $label_name;
			} else {
				$label->count += 1;
			}
			$label->save();
			if(!$loc_list->labels()->find($label->id)) {
				$loc_list->labels()->attach($label->id);
			}
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		return view('list.base');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('list.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(ListRequest $request)
	{
//		$locations = $this->parseLocations($request);
//		var_dump($locations);

		// Label Validations
		$labels = array_filter(explode(',', $request->labels));
		$reserved_labels = array_map('strtolower', config('constants.list_categories'));
		foreach($labels as $label) {
			$validator = Validator::make(array('label' => strtolower($label)), [
				'label' => 'valid_charset|min:2|max:20|not_in:' . implode(",", $reserved_labels),
			], [
				'valid_charset' => 'Label "'.$label.'" contains invalid characters.',
				'not_in' => 'Label "'.$label.'" cannot take the name of a category.',
			]);
			if ($validator->fails()) {
				if($request->ajax()) {
					return response()->json([
						'error' => true,
						'message' => $validator->messages()->get('label')[0],
					]);
				} else {
					return redirect()->back()->withInput()->withErrors($validator);
				}
			}
		}

		// If it's not pure validation, save entities
		if(!$request->validation) {
			// Create and save location list
			$loc_list = new LocationList;
			$loc_list->name = $request->name;
			$loc_list->category = $request->category;
			$loc_list->description = $request->description;
			$loc_list->private = $request->private;
			$loc_list->location_count = 0;
			$ref_num = 1;
			for ($i = 1; $i <= 5; $i++) {
				if ($request->has('reference' . $i)) {
					$ref = trim($request['reference' . $i]);
					$loc_list['reference' . $ref_num] = $ref;
					$ref_num++;
				}
			}

			Auth::user()->locationLists()->save($loc_list);

			// Create and save labels
			$this->saveLabels($labels, $loc_list);

			// Save locations and photos
			$locations = $this->parseLocations($request);
//			var_dump($locations);
			$this->saveLocations($locations, $loc_list);

			if($request->ajax()) {
				return response()->json([
					'error' => false,
					'flash_message' => [
						'error' => false,
						'message' => $this->getListCreatedMessage($loc_list->name),
					],
					'redirect_to' => '/list/'.$loc_list->id,
				]);
			} else {
				session()->flash('message', $this->getListCreatedMessage($loc_list->name));
				return redirect('/list/'.$loc_list->id); // With flash messages
			}

		} else {
			// Validation passed response
			if($request->ajax()) {
				return response()->json([
					'error' => false
				]);
			} else {
				return redirect()->back()->withInput();
			}
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, Request $request)
	{
		$loc_list = LocationList::find($id);
		if(!$loc_list) {
			if($request->ajax()) {
				return response()->json([
					'error'=> true,
					'message' => 'List not found.'
				]);
			} else {
				abort(404);
			}
		}
		if($request->ajax()) {
			return response()->json($loc_list->info());
		} else {
			return view('list.show')->with('loc_list', $loc_list->info());
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		// Validate list
		$loc_list = LocationList::find($id);
		if(!$loc_list) {
			abort(404);
		}
		$creator = $loc_list->creator;
		if(!$creator) {
			abort(404);
		}
		if($creator->id != Auth::user()->id) {
			abort(401);
		}

		return view('list.edit')->with('loc_list', $loc_list->info());
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, ListRequest $request)
	{
		// Validate list
		$loc_list = LocationList::find($id);
		if(!$loc_list) {
			abort(404);
		}
		$creator = $loc_list->creator;
		if(!$creator) {
			abort(404);
		}
		if($creator->id != Auth::user()->id) {
			abort(401);
		}

		// Label Validations
		$labels = array_filter(explode(',', $request->labels));
		$reserved_labels = array_map('strtolower', config('constants.list_categories'));
		foreach($labels as $label) {
			$validator = Validator::make(array('label' => strtolower($label)), [
				'label' => 'valid_charset|min:2|max:20|not_in:' . implode(",", $reserved_labels),
			], [
				'valid_charset' => 'Label "'.$label.'" contains invalid characters.',
				'not_in' => 'Label "'.$label.'" cannot take the name of a category.',
			]);
			if ($validator->fails()) {
				if($request->ajax()) {
					return response()->json([
						'error' => true,
						'message' => $validator->messages()->get('label')[0],
					]);
				} else {
					return redirect()->back()->withInput()->withErrors($validator);
				}
			}
		}

		// If it's not pure validation, save entities
		if(!$request->validation) {
			// Update and save location list
			$loc_list->name = $request->name;
			$loc_list->category = $request->category;
			$loc_list->description = $request->description;
			$loc_list->private = $request->private;
			$ref_num = 1;
			for ($i = 1; $i <= 5; $i++) {
				if ($request->has('reference' . $i)) {
					$ref = trim($request['reference' . $i]);
					$loc_list['reference' . $ref_num] = $ref;
					$ref_num++;
				}
			}

			$loc_list->save();

			// Save labels
			$this->saveLabels($labels, $loc_list);

			// Save locations and photos
			$locations = $this->parseLocations($request);
			$this->saveLocations($locations, $loc_list);

			if($request->ajax()) {
				return response()->json([
					'error' => false,
					'flash_message' => [
						'error' => false,
						'message' => $this->getListUpdatedMessage($loc_list->name),
					],
					'redirect_to' => '/list/'.$loc_list->id,
				]);
			} else {
				session()->flash('message', $this->getListUpdatedMessage($loc_list->name));
				return redirect('/list/'.$loc_list->id); // With flash messages
			}

		} else {
			// Validation passed response
			if($request->ajax()) {
				return response()->json([
					'error' => false
				]);
			} else {
				return redirect()->back()->withInput();
			}
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		return view('list.base');
	}

	/**
	 * Get the list created message.
	 *
	 * @return string
	 */
	protected function getListCreatedMessage($list_name)
	{
		return 'List '.$list_name.' has been successfully created!';
	}

	/**
	 * Get the list updated message.
	 *
	 * @return string
	 */
	protected function getListUpdatedMessage($list_name)
	{
		return 'List '.$list_name.' has been successfully updated!';
	}
}
