<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use App\Enums\PostContent\Type;
use App\Enums\PostContent\Status;

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
        if ($content->type !== Type::TEXT) {

            // Twitter only allows 4 images per post
            $mediaCount = 0;
            $maxMedia = 0;

            if ($content->type === Type::IMAGE) {
                $maxMedia = 4;
            }

            if ($content->type === Type::VIDEO) {
                $maxMedia = 1;
            }

            foreach ($content->getMedia('medias') as $media) {
                if ($mediaCount >= $maxMedia) {
                    break;
                }

                $twitterImage = $this->upload($media);
                if ($twitterImage) {
                    $mediaIds[] = strval($twitterImage->media_id);
                    $mediaCount++;
                }
            }
        }

        if ($mediaIds) {
            $data['media'] = [
                'media_ids' => $mediaIds
            ];
        }

        $this->connection->setApiVersion('2');
        $data = $this->connection->post("tweets", $data);

        $content->update([
            'status' => Status::PUBLISHED,
            'published_at' => now(),
            'url' => "https://x.com/{$content->account->username}/status/{$data->data->id}",
            'platform_id' => $data->data->id,
        ]);

        return true;
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
