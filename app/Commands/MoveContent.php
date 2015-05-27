<?php namespace App\Commands;

use App\Services\ContentService;
use App\Task;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use \Log;

class MoveContent extends Command implements SelfHandling, ShouldBeQueued {

    use InteractsWithQueue, SerializesModels;

    protected $task;

    protected $contentService;

    /**
     * Create a new command instance.
     * @param \App\Task $task
     * @param \App\Services\ContentService $contentService
     */
	public function __construct(Task $task, ContentService $contentService)
	{
        $this->contentService = $contentService;
		$this->task = $task;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
        $task = $this->task;
        if($task->cloudIdFrom === $task->cloudIdTo) {
            if($task->action === Task::COPY) {
                Log::debug('copy');
                $this->contentService
                    ->copyContent($task->cloudIdFrom, $task->pathFrom, $task->pathTo);
            }
            elseif($task->action === Task::MOVE) {
                $this->contentService
                    ->moveContent($task->cloudIdFrom, $task->pathFrom, $task->pathTo);
            }
        }
        else {
            //repeat download data and upload
        }
	}

}
