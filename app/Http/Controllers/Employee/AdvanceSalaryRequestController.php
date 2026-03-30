<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\StoreAdvanceSalaryRequestRequest;
use App\Models\AdvanceSalaryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvanceSalaryRequestController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', AdvanceSalaryRequest::class);

        $requests = AdvanceSalaryRequest::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('employee.advances.index', compact('requests'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', AdvanceSalaryRequest::class);

        return view('employee.advances.create');
    }

    public function store(StoreAdvanceSalaryRequestRequest $request): RedirectResponse
    {
        $adv = AdvanceSalaryRequest::query()->create([
            'user_id' => $request->user()->id,
            'amount_pkr' => $request->string('amount_pkr')->toString(),
            'reason' => $request->input('reason'),
            'status' => AdvanceSalaryRequest::STATUS_PENDING,
        ]);

        activity()
            ->performedOn($adv)
            ->causedBy($request->user())
            ->event('advance.submitted')
            ->log('Advance salary requested');

        return redirect()->route('employee.advances.index')->with('status', __('Advance request submitted.'));
    }
}
