<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Sort the list in the order they where submitted.
     *
     * @param Request $request
     * @return void
     */
    public function sort(Request $request)
    {
        $request->validate([
            'ids'         => [
                'required',
                'array',
                Rule::exists('tasks', 'id')->where(function ($query) {
                    return $query->where('owner_id', auth()->user()->getAuthIdentifier());
                })
            ],
            'start_order' => ['sometimes', 'numeric'],
        ]);

        Task::setNewOrder($request->input('ids'), $request->input('start_order', 1));

        return response()->noContent();
    }

    /**
     * Restore the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $task = Task::query()
            ->ownedBy(auth()->user())
            ->onlyTrashed()
            ->findOrFail($id);

        if (filled($task->parent_hierarchy)) {
            $isBroken =Task::query()
                ->withTrashed()
                ->whereNotNull('deleted_at')
                ->ancestorsOf($task)
                ->exists();

            if ($isBroken) {
                return response()->json([
                    'message' => 'Unabled to restore this tasks, one of its parent task has been deleted.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $task->restore();

        return response()->noContent();
    }

    /**
     * Update the status of the task.
     *
     * @param Request $request
     * @param Task $task
     * @return void
     */
    public function status(Request $request, Task $task)
    {
        Gate::authorize('update', $task);

        $request->validate([
            'status' => ['string', 'max:255']
        ]);

        DB::transaction(function () use ($request, $task) {
            $task->update(['status' => $request->input('status')]);
        });

        return response()->noContent();
    }
}
