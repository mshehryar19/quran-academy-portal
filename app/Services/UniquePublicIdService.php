<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class UniquePublicIdService
{
    /**
     * Atomically allocate the next teacher public ID (TCH-0001, TCH-0002, ...).
     * Uses identifier_sequences row lock for safe concurrency.
     */
    public function nextTeacherPublicId(): string
    {
        return DB::transaction(function (): string {
            $row = DB::table('identifier_sequences')
                ->where('name', 'teacher')
                ->lockForUpdate()
                ->first();

            if (! $row) {
                throw new \RuntimeException('Teacher identifier sequence is not initialized.');
            }

            $n = (int) $row->next_value;
            DB::table('identifier_sequences')
                ->where('name', 'teacher')
                ->update(['next_value' => $n + 1]);

            return $this->format('TCH', $n);
        });
    }

    /**
     * Atomically allocate the next student public ID (STD-0001, STD-0002, ...).
     */
    public function nextStudentPublicId(): string
    {
        return DB::transaction(function (): string {
            $row = DB::table('identifier_sequences')
                ->where('name', 'student')
                ->lockForUpdate()
                ->first();

            if (! $row) {
                throw new \RuntimeException('Student identifier sequence is not initialized.');
            }

            $n = (int) $row->next_value;
            DB::table('identifier_sequences')
                ->where('name', 'student')
                ->update(['next_value' => $n + 1]);

            return $this->format('STD', $n);
        });
    }

    private function format(string $prefix, int $number): string
    {
        return sprintf('%s-%04d', $prefix, $number);
    }
}
