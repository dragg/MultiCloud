<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {

    protected $table = 'tasks';

    protected $fillable = ['status', 'start', 'end', 'cloudIdFrom', 'pathFrom', 'cloudIdTo', 'pathTo', 'action'];

    const
        COPY = 1,
        MOVE = 2;

    const
        OPEN = 1,
        QUEUE = 2,
        PROGRESS = 3,
        FINISH = 4;

}
