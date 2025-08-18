<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TasksExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    public function __construct(
        protected ?string $from = null,
        protected ?string $to = null,
    ) {}

    public function query(): Builder
    {
        return Task::query()
            ->select(['id','name','description','assigned_user','created_at'])
            ->when($this->from, fn($q) => $q->whereDate('created_at', '>=', $this->from))
            ->when($this->to,   fn($q) => $q->whereDate('created_at', '<=', $this->to));
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Description', 'Assigned User', 'Created At'];
    }

    public function map($task): array
    {
        return [
            $task->id,
            $task->name,
            $task->description,
            $task->assigned_user,
            optional($task->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // keeps memory low for big datasets
    }
}
