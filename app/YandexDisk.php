<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class YandexDisk extends Model {

    protected $table = 'yandex_access';

    protected $fillable = ['access_token', 'user_id'];

}
