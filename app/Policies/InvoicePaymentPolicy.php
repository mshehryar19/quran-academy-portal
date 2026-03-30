<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;

class InvoicePaymentPolicy
{
    public function create(User $user, Invoice $invoice): bool
    {
        return $user->can('payment.manage');
    }

    public function view(User $user, InvoicePayment $invoicePayment): bool
    {
        return $user->can('payment.manage');
    }
}
