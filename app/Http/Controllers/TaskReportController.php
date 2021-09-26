<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Exports\TaskReport;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Maatwebsite\Excel\Excel;

class TaskReportController extends Controller
{
    /**
     * The report page.
     *
     * @param Request $request
     * @return void
     */
    public function report(Request $request)
    {
        $reports = Task::query()
            ->select([
                'completed_count' => Task::selectRaw('count(id)')
                    ->ownedBy($request->user())
                    ->onlyRootParent()
                    ->where('status', TaskStatus::COMPLETED)
                    ->limit(1),
                'cancelled_count' => Task::selectRaw('count(id)')
                    ->ownedBy($request->user())
                    ->onlyRootParent()
                    ->where('status', TaskStatus::CANCELLED)
                    ->limit(1),
                'pending_count' => Task::selectRaw('count(id)')
                    ->ownedBy($request->user())
                    ->onlyRootParent()
                    ->where('status', TaskStatus::PENDING)
                    ->limit(1),
                'others_count'	=> Task::selectRaw('count(id)')
                    ->ownedBy($request->user())
                    ->onlyRootParent()
                    ->whereNotIn('status', TaskStatus::getValues())
                    ->limit(1),
            ])
            ->first();

        return Inertia::render('Task/Report', [
            'reports' => optional($reports)->toArray() ?: []
        ]);
    }

    public function reportDownload(Request $request)
    {
        $exportableFileType = [
            'xlsx' => Excel::XLSX,
            'csv'  => Excel::CSV,
            'json' => 'custom'
        ];

        $request->validate([
            'filetype' => [Rule::in(array_keys($exportableFileType))]
        ]);

        $requestedFileType = Str::lower($request->input('filetype', 'xlsx'));
        $filename = "reports.${requestedFileType}";

        if ($requestedFileType === 'json') {
            return response()->streamDownload(function () use ($request) {
                $reportInstance = new TaskReport;
                echo ($reportInstance)
                    ->ownedBy($request->user())
                    ->query()
                    ->get()
                    ->map(function ($task) use ($reportInstance) {
                        return array_combine(
                            $reportInstance->headings(),
                            $reportInstance->map($task)
                        );
                    })
                    ->toJson(JSON_PRETTY_PRINT);
            }, $filename);
        } else {
            return (new TaskReport)
                ->ownedBy($request->user())
                ->download($filename, $exportableFileType[$requestedFileType]);
        }
    }
}
