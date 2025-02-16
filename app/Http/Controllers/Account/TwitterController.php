<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

use App\Models\Account;

use App\Enums\Account\Platform;
use App\Enums\Account\Status;

class TwitterController extends Controller
{
    private string $network = 'twitter';

    public function connect()
    {
        return Socialite::driver($this->network)->redirect();
    }

    public function callback()
    {
        $twitterUser = Socialite::driver($this->network)->user();

        $user = Auth::user();

        Account::updateOrCreate([
            'workspace_id' => $user->current_workspace_id,
            'platform' => Platform::TWITTER,
            'platform_id' => $twitterUser->getId(),
        ], [
            'username' => $twitterUser->getName(),
            'photo' => $twitterUser->getAvatar(),
            'access_token' => $twitterUser->token,
            'refresh_token' => $twitterUser->tokenSecret,
            'expires_in' => null,
            'status' => Status::CONNECTED,
        ]);

        session()->flash('flash.banner', 'LinkedIn account connected successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('accounts.index'));
    }
}
