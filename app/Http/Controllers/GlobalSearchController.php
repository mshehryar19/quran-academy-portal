<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GlobalSearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GlobalSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:search.global']);
    }

    public function __invoke(Request $request, GlobalSearchService $search): View
    {
        $query = $request->string('q')->trim()->toString();
        /** @var User $user */
        $user = $request->user();
        $results = $search->search($user, $query);

        return view('search.results', array_merge(compact('query'), $results));
    }
}
