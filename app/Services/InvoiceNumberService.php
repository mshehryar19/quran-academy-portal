<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InvoiceNumberService
{
    public function __construct(
        private readonly SettingsService $settings
    ) {}

    /**
     * Format: {PREFIX}-{YEAR}-{SEQ4} e.g. INV-2026-0001. Prefix from system settings; sequence is per calendar year.
     */
    public function nextInvoiceNumber(int $year): string
    {
        $prefixRaw = $this->settings->get('invoice_number_prefix', 'INV') ?? 'INV';
        $prefix = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $prefixRaw) ?: 'INV');

        return DB::transaction(function () use ($year, $prefix): string {
            $key = 'invoice_'.$year;

            $row = DB::table('identifier_sequences')
                ->where('name', $key)
                ->lockForUpdate()
                ->first();

            if (! $row) {
                DB::table('identifier_sequences')->insert([
                    'name' => $key,
                    'next_value' => 2,
                ]);

                $n = 1;
            } else {
                $n = (int) $row->next_value;
                DB::table('identifier_sequences')
                    ->where('name', $key)
                    ->update(['next_value' => $n + 1]);
            }

            return sprintf('%s-%d-%04d', $prefix, $year, $n);
        });
    }
}
