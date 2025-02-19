<?php

declare(strict_types=1);

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;

use App\Models\Account;

use Inertia\Inertia;
use Inertia\Response;

class PlannerController extends Controller
{
    public function __invoke() : Response
    {
        $workspace = Auth::user()->currentWorkspace;

        return Inertia::render('Planner/Index', [
            'accounts' => Account::where('workspace_id', $workspace->id)->get(),
        ]);
    }
}
