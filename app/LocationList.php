<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class LocationList extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'location_lists';

    /**
     * Get the user who creates the list
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator() {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * Get labels of the location list.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function labels() {
        return $this->belongsToMany('App\Label')->withTimestamps();
    }

    /**
     * Get locations of the location list.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function locations() {
        return $this->belongsToMany('App\Location')->withTimestamps();
    }

    public function createdAt() {
        return date("m/d/Y", strtotime($this->created_at));
    }

    public function info() {
        return array(
            "id" => $this->id,
            "name" => $this->name,
            "creator" => $this->creator->info(),
            "created_at" => $this->createdAt(),
            "category" => $this->category,
            "description" => $this->description,
            "labels" => $this->labels()->lists("name"),
            "reference1" => $this->reference1,
            "reference2" => $this->reference2,
            "reference3" => $this->reference3,
            "reference4" => $this->reference4,
            "reference5" => $this->reference5,
            "private" => $this->private,
            "location_count" => $this->location_count,
            "view_count" => $this->view_count,
            "like_count" => $this->like_count,
            "share_count" => $this->share_count
        );
    }
}
