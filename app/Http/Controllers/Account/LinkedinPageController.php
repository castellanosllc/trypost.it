<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use App\Models\Account;
use App\Enums\Platform;
use App\Enums\Account\Status;

use Inertia\Inertia;

class LinkedinPageController extends Controller
{
    private string $network = 'linkedin-openid';

    public function connect()
    {
        $workspace = Auth::user()->workspace;

        $response = Gate::inspect('reached-accounts-limit', $workspace);
        if ($response->denied()) {
            session()->flash('flash.banner', 'You have reached the maximum number of accounts for your workspace.');
            session()->flash('flash.bannerStyle', 'danger');

            return back();
        }

        return Inertia::location(Socialite::driver($this->network)
            ->scopes([
                "w_organization_social",
                "r_organization_social",
                "rw_organization_admin",
                "w_member_social",
            ])
            ->with([
                'redirect_uri' => config('services.linkedin-openid.redirect_page')
            ])
            ->redirect());
    }

    public function callback()
    {
        $linkedinUser = Socialite::driver($this->network)
            ->with([
                'redirect_uri' => config('services.linkedin-openid.redirect_page')
            ])
            ->user();

        // Buscar páginas do usuário
        $pages = $this->getLinkedInPages($linkedinUser->token);

        if (empty($pages)) {
            session()->flash('flash.banner', 'No LinkedIn Pages found. Please make sure you have admin access to at least one page.');
            session()->flash('flash.bannerStyle', 'danger');
            return redirect(route('accounts.index'));
        }

        session()->put('linkedin_page_select', [
            'pages' => $pages,
            'user' => encrypt([
                'token' => $linkedinUser->token,
                'refresh_token' => $linkedinUser->refreshToken,
                'expires_in' => $linkedinUser->expiresIn,
            ])
        ]);

        return redirect(route('accounts.linkedin-page.select'));
    }

    public function selectPage()
    {
        $data = session()->get('linkedin_page_select');
        if (!$data) {
            session()->flash('flash.banner', 'No LinkedIn Pages found. Please make sure you have admin access to at least one page.');
            session()->flash('flash.bannerStyle', 'danger');
            return redirect(route('accounts.index'));
        }

        return Inertia::render('Account/LinkedinPage/SelectPage', [
            'pages' => $data['pages'],
            'user' => $data['user'],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_id' => ['required'],
            'name' => ['required', 'string'],
            'photo' => ['required', 'string'],
            'user' => ['required', 'string'],
        ]);

        $workspace = Auth::user()->workspace;
        $linkedinUser = decrypt($request->user);

        Account::updateOrCreate([
            'workspace_id' => $workspace->id,
            'platform' => Platform::LINKEDIN_PAGE,
            'platform_id' => $request->page_id,
        ], [
            'name' => $request->name,
            'username' => $request->name,
            'photo' => $request->photo,
            'access_token' => $linkedinUser['token'],
            'refresh_token' => $linkedinUser['refresh_token'],
            'expires_in' => $linkedinUser['expires_in'],
            'status' => Status::CONNECTED,
        ]);

        // Limpar dados temporários
        session()->forget('linkedin_page_select');

        session()->flash('flash.banner', 'LinkedIn Page was connected successfully.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('accounts.index'));
    }

    private function getLinkedInPages(string $accessToken): array
    {
        $response = Http::withToken($accessToken)
            ->get('https://api.linkedin.com/v2/organizationAcls', [
                'q' => 'roleAssignee',
                'role' => 'ADMINISTRATOR',
                'projection' => '(elements*(organization~(id,localizedName,vanityName,logoV2(original~:playableStreams))))'
            ]);

        if (!$response->successful()) {
            return [];
        }

        $pages = [];
        $elements = $response->json('elements') ?? [];

        foreach ($elements as $element) {
            if (isset($element['organization~'])) {
                $org = $element['organization~'];
                $picture = null;

                if (isset($org['logoV2']['original~']['elements'][0]['identifiers'][0]['identifier'])) {
                    $picture = $org['logoV2']['original~']['elements'][0]['identifiers'][0]['identifier'];
                }

                $pages[] = [
                    'id' => $org['id'],
                    'name' => $org['localizedName'],
                    'vanity_name' => $org['vanityName'] ?? null,
                    'picture' => $picture,
                ];
            }
        }

        return $pages;
    }
}
