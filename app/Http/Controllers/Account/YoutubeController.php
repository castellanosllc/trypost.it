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

class YoutubeController extends Controller
{
    private string $network = 'youtube';

    public function connect()
    {
        return Inertia::location(Socialite::driver($this->network)
            ->redirect());
    }

    public function callback()
    {
        $youtubeUser = Socialite::driver($this->network)
            ->user();

        $user = Auth::user();
        $photo = uploadFromUrl($youtubeUser->getAvatar(), 'accounts');

        Account::updateOrCreate([
            'workspace_id' => $user->workspace_id,
            'space_id' => $user->currentSpace->id,
            'platform' => Platform::YOUTUBE,
            'platform_id' => $youtubeUser->getId(),
        ], [
            'name' => $youtubeUser->getName(),
            'username' => $youtubeUser->getNickname(),
            'photo' => $photo,
            'access_token' => $youtubeUser->token,
            'refresh_token' => $youtubeUser->tokenSecret,
            'expires_in' => null,
            'status' => Status::CONNECTED,
        ]);

        session()->flash('flash.banner', 'Threads account connected successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('accounts.index'));
    }
}
