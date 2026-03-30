<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\StoreEmployeeSalaryProfileRequest;
use App\Http\Requests\Payroll\UpdateEmployeeSalaryProfileRequest;
use App\Models\EmployeeSalaryProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalaryProfileController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', EmployeeSalaryProfile::class);

        $profiles = EmployeeSalaryProfile::query()
            ->with('user')
            ->orderBy('user_id')
            ->paginate(30);

        return view('admin.payroll.salary-profiles.index', compact('profiles'));
    }

    public function create(): View
    {
        $this->authorize('create', EmployeeSalaryProfile::class);

        $users = User::query()
            ->role(['Teacher', 'HR', 'Supervisor', 'Admin'])
            ->whereDoesntHave('salaryProfile')
            ->orderBy('name')
            ->get();

        return view('admin.payroll.salary-profiles.create', compact('users'));
    }

    public function store(StoreEmployeeSalaryProfileRequest $request): RedirectResponse
    {
        $profile = EmployeeSalaryProfile::query()->create($request->validated());

        activity()
            ->performedOn($profile)
            ->causedBy($request->user())
            ->event('salary_profile.created')
            ->log('Employee salary profile created');

        return redirect()->route('admin.salary-profiles.index')->with('status', __('Salary profile saved (PKR).'));
    }

    public function edit(EmployeeSalaryProfile $salary_profile): View
    {
        $this->authorize('update', $salary_profile);
        $salary_profile->load('user');

        return view('admin.payroll.salary-profiles.edit', compact('salary_profile'));
    }

    public function update(UpdateEmployeeSalaryProfileRequest $request, EmployeeSalaryProfile $salary_profile): RedirectResponse
    {
        $salary_profile->update($request->validated());

        activity()
            ->performedOn($salary_profile)
            ->causedBy($request->user())
            ->event('salary_profile.updated')
            ->log('Employee salary profile updated');

        return redirect()->route('admin.salary-profiles.index')->with('status', __('Salary profile updated.'));
    }
}
