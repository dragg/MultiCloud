<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Dropbox extends Model {

    protected $table = 'dropbox_access';

    public $timestamps = false;

    protected $fillable = ['access_token', 'user_id', 'uid'];

}
