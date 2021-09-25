<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Observers\TaskObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Task extends Model implements Sortable
{
    use HasFactory;
    use SoftDeletes;
    use SortableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'content',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'order_column' => 'int',
        'owner_id'     => 'int',
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        static::creating(function (Model $model) {
            if (filled($model->parent_id)) {
                $parent = self::find($model->parent_id);
                // Pass the ownershipt of our parent task.
                if (blank($model->owner_id)) {
                    $model->owner_id = $parent->owner_id;
                }

                // Set a reference to the top most parent of a subtasks.
                $model->root_parent_id = filled($parent->root_parent_id) ? $parent->root_parent_id : $parent->id;

                if (filled($parent->parent_hierarchy)) {
                    $model->parent_hierarchy = $parent->parent_hierarchy . '/' . $model->parent_id;
                } else {
                    $model->parent_hierarchy = $model->parent_id;
                }
            }
        });

        parent::boot();
    }

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    public static function booted()
    {
        static::observe(TaskObserver::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Filter out the tasks that is the child or grand child the specified tasks.
     *
     * @param Builder $query
     * @param Task $task
     * @return void
     */
    public function scopeDescendantsOf(Builder $query, Task $task)
    {
        $hierarchy = filled($task->parent_hierarchy) ? $task->parent_hierarchy . '/' . $task->id : $task->id;

        $query->where('parent_hierarchy', 'like', $hierarchy . '%');
    }

    /**
     * Filter out all the parent and grand parent of the current tasks.
     *
     * @param Builder $query
     * @param Task $task
     * @return void
     */
    public function scopeAncestorsOf(Builder $query, Task $task)
    {
        if (filled($task->parent_hierarchy)) {
            $query->whereIn('id', explode('/', $task->parent_hierarchy));
        } else {
            $query->whereRaw('1 != 1');
        }
    }

    /**
     * Filter out subtasks.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeOnlyRootParent(Builder $query)
    {
        $query->whereNull('parent_id');
    }

    /**
     * Filter items that are completed only.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeOnlyCompleted(Builder $query)
    {
        $query->where('status', TaskStatus::COMPLETED);
    }

    /**
     * Filter results where owned by specified user.
     *
     * @param Builder $query
     * @param User $user
     * @return Builder
     */
    public function scopeOwnedBy(Builder $query, User $user)
    {
        $query->where('owner_id', $user->getKey());
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Define a one-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    /**
     * Define a one-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subtasksCompleted()
    {
        return $this->subtasks()->onlyCompleted();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * The condition to be used when grouping tasks.
     *
     * @return Builder
     */
    public function buildSortQuery() : Builder
    {
        return static::query()
            ->where('owner_id', $this->owner_id)
            ->where('parent_id', $this->parent_id);
    }

    /**
     * Checks if a current task is a subtasks.
     *
     * @return boolean
     */
    public function isSubtask()
    {
        return filled($this->parent_id);
    }
}
