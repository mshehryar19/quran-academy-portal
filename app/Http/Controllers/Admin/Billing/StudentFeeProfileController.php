<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\StoreStudentFeeProfileRequest;
use App\Http\Requests\Billing\UpdateStudentFeeProfileRequest;
use App\Models\Student;
use App\Models\StudentFeeProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentFeeProfileController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', StudentFeeProfile::class);

        $profiles = StudentFeeProfile::query()
            ->with('student')
            ->orderByDesc('effective_from')
            ->paginate(30);

        return view('admin.billing.fee-profiles.index', compact('profiles'));
    }

    public function create(): View
    {
        $this->authorize('create', StudentFeeProfile::class);

        $students = Student::query()->orderBy('full_name')->get();

        return view('admin.billing.fee-profiles.create', compact('students'));
    }

    public function store(StoreStudentFeeProfileRequest $request): RedirectResponse
    {
        $profile = StudentFeeProfile::query()->create($request->validated());

        activity()
            ->performedOn($profile)
            ->causedBy($request->user())
            ->event('billing.fee_profile_created')
            ->log('Student fee profile created');

        return redirect()->route('admin.billing.student-fee-profiles.index')->with('status', __('Fee profile saved.'));
    }

    public function show(StudentFeeProfile $student_fee_profile): View
    {
        $this->authorize('view', $student_fee_profile);
        $student_fee_profile->load('student');

        return view('admin.billing.fee-profiles.show', ['profile' => $student_fee_profile]);
    }

    public function edit(StudentFeeProfile $student_fee_profile): View
    {
        $this->authorize('update', $student_fee_profile);
        $student_fee_profile->load('student');
        $students = Student::query()->orderBy('full_name')->get();

        return view('admin.billing.fee-profiles.edit', ['profile' => $student_fee_profile, 'students' => $students]);
    }

    public function update(UpdateStudentFeeProfileRequest $request, StudentFeeProfile $student_fee_profile): RedirectResponse
    {
        $student_fee_profile->update($request->validated());

        activity()
            ->performedOn($student_fee_profile)
            ->causedBy($request->user())
            ->event('billing.fee_profile_updated')
            ->log('Student fee profile updated');

        return redirect()->route('admin.billing.student-fee-profiles.index')->with('status', __('Fee profile updated.'));
    }

    public function destroy(StudentFeeProfile $student_fee_profile): RedirectResponse
    {
        $this->authorize('delete', $student_fee_profile);
        $id = $student_fee_profile->id;
        $student_fee_profile->delete();

        activity()
            ->causedBy(request()->user())
            ->event('billing.fee_profile_deleted')
            ->withProperties(['student_fee_profile_id' => $id])
            ->log('Student fee profile deleted');

        return redirect()->route('admin.billing.student-fee-profiles.index')->with('status', __('Fee profile removed.'));
    }
}
