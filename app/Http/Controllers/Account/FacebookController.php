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

class FacebookController extends Controller
{
    private string $network = 'facebook';

    public function connect()
    {
        return Inertia::location(Socialite::driver($this->network)
            ->redirect());
    }

    public function callback()
    {
        $facebookUser = Socialite::driver($this->network)
            ->user();

        $user = Auth::user();

        Account::updateOrCreate([
            'workspace_id' => $user->workspace_id,
            'space_id' => $user->currentSpace->id,
            'platform' => Platform::FACEBOOK,
            'platform_id' => $facebookUser->getId(),
        ], [
            'name' => $facebookUser->getName(),
            'username' => $facebookUser->getNickname(),
            'photo' => $facebookUser->getAvatar(),
            'access_token' => $facebookUser->token,
            'refresh_token' => $facebookUser->tokenSecret,
            'expires_in' => null,
            'status' => Status::CONNECTED,
        ]);

        session()->flash('flash.banner', 'Pinterest account connected successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('accounts.index'));
    }
}
