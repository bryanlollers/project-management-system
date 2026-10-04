<?php

namespace App\Services\Comments;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Services\Activity\ActivityService;
use Illuminate\Support\Facades\DB;

class CommentService
{
    public function __construct(private readonly ActivityService $activity) {}

    public function create(User $actor, Task $task, array $data): Comment
    {
        return DB::transaction(function () use ($actor, $task, $data): Comment {
            $comment = $task->comments()->create($data + ['user_id' => $actor->id]);
            $this->activity->record($actor, 'Commented on '.$task->title, $task->project_id, $task->id);

            return $comment->load('user');
        });
    }
}
