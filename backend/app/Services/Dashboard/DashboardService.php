<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Queries\Projects\ProjectQuery;
use App\Queries\Tasks\TaskQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(private readonly ProjectQuery $projects, private readonly TaskQuery $tasks) {}

    public function summary(User $actor): array
    {
        $projects = $this->projects->forUser($actor);
        $tasks = $this->tasks->forUser($actor);
        $statuses = (clone $tasks)->select('status', DB::raw('count(*) as total'))->groupBy('status')->get();
        $workload = User::select('id', 'name')->withCount(['tasks' => function (Builder $query) use ($actor): void {
            $query->where('status', '!=', 'done');
            if (! $actor->manages()) {
                $query->whereHas('project.members', fn (Builder $members) => $members->where('users.id', $actor->id));
            }
        }])->get();

        return [
            'active_projects' => (clone $projects)->where('status', 'active')->count(),
            'completed_projects' => (clone $projects)->where('status', 'completed')->count(),
            'overdue_tasks' => (clone $tasks)->where('status', '!=', 'done')->whereDate('due_date', '<', today())->count(),
            'total_tasks' => (clone $tasks)->count(),
            'task_status' => $statuses,
            'workload' => $workload,
        ];
    }
}
