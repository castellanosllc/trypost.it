<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

use Laravel\Socialite\Facades\Socialite;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

use App\Models\Account;

use App\Enums\Platform;
use App\Enums\Account\Status;

use Inertia\Inertia;

class LinkedinController extends Controller
{
    private string $network = 'linkedin-openid';

    private array $scopes = [
        'r_basicprofile',
        "w_organization_social",
        "r_organization_social",
        "rw_organization_admin",
        "w_member_social",
    ];

    public function connect()
    {
        return Inertia::location(Socialite::driver($this->network)
            ->scopes($this->scopes)
            ->redirect());
    }

    public function callback()
    {
        $linkedinUser = Socialite::driver($this->network)
            ->scopes($this->scopes)
            ->user();

        $photo = uploadFromUrl($linkedinUser->getAvatar(), 'accounts');
        $user = Auth::user();

        Account::updateOrCreate([
            'workspace_id' => $user->workspace_id,
            'space_id' => $user->currentSpace->id,
            'platform' => Platform::LINKEDIN,
            'platform_id' => $linkedinUser->getId(),
        ], [
            'name' => $linkedinUser->getName(),
            'username' => $linkedinUser->getNickname(),
            'photo' => $photo,
            'access_token' => $linkedinUser->token,
            'refresh_token' => $linkedinUser->refreshToken,
            'expires_in' => $linkedinUser->expiresIn,
            'status' => Status::CONNECTED,
        ]);

        session()->flash('flash.banner', 'LinkedIn Page was connected successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('accounts.index'));
    }
}
