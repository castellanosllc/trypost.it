<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\Post;

use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter
{
    private $connection;

    public function __construct(Account $account)
    {
        $this->connection = new TwitterOAuth(
            config('services.twitter.client_id'),
            config('services.twitter.client_secret'),
            $account->access_token,
            $account->refresh_token
        );
        $this->connection->setTimeouts(20, 25);
        $this->connection->setApiVersion('2');
    }

    public function post(Post $post)
    {
        $data = [
            'text' => $post->content,
        ];

        return $this->connection->post("tweets", $data);
    }
}
