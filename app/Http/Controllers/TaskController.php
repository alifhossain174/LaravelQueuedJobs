<?php

namespace App\Http\Controllers;

use App\Jobs\TaskNotification;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function sendTask(Request $request){

        foreach($request->assigned_users as $assignedEmail){

            for($i=1; $i<=5; $i++){

                Task::insert([
                    'name' => $request->name,
                    'description' => $request->description,
                    'assigned_user' => $assignedEmail,
                    'created_at' => Carbon::now()
                ]);

                // $taskName = $request->name;
                // $taskDescription = $request->description;
                // Mail::raw("Task: {$taskName}\n\n{$taskDescription}", function ($m) use ($assignedEmail, $taskName) {
                //     $m->to($assignedEmail)->subject("New Task: {$taskName}");
                // });
            }

        }

        $data = array();
        $data['name'] = $request->name;
        $data['description'] = $request->description;
        $data['assigned_users'] = $request->assigned_users;
        TaskNotification::dispatch($data);

        return response()->json(['success' => 'Notification send successfully.']);

    }
}
