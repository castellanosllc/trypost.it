<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

use App\Http\Requests\Account\UpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
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

        $accounts = $request->user()->currentWorkspace->accounts;

        return Inertia::render('Account/Index', [
            'accounts' => $accounts,
        ]);
    }
}
