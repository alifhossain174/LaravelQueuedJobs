<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Exports\TasksExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ExportTasksJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 900;

    public function __construct(
        public string $filename = 'exports/tasks.xlsx',
        public ?string $notifyEmail = null,
        public ?string $from = null,
        public ?string $to = null,
    ) {
        // Put this job on a named queue if you like:
        // $this->onQueue('exports');
    }

    public function handle(): void
    {

        @ini_set('memory_limit', '4096M'); // or '512M'

        // Ensure directory exists
        Storage::disk('local')->makeDirectory(dirname($this->filename));

        // Generate the file and save to storage/app/{filename}
        Excel::store(
            new TasksExport($this->from, $this->to),
            $this->filename, // relative to disk root
            'local'          // disk
        );

        // Optional: notify user
        if ($this->notifyEmail) {
            $url = url('/download/tasks?path=' . urlencode($this->filename));
            Mail::raw("Your export is ready.\n\nDownload: {$url}", function ($m) {
                $m->to($this->notifyEmail)->subject('Tasks Export Ready');
            });
        }
    }

    public function failed(Throwable $e): void
    {
        Log::error('ExportTasksJob failed', [
            'error' => $e->getMessage(),
            'file'  => $this->filename,
            'from'  => $this->from,
            'to'    => $this->to,
        ]);

        if ($this->notifyEmail) {
            Mail::raw("Your tasks export failed. Please try again.\n\nError: {$e->getMessage()}", function ($m) {
                $m->to($this->notifyEmail)->subject('Tasks Export Failed');
            });
        }
    }
}
