<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TaskCleanUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:task:clean-up {--days=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up task that where deleted after n days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = $this->option('days');

        return Task::onlyTrashed()
            ->where('deleted_at', '<', Carbon::now()->subDays($days))
            ->forceDelete();
    }
}
