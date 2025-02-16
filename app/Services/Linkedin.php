<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReAuthNotification;
use Carbon\Carbon;

use App\Models\Account;
use App\Models\Post;

class Linkedin
{
    /**
     * Publishes a post to LinkedIn
     *
     * @param Post $post
     * @param Account $account
     * @return array
     */
    public function publish(Post $post, Account $account)
    {
        // Check if token is still valid
        if ($this->isTokenExpired($account)) {
            $this->notifyUserToRelog($account);
            return ['error' => 'Access token expired. User notified to re-authenticate.'];
        }

        // Prepare the payload
        $payload = [
            "author" => "urn:li:person:" . $account->account_id,
            "lifecycleState" => "PUBLISHED",
            "specificContent" => [
                "com.linkedin.ugc.ShareContent" => [
                    "shareCommentary" => [
                        "text" => $post->content
                    ],
                    "shareMediaCategory" => "NONE"
                ]
            ],
            "visibility" => [
                "com.linkedin.ugc.MemberNetworkVisibility" => "PUBLIC"
            ]
        ];

        // Make the request
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $account->access_token,
            'X-Restli-Protocol-Version' => '2.0.0'
        ])->post('https://api.linkedin.com/v2/ugcPosts', $payload);

        if ($response->failed()) {
            return ['error' => 'Failed to publish post.', 'response' => $response->json()];
        }

        // Update post status
        $post->accounts()->updateExistingPivot($account->id, [
            'status' => 'published',
            'published_at' => Carbon::now(),
            'response' => json_encode($response->json())
        ]);

        return ['success' => 'Post published successfully.', 'response' => $response->json()];
    }

    /**
     * Checks if the token is expired.
     *
     * @param Account $account
     * @return bool
     */
    private function isTokenExpired(Account $account)
    {
        return Carbon::now()->greaterThan(Carbon::parse($account->token_expires_at));
    }

    /**
     * Sends an email notification to the user for re-authentication.
     *
     * @param Account $account
     */
    private function notifyUserToRelog(Account $account)
    {
        $user = $account->workspace->users()->first();
        if ($user) {
            Mail::to($user->email)->send(new ReAuthNotification($account));
        }
    }
}
