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
'failed' => [
    'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
    'database' => env('DB_CONNECTION', 'mysql'),
    'table' => 'failed_jobs',
],

## 6. Run this command to active the worker to server your Job
php artisan queue:work (set it in cron in hosting)

## 7. In terms of a failed Jobs
Again the restart the worker
php artisan queue:work (auto by cron)
And send the failed queue to job table
php artisan queue:retry all
