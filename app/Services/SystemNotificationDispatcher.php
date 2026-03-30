<?php

namespace App\Services;

use App\Models\ClassSchedule;
use App\Models\ClassSession;
use App\Models\Invoice;
use App\Models\LeaveRequest;
use App\Models\Student;
use App\Models\User;
use App\Notifications\PortalAlert;
use Illuminate\Support\Facades\URL;

class SystemNotificationDispatcher
{
    /**
     * @return list<string>
     */
    public function systemChannels(): array
    {
        $raw = config('notifications.system_channels', ['database']);
        if (! is_array($raw)) {
            $raw = ['database'];
        }

        $via = [];
        foreach ($raw as $c) {
            $c = strtolower((string) $c);
            if ($c === 'portal' || $c === 'database') {
                $via[] = 'database';
            }
            if ($c === 'email' || $c === 'mail') {
                if (config('notifications.system_email_enabled')) {
                    $via[] = 'mail';
                }
            }
            if ($c === 'whatsapp') {
                $via[] = 'whatsapp';
            }
        }

        $via = array_values(array_unique($via));
        if ($via === []) {
            return ['database'];
        }

        return $via;
    }

    public function scheduleAssigned(ClassSchedule $schedule): void
    {
        $schedule->load(['teacher.user', 'student.user', 'classSlot']);

        $channels = $this->systemChannels();

        if ($schedule->teacher?->user) {
            $this->sendAlert(
                $schedule->teacher->user,
                __('Class schedule assigned'),
                __('You have been assigned a recurring class for :student.', ['student' => $schedule->student?->full_name ?? '—']),
                'schedule_assigned',
                $channels,
                URL::route('teacher.schedule.index')
            );
        }

        if ($schedule->student?->user) {
            $this->sendAlert(
                $schedule->student->user,
                __('Your class schedule'),
                __('A class has been scheduled with :teacher.', ['teacher' => $schedule->teacher?->full_name ?? '—']),
                'schedule_assigned',
                $channels,
                URL::route('dashboard')
            );
        }
    }

    public function invoiceGenerated(Invoice $invoice): void
    {
        $invoice->loadMissing(['student.user', 'student.parents.user']);

        $channels = $this->systemChannels();

        $student = $invoice->student;
        if (! $student) {
            return;
        }

        $body = __('Invoice :num for :month/:year — balance :cur :bal.', [
            'num' => $invoice->invoice_number,
            'month' => $invoice->billing_month,
            'year' => $invoice->billing_year,
            'cur' => $invoice->currency,
            'bal' => $invoice->balanceOutstanding(),
        ]);

        if ($student->user) {
            $this->sendAlert(
                $student->user,
                __('New tuition invoice'),
                $body,
                'invoice_generated',
                $channels,
                $student->user->hasRole('Student') ? URL::route('student.billing.invoices.show', $invoice) : URL::route('dashboard')
            );
        }

        foreach ($student->parents as $parent) {
            if ($parent->user) {
                $this->sendAlert(
                    $parent->user,
                    __('New tuition invoice'),
                    $body,
                    'invoice_generated',
                    $channels,
                    URL::route('parent.billing.invoice.show', [$student, $invoice])
                );
            }
        }
    }

    public function studentAbsentInClass(ClassSession $session, Student $student, bool $teacherOfferedReassignment): void
    {
        $student->loadMissing(['parents.user', 'user']);
        $session->load(['classSchedule.teacher.user', 'classSchedule.student.user', 'classSchedule.classSlot']);

        $channels = $this->systemChannels();

        $studentUser = $student->user;
        if ($studentUser) {
            $this->sendAlert(
                $studentUser,
                __('Marked absent'),
                __('You were marked absent for a class session on :date.', ['date' => $session->session_date->toDateString()]),
                'student_absent',
                $channels,
                URL::route('dashboard')
            );
        }

        foreach ($student->parents as $parent) {
            if ($parent->user) {
                $this->sendAlert(
                    $parent->user,
                    __('Child absent'),
                    __(':name was marked absent for a session on :date.', [
                        'name' => $student->full_name,
                        'date' => $session->session_date->toDateString(),
                    ]),
                    'student_absent',
                    $channels,
                    URL::route('dashboard')
                );
            }
        }

        if ($teacherOfferedReassignment) {
            foreach (User::role('Supervisor')->where('is_active', true)->get() as $supervisor) {
                $this->sendAlert(
                    $supervisor,
                    __('Teacher availability'),
                    __(':teacher flagged availability for reassignment after :student was absent.', [
                        'teacher' => $session->classSchedule?->teacher?->full_name ?? '—',
                        'student' => $student->full_name,
                    ]),
                    'teacher_availability_alert',
                    $channels,
                    URL::route('admin.reports.class-sessions')
                );
            }
        }
    }

    public function leaveFinalDecision(LeaveRequest $leave): void
    {
        $leave->load('user');
        $user = $leave->user;
        if (! $user) {
            return;
        }

        $channels = $this->systemChannels();

        $this->sendAlert(
            $user,
            __('Leave decision'),
            __('Your leave request was :decision by admin.', ['decision' => $leave->admin_decision ?? 'updated']),
            'leave_notice',
            $channels,
            URL::route('employee.leaves.index')
        );
    }

    /**
     * @param  list<string>  $channels
     */
    private function sendAlert(
        User $user,
        string $title,
        string $body,
        string $category,
        array $channels,
        ?string $actionUrl = null
    ): void {
        $user->notify(new PortalAlert($title, $body, $category, $actionUrl, $channels));
    }
}
