<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('admin.dashboard', [
            'user' => $user,
            'primaryRole' => $user?->getRoleNames()->first() ?? 'Admin',
        ]);
    }
}
