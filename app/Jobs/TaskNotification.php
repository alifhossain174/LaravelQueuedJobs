<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class TaskNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $data)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $name        = $this->data['name'] ?? 'New Task';
        $description = $this->data['description'] ?? '';
        $emails      = (array)($this->data['assigned_users'] ?? []);

        for($i=1; $i<=5; $i++){
            foreach ($emails as $email) {
                Mail::raw("Task: {$name}\n\n{$description}", function ($m) use ($email, $name) {
                    $m->to($email)->subject("New Task: {$name}");
                });
            }
        }

    }
}
