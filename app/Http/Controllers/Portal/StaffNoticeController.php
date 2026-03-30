<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\StaffNotice;
use App\Models\StaffNoticeRead;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffNoticeController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', StaffNotice::class);

        $notices = StaffNotice::query()
            ->published()
            ->notExpired()
            ->visibleToUser($request->user())
            ->with('createdBy')
            ->latest()
            ->paginate(20);

        return view('portal.staff-notices.index', compact('notices'));
    }

    public function show(Request $request, StaffNotice $staffNotice): View
    {
        $this->authorize('view', $staffNotice);

        $staffNotice->load('createdBy');

        StaffNoticeRead::query()->updateOrCreate(
            [
                'staff_notice_id' => $staffNotice->id,
                'user_id' => $request->user()->id,
            ],
            ['read_at' => now()]
        );

        return view('portal.staff-notices.show', compact('staffNotice'));
    }
}
