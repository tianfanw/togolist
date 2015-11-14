<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use App\Location;
use App\Photo;
use Illuminate\Http\Request;

class PhotoController extends Controller {

	/**
	 * Get photos associated with a location and a user
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function index(Request $request)
	{
//		if(!$request->ajax()) {
//			abort(404);
//		}

//		if($request->list_id) {
//			return list sample photos
//		}

		$location_id = null;
		if($request->place_id) {
			$location = Location::where('place_id', $request->place_id)->first();
			if($location) $location_id = $location->id;
		} else if($request->location_id) {
			$location_id = $request->location_id;
		}

		$user_id = null;
		if($request->user_id) {
			$user_id = $request->user_id;
		} else if(Auth::check()) {
			$user_id = Auth::user()->id;
		}

		if($location_id && $user_id) {
			$photos = Photo::where('user_id', $user_id)->where('location_id', $location_id)->get();
			return response()->json($photos);
		} else {
			return response()->json([]);
		}

	}

	public function store(Request $request) {
		$success = false;
		if($request->hasFile('photo') && $request->location_id) {
			$location = Location::find($request->location_id);
			$photo_file = $request->file('photo');
			if($location) {
				$photo_count = $location->photos(Auth::user()->id)->count();
				if($photo_count < config('constants.max_photo_allowed')) {
					$photo_file_name = time().$photo_file->getClientOriginalName();
					if( $photo_file->move('upload/photos', $photo_file_name) ) {
						// Photo file successfully uploaded
						$photo = new Photo;
						$photo->file_dir = "upload/photos/".$photo_file_name;
						$photo->user_id = Auth::user()->id;
						$photo->location_id = $location->id;
						$photo->save();
						$success = true;
					}
				}
			}
		}
		if($success) {
			if($request->ajax()) {
				return response()->json([
					'error' => false,
					'id' => $photo->id,
					'url' => '/'.$photo->file_dir,
					'flash_message' => [
						'error' => false,
						'message' => $this->getSaveSuccessMessage(),
						'is_important' => false,
					]]);
			} else {
				session()->flash('message', $this->getSaveSuccessMessage());
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
		$success = false;
		$photo = Photo::find($id);
		if($photo && $photo->user_id == Auth::user()->id) {
			// returning null means deletion success
			if(!$photo->delete()) {
				$success = true;
			}
		}
		if($success) {
			if($request->ajax()) {
				return response()->json([
					'error' => false,
					'flash_message' => [
						'error' => false,
						'message' => $this->getDeleteSuccessMessage(),
						'is_important' => false,
					]]);
			} else {
				session()->flash('message', $this->getDeleteSuccessMessage());
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

	protected function getSaveSuccessMessage() {
		return 'Photo has been successfully uploaded!';
	}

	protected function getSaveFailedMessage() {
		return 'Failed to upload the photo.';
	}

	protected function getDeleteSuccessMessage() {
		return 'Photo has been successfully deleted!';
	}

	protected function getDeleteFailedMessage() {
		return 'Failed to delete the photo.';
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
