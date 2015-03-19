<?php
/**
 * Created by PhpStorm.
 * User: nikolay
 * Date: 3/19/15
 * Time: 9:25 PM
 */
use Illuminate\Database\Seeder;
use Illuminate\Contracts\Auth\Registrar;

class UserTableSeeder extends Seeder {

    public function __construct(Registrar $registrar)
    {
        $this->registrar = $registrar;
    }

    public function run()
    {
        DB::table('users')->delete();

        $this->registrar->create([
            'name' => 'Zhidenko Nikolay',
            'email' => 'dragg.ko@gmail.com',
            'password' => 'zhidenko'
        ]);
    }

}