<?php
/**
 * Created by PhpStorm.
 * User: nikolay
 * Date: 3/19/15
 * Time: 10:48 PM
 */

return [
    'yandex_disk' => [
        'id'        => env('YANDEX_DISK_ID'),
        'password'  => env('YANDEX_DISK_PASSWORD'),
    ],

    'dropbox' => [
        'key'       => env('DROPBOX_KEY'),
        'secret'    => env('DROPBOX_SECRET'),
    ],

    'google_drive' => [
        'name'      => env('GOOGLE_DRIVE_NAME'),
        'id'        => env('GOOGLE_DRIVE_ID'),
        'secret'    => env('GOOGLE_DRIVE_SECRET'),
    ],
];