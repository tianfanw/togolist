<?php namespace App;

use App\Photo;
use Illuminate\Database\Eloquent\Model;

class Location extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'locations';

    /**
     * Get location lists that contain the location.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function locationLists() {
        return $this->belongsToMany('App\LocationList')->withTimestamps();
    }

    /**
     * Get users who save the location.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this->belongsToMany('App\User')->withTimestamps();
    }

    /**
     * Get photos associated with the location and the user
     *
     * @param null $user_id: the id of the photo uploader, if not set then return photos uploaded by all users
     * @return mixed
     */
    public function photos($user_id = null) {
        $query = Photo::where('location_id', $this->id);
        if($user_id) {
            $query = $query->where('user_id', $user_id);
        }
        return $query;
    }

    public function info() {
        return array(
            "id" => $this->id,
            "name" => $this->name,
            "place_id" => $this->place_id,
            "address" => $this->address,
            "lat" => $this->lat,
            "lng" => $this->lng
        );
    }

}
