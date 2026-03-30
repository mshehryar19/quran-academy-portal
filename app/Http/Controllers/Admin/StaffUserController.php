<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StaffUserController extends Controller
{
    public function index(): View
    {
        $staff = User::query()
            ->role(['HR', 'Supervisor'])
            ->orderBy('name')
            ->get();

        return view('admin.staff.index', compact('staff'));
    }

    public function create(): View
    {
        return view('admin.staff.create');
    }

    public function store(StoreStaffUserRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => $request->string('password')->toString(),
            'is_active' => true,
        ]);

        $user->assignRole($request->string('role')->toString());

        activity()
            ->performedOn($user)
            ->causedBy($request->user())
            ->withProperties(['role' => $request->string('role')->toString()])
            ->event('staff.created')
            ->log('HR or Supervisor user created');

        return redirect()->route('admin.staff.index')->with('status', 'Staff user created successfully.');
    }
}
