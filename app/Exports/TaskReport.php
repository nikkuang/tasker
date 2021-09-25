<?php

namespace App\Exports;

use App\Models\Task;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TaskReport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

    protected $user;

    public function ownedBy(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function query()
    {
        return Task::query()
            ->ownedBy($this->user)
            ->onlyRootParent()
            ->orderBy('order_column');
    }

    /**
    * @var Task $task
    */
    public function map($task) : array
    {
        return [
            $task->content,
            $task->status,
            $task->created_at
        ];
    }

    public function headings(): array
    {
        return [
            'Task',
            'Status',
            'Created',
        ];
    }
}
