<?php

namespace App\Console\Commands;

use App\Services\InvoiceGenerationService;
use Illuminate\Console\Command;

class GenerateMonthlyInvoicesCommand extends Command
{
    protected $signature = 'billing:generate-month {year : Billing year (e.g. 2026)} {month : Billing month 1-12} {--student=* : Limit to student ID(s)}';

    protected $description = 'Generate monthly tuition invoices from active fee profiles (GBP/USD), skipping existing periods.';

    public function handle(InvoiceGenerationService $generator): int
    {
        $year = (int) $this->argument('year');
        $month = (int) $this->argument('month');
        $only = $this->option('student');
        $only = $only !== [] ? array_map('intval', $only) : null;

        $created = $generator->generateForBillingMonth($year, $month, null, $only);

        $this->info('Generated '.$created->count().' invoice(s).');

        return self::SUCCESS;
    }
}
