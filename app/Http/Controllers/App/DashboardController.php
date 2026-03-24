<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard.index', [
            'user' => $user,
            'primaryRole' => $user?->getRoleNames()->first() ?? 'Unassigned',
        ]);
    }
}
