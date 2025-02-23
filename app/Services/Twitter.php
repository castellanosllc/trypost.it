<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use App\Models\Account;
use App\Models\PostContent;

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

    public function post(PostContent $content)
    {
        $data = [
            'text' => $content->content,
        ];

        $mediaIds = [];
        if ($content->getMedia('medias') && count($content->getMedia('medias')) >= 1) {
            foreach ($content->getMedia('medias') as $media) {
                $twitterImage = $this->upload($media);
                if ($twitterImage) {
                    $mediaIds[] = strval($twitterImage->media_id);
                }
            }
        }

        if ($mediaIds) {
            $data['media'] = [
                'media_ids' => $mediaIds
            ];
        }

        $this->connection->setApiVersion('2');

        return $this->connection->post("tweets", $data);
    }

    public function upload($media)
    {
        $contents = Storage::get($media->getPath());
        $dir = sys_get_temp_dir();
        $tempFile = tempnam($dir, $media->file_name);
        file_put_contents($tempFile, $contents);

        $this->connection->setApiVersion('1.1');
        $media = $this->connection->upload('media/upload', ['media' => $tempFile]);

        unlink($tempFile);
        return $media;
    }
}
