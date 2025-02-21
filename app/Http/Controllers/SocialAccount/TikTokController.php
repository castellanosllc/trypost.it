<?php

namespace App\Http\Controllers\SocialAccount;

use App\Http\Controllers\Controller;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

use App\Models\SocialAccount;

use App\Enums\Platform;
use App\Enums\SocialAccount\Status;

class TiktokController extends Controller
{
    private string $network = 'tiktok';

    public function connect()
    {
        return Inertia::location(Socialite::driver($this->network)->redirect());
    }

    public function callback()
    {
        $tiktokUser = Socialite::driver($this->network)->user();

        dd($tiktokUser);

        $user = Auth::user();

        SocialAccount::updateOrCreate([
            'workspace_id' => $user->current_workspace_id,
            'platform' => Platform::TIKTOK,
            'platform_id' => $tiktokUser->getId(),
        ], [
            'name' => $tiktokUser->getName(),
            'username' => $tiktokUser->getNickname(),
            'photo' => $tiktokUser->getAvatar(),
            'access_token' => $tiktokUser->token,
            'refresh_token' => $tiktokUser->tokenSecret,
            'expires_in' => null,
            'status' => Status::CONNECTED,
        ]);

        session()->flash('flash.banner', 'LinkedIn account connected successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('social-accounts.index'));
    }
}
