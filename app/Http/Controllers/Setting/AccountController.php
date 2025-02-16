<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Http\Requests\Account\UpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\Account;

class AccountController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request): Response
    {

        $accounts = Account::where('workspace_id', $request->user()->current_workspace_id)->get();

        return Inertia::render('Account/Index', [
            'accounts' => $accounts,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateRequest $request)
    {
        $user = $request->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->theme = $request->theme;

        // If the user is changing their email address, they will need to verify the new address.
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // update the user password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        session()->flash('flash.banner', 'Account updated');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function deletePhoto(Request $request)
    {
        $user = $request->user();
        $user->photo = null;
        $user->save();

        session()->flash('flash.banner', 'Photo deleted');
        session()->flash('flash.bannerStyle', 'success');

        return Inertia::location(route('setting.account.edit'));
    }
}
