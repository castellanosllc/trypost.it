<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\PostContent;

use App\Enums\PostContent\Status;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class LinkedinPage
{
    private string $baseUrl = 'https://api.linkedin.com';
    private string $apiVersion = 'v2';
    private Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function post(PostContent $postContent)
    {
        try {
            // First verify if token is valid
            $this->verifyToken();

            $response = Http::withToken($this->account->access_token)
                ->withHeaders([
                    'X-Restli-Protocol-Version' => '2.0.0',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/{$this->apiVersion}/shares", [
                    'owner' => "urn:li:organization:{$this->account->platform_id}",
                    'text' => [
                        'text' => $postContent->content
                    ],
                    'distribution' => [
                        'linkedInDistributionTarget' => [
                            'visibleToGuest' => true
                        ]
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('LinkedIn Post Error', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'account_id' => $this->account->id
                ]);

                if ($response->status() === 401) {
                    // Try to refresh token and post again
                    $this->refreshToken();
                    return $this->post($postContent); // Try once more
                }
            }

            $response->throw();
            $postData = $response->json();

            // update post content
            $postContent->update([
                'status' => Status::PUBLISHED,
                'published_at' => now(),
                'url' => "https://www.linkedin.com/feed/update/urn:li:share:{$postData['id']}",
                'platform_id' => $postData['id'],
            ]);

            return true;
        } catch (RequestException $e) {
            Log::error('LinkedIn API Error', [
                'error' => $e->getMessage(),
                'response' => $e->response?->json(),
                'account_id' => $this->account->id
            ]);

            throw new \Exception('Failed to post to LinkedIn: ' . $e->getMessage());
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
        // Check if token is expired based on expires_in
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
                Log::error('LinkedIn Token Refresh Error', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'account_id' => $this->account->id
                ]);

                throw new \Exception('Failed to refresh LinkedIn token');
            }
        } catch (\Exception $e) {
            Log::error('LinkedIn Token Refresh Exception', [
                'error' => $e->getMessage(),
                'account_id' => $this->account->id
            ]);

            throw new \Exception('Failed to refresh LinkedIn token: ' . $e->getMessage());
        }
    }
}
