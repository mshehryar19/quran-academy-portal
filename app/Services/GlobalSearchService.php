<?php

namespace App\Services;

use App\Models\AcademyParent;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GlobalSearchService
{
    private const LIMIT = 12;

    /**
     * @return array{teachers: Collection<int, Teacher>, students: Collection<int, Student>, parents: Collection<int, AcademyParent>, invoices: Collection<int, Invoice>}
     */
    public function search(User $user, string $query): array
    {
        $q = trim($query);
        if (mb_strlen($q) < 2) {
            return [
                'teachers' => collect(),
                'students' => collect(),
                'parents' => collect(),
                'invoices' => collect(),
            ];
        }

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $q).'%';

        $teachers = collect();
        if ($user->can('viewAny', Teacher::class)) {
            $teachers = Teacher::query()
                ->with('user')
                ->where(function (Builder $b) use ($like): void {
                    $b->where('public_id', 'like', $like)
                        ->orWhere('full_name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                })
                ->orderBy('full_name')
                ->limit(self::LIMIT)
                ->get();
        }

        $students = collect();
        $canSearchStudents = $user->can('viewAny', Student::class)
            || ($user->can('search.global') && $user->hasRole('Teacher'));
        if ($canSearchStudents) {
            $students = Student::query()
                ->with('user')
                ->where(function (Builder $b) use ($like): void {
                    $b->where('public_id', 'like', $like)
                        ->orWhere('full_name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                })
                ->orderBy('full_name')
                ->limit(self::LIMIT)
                ->get();
        }

        $parents = collect();
        if ($user->can('viewAny', AcademyParent::class)) {
            $parents = AcademyParent::query()
                ->with('user')
                ->where(function (Builder $b) use ($like): void {
                    $b->where('full_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhereHas('user', fn (Builder $u) => $u->where('email', 'like', $like));
                })
                ->orderBy('full_name')
                ->limit(self::LIMIT)
                ->get();
        }

        $invoices = collect();
        if ($user->can('viewAny', Invoice::class)) {
            $invoices = Invoice::query()
                ->with(['student'])
                ->where(function (Builder $b) use ($like): void {
                    $b->where('invoice_number', 'like', $like)
                        ->orWhereHas('student', function (Builder $s) use ($like): void {
                            $s->where('full_name', 'like', $like)
                                ->orWhere('public_id', 'like', $like)
                                ->orWhere('email', 'like', $like);
                        });
                })
                ->orderByDesc('billing_year')
                ->orderByDesc('billing_month')
                ->limit(self::LIMIT)
                ->get();
        }

        return compact('teachers', 'students', 'parents', 'invoices');
    }
}
