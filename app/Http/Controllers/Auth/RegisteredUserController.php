<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

use App\Enums\User\Role;

use App\Models\User;
use App\Models\Workspace;
use App\Models\Language;
use App\Models\Space;
use App\Models\Plan;
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $workspace = Workspace::create([
            'name' => "$request->name's Workspace",
            'plan_id' => Plan::where('name', 'Free')->first()->id,
        ]);

        $space = Space::create([
            'name' => "$request->name's Space",
            'workspace_id' => $workspace->id,
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => Role::OWNER,
            'language_id' => Language::where('code', 'en')->first()->id,
            'workspace_id' => $workspace->id,
            'current_space_id' => $space->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('posts.index', absolute: false));
    }
}
