<?php

use App\Http\Controllers\Admin\Billing\InvoiceController;
use App\Http\Controllers\Admin\Billing\InvoicePaymentController;
use App\Http\Controllers\Admin\Billing\StudentFeeProfileController;
use App\Http\Controllers\Portal\ParentBillingController;
use App\Http\Controllers\Portal\StudentBillingController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/billing')
    ->middleware(['auth', 'role:Admin|Accountant'])
    ->name('admin.billing.')
    ->group(function (): void {
        Route::resource('student-fee-profiles', StudentFeeProfileController::class)
            ->parameters(['student-fee-profiles' => 'student_fee_profile']);

        Route::get('invoices/generate', [InvoiceController::class, 'generateForm'])->name('invoices.generate');
        Route::post('invoices/generate', [InvoiceController::class, 'generateRun'])->name('invoices.generate.run');
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('invoices.void');
        Route::patch('invoices/{invoice}/tax', [InvoiceController::class, 'updateTax'])->name('invoices.tax');
        Route::post('invoices/{invoice}/payments', [InvoicePaymentController::class, 'store'])->name('invoices.payments.store');
    });

Route::middleware(['auth', 'role:Student', 'permission:student_billing.view'])
    ->prefix('my-billing')
    ->name('student.billing.')
    ->group(function (): void {
        Route::get('/', [StudentBillingController::class, 'index'])->name('index');
        Route::get('/invoices/{invoice}', [StudentBillingController::class, 'show'])->name('invoices.show');
    });

Route::middleware(['auth', 'role:Parent', 'permission:student_billing.view'])
    ->prefix('parent/billing')
    ->name('parent.billing.')
    ->group(function (): void {
        Route::get('/', [ParentBillingController::class, 'index'])->name('index');
        Route::get('/students/{student}/invoices', [ParentBillingController::class, 'studentInvoices'])->name('student.invoices');
        Route::get('/students/{student}/invoices/{invoice}', [ParentBillingController::class, 'showInvoice'])->name('invoice.show');
    });
