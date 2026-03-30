<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\MonthlySalaryRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MySalaryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->can('salary.view'), 403);

        $records = MonthlySalaryRecord::query()
            ->where('user_id', $user->id)
            ->where('status', MonthlySalaryRecord::STATUS_FINALIZED)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->paginate(12);

        return view('employee.salary.index', compact('records'));
    }

    public function show(Request $request, MonthlySalaryRecord $monthly_salary_record): View
    {
        $this->authorize('view', $monthly_salary_record);

        abort_unless($monthly_salary_record->status === MonthlySalaryRecord::STATUS_FINALIZED, 404);

        return view('employee.salary.show', ['monthlySalaryRecord' => $monthly_salary_record]);
    }
}
