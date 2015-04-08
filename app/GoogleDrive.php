<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleDrive extends Model {

    protected $table = 'google_access';

    public $timestamps = false;

    protected $fillable = ['access_token', 'token_type', 'expires_in', 'created', 'user_id', 'uid'];

}
