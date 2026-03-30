<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('invoice.manage');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->can('invoice.manage')) {
            return true;
        }

        if (! $user->can('student_billing.view')) {
            return false;
        }

        $invoice->loadMissing('student');

        if ($user->hasRole('Student') && $user->student && (int) $user->student->id === (int) $invoice->student_id) {
            return true;
        }

        if ($user->hasRole('Parent') && $user->academyParent) {
            return $user->academyParent->students()->where('students.id', $invoice->student_id)->exists();
        }

        return false;
    }

    public function manageInternally(User $user, Invoice $invoice): bool
    {
        return $user->can('invoice.manage');
    }

    public function voidInvoice(User $user, Invoice $invoice): bool
    {
        return $user->can('invoice.manage') && $invoice->status !== Invoice::STATUS_CANCELLED;
    }
}
