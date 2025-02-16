<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

use Laravel\Socialite\Facades\Socialite;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

use App\Models\Account;

use App\Enums\Account\Platform;
use App\Enums\Account\Status;

class LinkedinController extends Controller
{
    private string $network = 'linkedin-openid';

    public function connect(Request $request): RedirectResponse
    {
        return Socialite::driver($this->network)
        ->scopes([
            "w_organization_social",
            "r_organization_social",
            "rw_organization_admin",
            "w_member_social",
        ])
        ->redirect();
    }

    public function callback(Request $request)
    {
        $linkedinUser = Socialite::driver($this->network)->user();

        $user = Auth::user();

        Account::updateOrCreate([
            'workspace_id' => $user->current_workspace_id,
            'platform' => Platform::LINKEDIN,
            'platform_id' => $linkedinUser->getId(),
        ], [
            'username' => $linkedinUser->getName(),
            'photo' => $linkedinUser->getAvatar(),
            'access_token' => $linkedinUser->token,
            'refresh_token' => $linkedinUser->refreshToken,
            'expires_in' => $linkedinUser->expiresIn,
            'status' => Status::CONNECTED,
        ]);

        session()->flash('flash.banner', 'LinkedIn account connected successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('accounts.index'));
    }
}
