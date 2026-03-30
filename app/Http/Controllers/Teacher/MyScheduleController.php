<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\ClassSessionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyScheduleController extends Controller
{
    public function index(Request $request, ClassSessionService $sessionService): View
    {
        $teacher = $request->user()->teacher;

        abort_if(! $teacher, 403);

        $date = $request->filled('date')
            ? Carbon::parse($request->string('date')->toString())->startOfDay()
            : Carbon::today();

        $sessions = $sessionService->syncSessionsForTeacherAndDate($teacher, $date);

        return view('teacher.schedule.index', compact('teacher', 'sessions', 'date'));
    }
}
