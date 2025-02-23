<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

use App\Models\Account;

use App\Enums\Platform;
use App\Enums\Account\Status;

class TikTokController extends Controller
{
    private string $network = 'tiktok';

    private array $scopes = [
        'user.info.basic',
        'user.info.profile',
        'user.info.stats',
        'video.list',
        'video.publish',
        'video.upload',
    ];

    public function connect()
    {
        return Inertia::location(Socialite::driver($this->network)
            ->scopes($this->scopes)
            ->redirect());
    }

    public function callback()
    {
        $tiktokUser = Socialite::driver($this->network)
            ->scopes($this->scopes)
            ->user();

        $photo = uploadFromUrl($tiktokUser->getAvatar(), 'accounts');
        $user = Auth::user();

        Account::updateOrCreate([
            'workspace_id' => $user->workspace_id,
            'space_id' => $user->currentSpace->id,
            'platform' => Platform::TIKTOK,
            'platform_id' => $tiktokUser->getId(),
        ], [
            'name' => $tiktokUser->getName(),
            'username' => $tiktokUser->getNickname(),
            'photo' => $photo,
            'access_token' => $tiktokUser->token,
            'refresh_token' => $tiktokUser->refreshToken,
            'expires_in' => $tiktokUser->expiresIn,
            'status' => Status::CONNECTED,
        ]);

        session()->flash('flash.banner', 'LinkedIn account connected successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('accounts.index'));
    }
}
