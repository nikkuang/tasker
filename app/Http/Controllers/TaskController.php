<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tasks = QueryBuilder::for(Task::class)
            ->withCount(['subtasks', 'subtasksCompleted'])
            ->allowedFilters([
                AllowedFilter::callback('only_trashed', function ($query, $value) {
                    if (boolval($value)) {
                        $query->onlyTrashed();
                    }
                })
            ])
            ->ownedBy($request->user())
            ->onlyRootParent()
            ->orderBy('order_column')
            ->get();

        return Inertia::render('Task/Index', [
            'tasks' => $tasks,
            'filter' => $request->only('filter'),
        ]);
    }


    /**
     * Display the edit page.
     *
     * @param Request $request
     * @param Task $task
     * @return void
     */
    public function show(Request $request, $id)
    {
        $task = Task::query()
            ->withTrashed()
            ->withCount('subtasks', 'subtasksCompleted')
            ->with([
                'subtasks' => function ($query) {
                    $query->withCount('subtasks', 'subtasksCompleted');
                    $query->orderBy('order_column');
                },
                'parent'
            ])
            ->findOrFail($id);

        return Inertia::render('Task/Show', [
            'task' => $task
        ]);
    }

    /**
     * Add a subtask under a tasks.
     *
     * @param TaskRequest $request
     * @param Task $task
     * @return void
     */
    public function subtask(TaskRequest $request, Task $task)
    {
        $task->subtasks()->create($request->validated());

        return Redirect::route('tasks.show', $task->getKey());
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(TaskRequest $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->tasks()->create($request->validated());

        return Redirect::route('tasks.index')->with('success', 'Task Created.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        if ($request->input('as_subtask', false)) {
            if ($task->isSubtask()) {
                return Redirect::route('tasks.show', $task->parent_id);
            }

            return Redirect::route('tasks.index');
        }

        return Redirect::route('tasks.show', $task->id);
    }

    /**
     * Return the page for trashed items.
     *
     * @param Request $request
     * @return void
     */
    public function trashed(Request $request)
    {
        $tasks = Task::query()
            ->with(['parent' => function ($query) {
                $query->withTrashed();
            }])
            ->withCount('subtasks')
            ->onlyTrashed()
            ->latest('deleted_at')
            ->get();

        return Inertia::render('Task/Trashed', [
            'tasks' => $tasks
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::withTrashed()->findOrFail($id);

        $task->forceDelete();

        return Redirect::route('tasks.trashed');
    }

    /**
     * Soft delete the specified resource from storage.
     *
     * @param  Task $task
     * @return \Illuminate\Http\Response
     */
    public function trash(Task $task)
    {
        $task->delete();

        if ($task->isSubtask()) {
            return Redirect::route('tasks.show', $task->parent_id);
        }

        return Redirect::route('tasks.index');
    }
}
