<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\AdminAdvanceDecisionRequest;
use App\Models\AdvanceSalaryRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvanceSalaryAdminController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('adminReview', AdvanceSalaryRequest::class);

        $tab = $request->string('tab', 'pending')->toString();
        $query = AdvanceSalaryRequest::query()->with('user')->orderByDesc('created_at');

        if ($tab === 'pending') {
            $query->where('status', AdvanceSalaryRequest::STATUS_PENDING);
        } elseif ($tab === 'history') {
            $query->whereIn('status', [
                AdvanceSalaryRequest::STATUS_APPROVED,
                AdvanceSalaryRequest::STATUS_REJECTED,
                AdvanceSalaryRequest::STATUS_DEDUCTED,
            ]);
        }

        $requests = $query->paginate(25)->withQueryString();

        return view('admin.payroll.advances.index', compact('requests', 'tab'));
    }

    public function show(AdvanceSalaryRequest $advance_salary_request): View
    {
        $this->authorize('adminReview', AdvanceSalaryRequest::class);
        $advance_salary_request->load('user');

        return view('admin.payroll.advances.show', ['advanceSalaryRequest' => $advance_salary_request]);
    }

    public function decide(AdminAdvanceDecisionRequest $request, AdvanceSalaryRequest $advance_salary_request): RedirectResponse
    {
        if ($advance_salary_request->status !== AdvanceSalaryRequest::STATUS_PENDING) {
            return back()->withErrors(['status' => __('Request is no longer pending.')]);
        }

        $decision = $request->string('decision')->toString();
        $next = Carbon::now()->startOfMonth()->addMonth();

        $advance_salary_request->update([
            'status' => $decision,
            'admin_user_id' => $request->user()->id,
            'admin_comment' => $request->input('comment'),
            'admin_decided_at' => now(),
            'deduction_period_year' => $decision === AdvanceSalaryRequest::STATUS_APPROVED ? (int) $next->year : null,
            'deduction_period_month' => $decision === AdvanceSalaryRequest::STATUS_APPROVED ? (int) $next->month : null,
        ]);

        activity()
            ->performedOn($advance_salary_request)
            ->causedBy($request->user())
            ->event('advance.admin_decision')
            ->withProperties(['decision' => $decision])
            ->log('Advance salary request decided by admin');

        return redirect()->route('admin.advances.index')->with('status', __('Advance request updated.'));
    }
}
