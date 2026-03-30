<?php

namespace App\Exports;

use App\Models\ClassSession;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClassSessionsReportExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?int $teacherId,
        private readonly ?string $from,
        private readonly ?string $to,
        private readonly ?string $status,
    ) {}

    public function query(): Builder
    {
        $query = ClassSession::query()
            ->with(['classSchedule.teacher', 'classSchedule.student'])
            ->orderByDesc('session_date');

        if ($this->teacherId) {
            $query->whereHas('classSchedule', fn ($q) => $q->where('teacher_id', $this->teacherId));
        }
        if ($this->from) {
            $query->whereDate('session_date', '>=', $this->from);
        }
        if ($this->to) {
            $query->whereDate('session_date', '<=', $this->to);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Teacher',
            'Student',
            'Status',
            'Lesson summary',
        ];
    }

    /**
     * @param  ClassSession  $row
     */
    public function map($row): array
    {
        return [
            $row->session_date?->toDateString() ?? '',
            $row->classSchedule?->teacher?->full_name ?? '—',
            $row->classSchedule?->student?->full_name ?? '—',
            $row->status,
            $row->lessonSummary ? 'yes' : 'no',
        ];
    }
}
