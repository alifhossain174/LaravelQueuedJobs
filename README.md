# Laravel Job Queue Setup

## 1. in your .env
QUEUE_CONNECTION=database

## 2. Create your Job Class
php artisan make:job TaskNotification

## 3. In your Job class
Follow our TaskNotification class inside job

## 4. To trigger the Job 
TaskNotification::dispatch($data)

## 5. in queue.php set database to mysql
'failed' => [<br>
    'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),<br>
    'database' => env('DB_CONNECTION', 'mysql'),<br>
    'table' => 'failed_jobs',<br>
],

## 6. Run this command to active the worker to server your Job
php artisan queue:work (set it in cron in hosting)

## 7. In terms of a failed Jobs
Again the restart the worker<br>
php artisan queue:work (auto by cron)<br>
And send the failed queue to job table<br>
php artisan queue:retry all
