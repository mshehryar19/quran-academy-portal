<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffNoticeRequest;
use App\Models\StaffNotice;
use App\Models\StaffNoticeTargetRole;
use App\Models\StaffNoticeTargetUser;
use App\Models\User;
use App\Services\StaffNoticeDispatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StaffNoticeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:notifications.manage']);
    }

    public function index(Request $request): View
    {
        $query = StaffNotice::query()->with('createdBy')->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        $notices = $query->paginate(20)->withQueryString();

        return view('admin.staff-notices.index', compact('notices'));
    }

    public function create(): View
    {
        $roles = ['Admin', 'HR', 'Supervisor', 'Teacher', 'Accountant'];
        $users = User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', $roles))
            ->orderBy('name')
            ->get();

        return view('admin.staff-notices.create', compact('roles', 'users'));
    }

    public function store(StoreStaffNoticeRequest $request, StaffNoticeDispatchService $dispatcher): RedirectResponse
    {
        $notice = DB::transaction(function () use ($request): StaffNotice {
            $notice = StaffNotice::query()->create([
                'title' => $request->string('title')->toString(),
                'short_alert' => $request->string('short_alert')->toString(),
                'full_message' => $request->string('full_message')->toString(),
                'category' => $request->string('category')->toString(),
                'severity' => $request->filled('severity') ? $request->string('severity')->toString() : null,
                'recipient_mode' => $request->string('recipient_mode')->toString(),
                'channels' => $request->input('channels'),
                'created_by_user_id' => $request->user()->id,
                'published_at' => now(),
                'expires_at' => $request->filled('expires_at') ? $request->date('expires_at') : null,
            ]);

            if ($notice->recipient_mode === StaffNotice::MODE_ROLES) {
                foreach ($request->input('role_names', []) as $roleName) {
                    StaffNoticeTargetRole::query()->create([
                        'staff_notice_id' => $notice->id,
                        'role_name' => $roleName,
                    ]);
                }
            }

            if ($notice->recipient_mode === StaffNotice::MODE_USERS) {
                foreach ($request->input('user_ids', []) as $userId) {
                    StaffNoticeTargetUser::query()->create([
                        'staff_notice_id' => $notice->id,
                        'user_id' => (int) $userId,
                    ]);
                }
            }

            return $notice;
        });

        $dispatcher->dispatch($notice->fresh(['roleTargets', 'userTargets']));

        return redirect()->route('admin.staff-notices.show', $notice)->with('status', __('Notice published and dispatched.'));
    }

    public function show(StaffNotice $staffNotice): View
    {
        $staffNotice->load(['createdBy', 'roleTargets', 'userTargets.user']);

        return view('admin.staff-notices.show', compact('staffNotice'));
    }

    public function destroy(StaffNotice $staffNotice): RedirectResponse
    {
        $staffNotice->delete();

        return redirect()->route('admin.staff-notices.index')->with('status', __('Notice deleted.'));
    }
}
