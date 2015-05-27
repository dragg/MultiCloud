<?php namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed pathTo
 * @property mixed pathFrom
 * @property mixed cloudIdFrom
 * @property mixed action
 * @property mixed cloudIdTo
 * @property int status
 * @property string start
 * @property string end
 */
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
        'action'
    ];

    const
        COPY = 1,
        MOVE = 2;

    const
        QUEUE = 1,
        PROGRESS = 2,
        SUCCESS = 3,
        FAIL = 4;


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

    public function toFail()
    {
        $this->status = Task::FAIL;
        $this->end = Carbon::now();
        $this->save();
        return $this;
    }
}
