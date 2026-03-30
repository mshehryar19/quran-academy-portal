<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\RecomputeMonthlySalaryRequest;
use App\Models\MonthlySalaryRecord;
use App\Models\User;
use App\Services\PayrollComputationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthlySalaryRecordController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('salary.manage'), 403);

        $query = MonthlySalaryRecord::query()->with('user')->orderByDesc('period_year')->orderByDesc('period_month');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $records = $query->paginate(40)->withQueryString();
        $users = User::query()->role(['Teacher', 'HR', 'Supervisor', 'Admin'])->orderBy('name')->get();

        return view('admin.payroll.monthly-records.index', compact('records', 'users'));
    }

    public function show(Request $request, MonthlySalaryRecord $monthly_salary_record): View
    {
        abort_unless($request->user()?->can('salary.manage'), 403);
        $monthly_salary_record->load('user');

        return view('admin.payroll.monthly-records.show', ['monthlySalaryRecord' => $monthly_salary_record]);
    }

    public function recompute(RecomputeMonthlySalaryRequest $request, PayrollComputationService $payroll): RedirectResponse
    {
        $user = User::query()->findOrFail($request->integer('user_id'));

        try {
            $record = $payroll->buildOrRefreshDraft(
                $user,
                $request->integer('period_year'),
                $request->integer('period_month'),
                $request->user()
            );
        } catch (ModelNotFoundException) {
            return back()->withErrors(['user_id' => __('No salary profile found for this employee.')]);
        }

        activity()
            ->performedOn($record)
            ->causedBy($request->user())
            ->event('salary_record.recomputed')
            ->log('Monthly salary draft (re)computed');

        return redirect()->route('admin.monthly-salary-records.show', $record)->with('status', __('Draft salary (PKR) recomputed from attendance, unpaid leave, and scheduled advances.'));
    }

    public function finalize(Request $request, MonthlySalaryRecord $monthly_salary_record, PayrollComputationService $payroll): RedirectResponse
    {
        abort_unless($request->user()?->can('salary.manage'), 403);

        if ($monthly_salary_record->status === MonthlySalaryRecord::STATUS_FINALIZED) {
            return back()->withErrors(['status' => __('Already finalized.')]);
        }

        $payroll->finalizeRecord($monthly_salary_record, $request->user());

        activity()
            ->performedOn($monthly_salary_record->fresh())
            ->causedBy($request->user())
            ->event('salary_record.finalized')
            ->log('Monthly salary record finalized');

        return redirect()->route('admin.monthly-salary-records.show', $monthly_salary_record)->with('status', __('Salary period finalized. Linked advances marked as deducted.'));
    }
}
