<?php namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {

    protected $table = 'tasks';

    protected $fillable = [
        'status',
        'start',
        'end',
        'cloudIdFrom',
        'pathFrom',
        'cloudIdTo',
        'pathTo',
        'action',
        'path'
    ];

    const
        COPY = 1,
        MOVE = 2;

    const
        QUEUE = 1,
        PROGRESS = 2,
        SUCCESS = 3,
        FAIL = 4,
        ERROR_REQUEST = 5;


    public function toProgress()
    {
        $this->status = Task::PROGRESS;
        $this->start = Carbon::now();
        $this->save();
        return $this;
    }

    public function toSuccess()
    {
        $this->status = Task::SUCCESS;
        $this->end = Carbon::now();
        $this->save();
        return $this;
    }

    public function toFail($typeOfError)
    {
        $this->status = $typeOfError;
        $this->end = Carbon::now();
        $this->save();
        return $this;
    }
}
