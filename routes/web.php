<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});

// Storage Link
Route::get('/storage-link', function () {
    $link = public_path('storage');

    if (file_exists($link)) {
        if (is_link($link)) {
            unlink($link);
        } elseif (is_dir($link)) {
            File::deleteDirectory($link);
        } else {
            File::delete($link);
        }
    }

    Artisan::call('storage:link');

    Toastr()->success('Storage linked Successfully');
    return back();
})->name('storage.link');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::post('/send/task', [TaskController::class, 'sendTask'])->name('SendTask');
Route::get('/exports/tasks/queue', [TaskController::class, 'exportsTaskQueue'])->name('ExportsTaskQueue');
Route::get('/download/tasks', [TaskController::class, 'downloadTasks'])->name('DownloadTasks');
