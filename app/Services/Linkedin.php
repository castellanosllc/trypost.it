<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SocialAccount;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class Linkedin
{
    private string $baseUrl = 'https://api.linkedin.com';
    private string $apiVersion = 'v2';
    private SocialAccount $account;

    public function __construct(SocialAccount $account)
    {
        $this->account = $account;
    }

    public function post(Post $post)
    {
        try {
            // First verify if token is valid
            $this->verifyToken();

            // Get user profile URN
            $profileUrn = $this->getProfileUrn();

            $response = Http::withToken($this->account->access_token)
                ->withHeaders([
                    'X-Restli-Protocol-Version' => '2.0.0',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/{$this->apiVersion}/ugcPosts", [
                    'author' => "urn:li:person:{$profileUrn}",
                    'lifecycleState' => 'PUBLISHED',
                    'specificContent' => [
                        'com.linkedin.ugc.ShareContent' => [
                            'shareCommentary' => [
                                'text' => $post->content
                            ],
                            'shareMediaCategory' => 'NONE'
                        ]
                    ],
                    'visibility' => [
                        'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('LinkedIn Profile Post Error', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'account_id' => $this->account->id
                ]);

                if ($response->status() === 401) {
                    // Try to refresh token and post again
                    $this->refreshToken();
                    return $this->post($post); // Try once more
                }
            }

            $response->throw();
            $postData = $response->json();

            return [
                'id' => $postData['id'],
                'url' => "https://www.linkedin.com/feed/update/urn:li:share:{$postData['id']}"
            ];
        } catch (RequestException $e) {
            Log::error('LinkedIn Profile API Error', [
                'error' => $e->getMessage(),
                'response' => $e->response?->json(),
                'account_id' => $this->account->id
            ]);

            throw new \Exception('Failed to post to LinkedIn Profile: ' . $e->getMessage());
        }
    }

    private function getProfileUrn(): string
    {
        try {
            $response = Http::withToken($this->account->access_token)
                ->get("{$this->baseUrl}/{$this->apiVersion}/me");

            if (!$response->successful()) {
                throw new \Exception('Failed to get LinkedIn profile information');
            }

            return $response->json('id');
        } catch (\Exception $e) {
            Log::error('LinkedIn Get Profile Error', [
                'error' => $e->getMessage(),
                'account_id' => $this->account->id
            ]);

            throw new \Exception('Failed to get LinkedIn profile information: ' . $e->getMessage());
        }
    }

    private function verifyToken()
    {
        if ($this->isTokenExpired()) {
            $this->refreshToken();
        }
    }

    private function isTokenExpired(): bool
    {
        if (!$this->account->expires_in) {
            return false;
        }

        $expirationTime = $this->account->updated_at->addSeconds($this->account->expires_in);
        return now()->greaterThan($expirationTime);
    }

    private function refreshToken()
    {
        try {
            $response = Http::asForm()
                ->post("{$this->baseUrl}/oauth/v2/accessToken", [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $this->account->refresh_token,
                    'client_id' => config('services.linkedin-openid.client_id'),
                    'client_secret' => config('services.linkedin-openid.client_secret'),
                ]);

            if ($response->successful()) {
                $tokenData = $response->json();

                $this->account->update([
                    'access_token' => $tokenData['access_token'],
                    'refresh_token' => $tokenData['refresh_token'] ?? $this->account->refresh_token,
                    'expires_in' => $tokenData['expires_in'] ?? null,
                ]);

                // Refresh the account instance
                $this->account->refresh();
            } else {
                Log::error('LinkedIn Profile Token Refresh Error', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'account_id' => $this->account->id
                ]);

                throw new \Exception('Failed to refresh LinkedIn token');
            }
        } catch (\Exception $e) {
            Log::error('LinkedIn Profile Token Refresh Exception', [
                'error' => $e->getMessage(),
                'account_id' => $this->account->id
            ]);

            throw new \Exception('Failed to refresh LinkedIn token: ' . $e->getMessage());
        }
    }
}
