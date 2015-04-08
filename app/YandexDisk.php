<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class YandexDisk extends Model {

    protected $table = 'yandex_access';

    public $timestamps = false;

    protected $fillable = ['access_token', 'user_id', 'uid'];

}
