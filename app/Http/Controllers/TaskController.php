<?php

namespace App\Http\Controllers;

use App\Jobs\TaskNotification;
use App\Jobs\ExportTasksJob;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function sendTask(Request $request){

        foreach($request->assigned_users as $assignedEmail){

            Task::insert([
                'name' => $request->name,
                'description' => $request->description,
                'assigned_user' => $assignedEmail,
                'created_at' => Carbon::now()
            ]);

            // Uncomment the following lines if you want to send an email immediately
            // $taskName = $request->name;
            // $taskDescription = $request->description;
            // Mail::raw("Task: {$taskName}\n\n{$taskDescription}", function ($m) use ($assignedEmail, $taskName) {
            //     $m->to($assignedEmail)->subject("New Task: {$taskName}");
            // });

        }

        // Dispatch the job to send notificationss
        $data = array();
        $data['name'] = $request->name;
        $data['description'] = $request->description;
        $data['assigned_users'] = $request->assigned_users;
        TaskNotification::dispatch($data);

        return response()->json(['success' => 'Notification send successfully.']);

    }

    public function exportsTaskQueue(){
        $filename = 'exports/tasks' . '.xlsx';

        ExportTasksJob::dispatch(
            filename: $filename,
            notifyEmail: "alifhossain174@gmail.com", // or null if not logged in
            from: null,         // optional filter
            to: null            // optional filter
        )->delay(now()->addSeconds(1));

        Toastr::success('Download Link will be sent to Email', 'Success');
        return back();
    }

    // Download the generated file
    public function downloadTasks(Request $request)
    {
        $path = $request->query('path', 'exports/tasks.xlsx');

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found.');
        }

        return Response::download(
            Storage::path($path),
            basename($path),
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }
}
