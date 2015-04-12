<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Cloud extends Model {

    const
        DropBox = 1,
        YandexDisk = 2,
        GoogleDrive = 3;

    protected $table = 'clouds';

    protected $fillable = [
        'user_id',
        'access_token',
        'uid',
        'name',
        'type',
        'token_type',
        'expires_in',
        'created'
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
