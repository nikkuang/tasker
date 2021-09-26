<?php

namespace App\Observers;

use App\Enums\TaskStatus;
use App\Models\Task;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function created(Task $task)
    {
        if ($task->isSubtask()) {
            Task::query()
                ->ancestorsOf($task)
                ->where('status', TaskStatus::COMPLETED)
                ->update(['status' => TaskStatus::PENDING]);
        }
    }

    /**
     * Handle the Task "updating" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function updating(Task $task)
    {
        if ($task->isDirty('status')) {
            if ($task->status === TaskStatus::COMPLETED) {
                Task::query()
                    ->descendantsOf($task)
                    ->where('status', TaskStatus::PENDING)
                    ->update(['status' => TaskStatus::COMPLETED]);
            } elseif ($task->status === TaskStatus::PENDING) {
                Task::query()
                    ->where(function ($query) use ($task) {
                        $query->ancestorsOf($task);
                        $query->orWhere->descendantsOf($task);
                    })
                    ->where('status', TaskStatus::COMPLETED)
                    ->update(['status' => TaskStatus::PENDING]);
            } elseif ($task->status === TaskStatus::CANCELLED) {
                Task::query()
                    ->descendantsOf($task)
                    ->update(['status' => TaskStatus::CANCELLED]);
            }
        }
    }

    /**
     * Handle the Task "updated" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function updated(Task $task)
    {
        if ($task->isSubtask() && $task->status === TaskStatus::COMPLETED) {
            // Check other descendants.
            $hasPending = Task::query()
                ->descendantsOf($task->parent)
                ->where('status', TaskStatus::PENDING)
                ->exists();

            if (!$hasPending) {
                $task->parent->update(['status' => TaskStatus::COMPLETED]);
            }
        }
    }
}
