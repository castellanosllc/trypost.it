<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request): Response
    {

        $accounts = $request->user()->workspace->accounts;

        return Inertia::render('Account/Index', [
            'accounts' => $accounts,
        ]);
    }
}
