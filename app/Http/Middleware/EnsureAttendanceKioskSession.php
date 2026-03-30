<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAttendanceKioskSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('attendance_teacher_id')) {
            return redirect()->route('attendance.identify')
                ->with('status', __('Identify with your attendance ID first.'));
        }

        return $next($request);
    }
}
