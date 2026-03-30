<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('leave.request')
            || $user->hasAnyRole(['Admin', 'Supervisor', 'HR']);
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        if ((int) $user->id === (int) $leaveRequest->user_id) {
            return true;
        }

        return $user->hasAnyRole(['Admin', 'Supervisor', 'HR']);
    }

    public function create(User $user): bool
    {
        return $user->can('leave.request');
    }

    public function downloadAttachment(User $user, LeaveRequest $leaveRequest): bool
    {
        return $this->view($user, $leaveRequest);
    }

    public function supervisorDecide(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasRole('Supervisor')
            && $user->can('leave.review')
            && $leaveRequest->awaitingSupervisor();
    }

    public function adminDecide(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasRole('Admin')
            && $leaveRequest->awaitingAdmin();
    }
}
