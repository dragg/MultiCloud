<?php namespace App;

use App\Services\DropBoxServices;
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

    protected static $dropBoxServices;

    private static $clientIdentifier = "MultiCloudThesis alpha";

    public function __construct(array $attributes = array(), DropBoxServices $dropBoxServices = null)
    {
        parent::__construct($attributes);
        self::$dropBoxServices = $dropBoxServices;
    }

    public function getContents($path)
    {
        $contents = [];
        if($this->type === Cloud::DropBox && self::$dropBoxServices != null) {
            $contents = self::$dropBoxServices->getContents($path, $this->access_token);

        }
        return $contents;
    }
}
