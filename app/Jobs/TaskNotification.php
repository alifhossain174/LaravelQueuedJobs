<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class TaskNotification implements ShouldQueue
{
    use Queueable;

    /**
    * Number of attempts before failing the job.
    */
    public int $tries = 2;


    /**
     * Create a new job instance.
     */
    public function __construct(public array $data)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $name        = $this->data['name'] ?? 'New Task';
        $description = $this->data['description'] ?? '';
        $emails      = (array)($this->data['assigned_users'] ?? []);

        foreach ($emails as $email) {
            Mail::raw("Task: {$name}\n\n{$description}", function ($m) use ($email, $name) {
                $m->to($email)->subject("New Task: {$name}");
            });
        }
    }

    public function failed(Throwable $e): void
    {
        Log::error('TaskNotification failed', [
            'error' => $e->getMessage(),
            'data'  => $this->data,
        ]);

        $adminEmail = "alifhossain174@gmail.com";
        $subjectName = "Sending Email";
        Mail::raw("A Job has been failed. Please check", function ($m) use ($adminEmail, $subjectName) {
            $m->to($adminEmail)->subject("Failed Job: {$subjectName}");
        });

    }
}
