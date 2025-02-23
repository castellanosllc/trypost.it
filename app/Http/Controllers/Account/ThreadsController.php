<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

use App\Models\Account;

use App\Enums\Platform;
use App\Enums\Account\Status;

class ThreadsController extends Controller
{
    private string $network = 'threads';

    public function connect()
    {
        return Inertia::location(Socialite::driver($this->network)
            ->redirect());
    }

    public function callback()
    {
        $threadsUser = Socialite::driver($this->network)
            ->user();

        $photo = uploadFromUrl($threadsUser->getAvatar(), 'accounts');
        $user = Auth::user();

        Account::updateOrCreate([
            'workspace_id' => $user->workspace_id,
            'space_id' => $user->currentSpace->id,
            'platform' => Platform::THREADS,
            'platform_id' => $threadsUser->getId(),
        ], [
            'name' => $threadsUser->getName(),
            'username' => $threadsUser->getNickname(),
            'photo' => $photo,
            'access_token' => $threadsUser->token,
            'refresh_token' => $threadsUser->tokenSecret,
            'expires_in' => null,
            'status' => Status::CONNECTED,
        ]);

        session()->flash('flash.banner', 'Threads account connected successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('accounts.index'));
    }
}
