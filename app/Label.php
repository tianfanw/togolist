<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Label extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'labels';

    /**
     * Get location lists associated with the label.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
	public function locationLists() {
        return $this->belongsToMany('App\LocationList')->withTimestamps();
    }

}
