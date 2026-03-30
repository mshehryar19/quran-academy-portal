<?php

namespace App\Exports;

use App\Models\EmployeeAttendanceEvent;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeeAttendanceEventsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?int $teacherId,
        private readonly ?string $from,
        private readonly ?string $to,
        private readonly ?string $eventType,
    ) {}

    public function query(): Builder
    {
        $query = EmployeeAttendanceEvent::query()
            ->with(['teacher'])
            ->orderByDesc('occurred_at');

        if ($this->teacherId) {
            $query->where('teacher_id', $this->teacherId);
        }
        if ($this->from) {
            $query->whereDate('attendance_date', '>=', $this->from);
        }
        if ($this->to) {
            $query->whereDate('attendance_date', '<=', $this->to);
        }
        if ($this->eventType) {
            $query->where('event_type', $this->eventType);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Teacher',
            'Date',
            'Event',
            'Occurred at',
            'Class session',
        ];
    }

    /**
     * @param  EmployeeAttendanceEvent  $row
     */
    public function map($row): array
    {
        return [
            $row->teacher?->full_name ?? '—',
            $row->attendance_date?->toDateString() ?? '',
            $row->event_type,
            $row->occurred_at?->toDateTimeString() ?? '',
            $row->class_session_id ?? '',
        ];
    }
}
