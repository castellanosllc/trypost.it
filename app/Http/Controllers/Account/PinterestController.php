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

class PinterestController extends Controller
{
    private string $network = 'pinterest';

    public function connect()
    {
        return Inertia::location(Socialite::driver($this->network)
            ->redirect());
    }

    public function callback()
    {
        $pinterestUser = Socialite::driver($this->network)
            ->user();

        $user = Auth::user();

        Account::updateOrCreate([
            'workspace_id' => $user->workspace_id,
            'space_id' => $user->currentSpace->id,
            'platform' => Platform::PINTEREST,
            'platform_id' => $pinterestUser->getId(),
        ], [
            'name' => $pinterestUser->getName(),
            'username' => $pinterestUser->getNickname(),
            'photo' => $pinterestUser->getAvatar(),
            'access_token' => $pinterestUser->token,
            'refresh_token' => $pinterestUser->tokenSecret,
            'expires_in' => null,
            'status' => Status::CONNECTED,
        ]);

        session()->flash('flash.banner', 'Pinterest account connected successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('accounts.index'));
    }
}
