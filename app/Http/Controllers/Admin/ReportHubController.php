<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportHubController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:reports.view']);
    }

    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('admin.reports.hub', compact('user'));
    }
}
