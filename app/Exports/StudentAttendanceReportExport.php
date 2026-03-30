<?php

namespace App\Exports;

use App\Models\StudentClassAttendance;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentAttendanceReportExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?int $teacherId,
        private readonly ?int $studentId,
        private readonly ?string $from,
        private readonly ?string $to,
        private readonly ?string $status,
    ) {}

    public function query(): Builder
    {
        $query = StudentClassAttendance::query()
            ->with([
                'classSession.classSchedule.teacher',
                'classSession.classSchedule.student',
            ])
            ->orderByDesc('marked_at');

        $query->whereHas('classSession', function ($q): void {
            if ($this->from) {
                $q->whereDate('session_date', '>=', $this->from);
            }
            if ($this->to) {
                $q->whereDate('session_date', '<=', $this->to);
            }
            if ($this->teacherId) {
                $q->whereHas('classSchedule', fn ($cs) => $cs->where('teacher_id', $this->teacherId));
            }
            if ($this->studentId) {
                $q->whereHas('classSchedule', fn ($cs) => $cs->where('student_id', $this->studentId));
            }
        });

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Session date',
            'Teacher',
            'Student',
            'Status',
            'Marked at',
            'Teacher offered reassignment',
        ];
    }

    /**
     * @param  StudentClassAttendance  $row
     */
    public function map($row): array
    {
        $session = $row->classSession;
        $schedule = $session?->classSchedule;

        return [
            $session?->session_date?->toDateString() ?? '',
            $schedule?->teacher?->full_name ?? '—',
            $schedule?->student?->full_name ?? '—',
            $row->status,
            $row->marked_at?->toDateTimeString() ?? '',
            $row->teacher_available_for_reassignment ? 'yes' : 'no',
        ];
    }
}
