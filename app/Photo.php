<?php namespace App;

use File;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'photos';

    /**
     * Delete file on deleting the instance
     */
    public function delete() {
        if($this->file_dir) {
            if(File::isFile($this->file_dir)) {
                File::delete($this->file_dir);
            }
        }
        parent::delete();
    }

}
