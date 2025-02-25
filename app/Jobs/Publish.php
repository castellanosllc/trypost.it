<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Services\Linkedin;
use App\Services\LinkedinPage;
use App\Services\Twitter;
use App\Services\Tiktok;

use App\Enums\Platform;
use App\Enums\Post\Status;
use App\Enums\PostContent\Status as PostContentStatus;

use App\Models\Post;

class Publish implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Post $post
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // publish post contents
        foreach ($this->post->postContents as $postContent) {
            switch ($postContent->account->platform) {
                case Platform::LINKEDIN:
                    if($postContent->status === PostContentStatus::PUBLISHED) {
                        $linkedin = new Linkedin($postContent->account);
                        $linkedin->post($postContent);
                    }
                    break;
                case Platform::LINKEDIN_PAGE:
                    if($postContent->status === PostContentStatus::PUBLISHED) {
                        $linkedin = new LinkedinPage($postContent->account);
                        $linkedin->post($postContent);
                    }
                    break;
                case Platform::TWITTER:
                    if($postContent->status === PostContentStatus::PUBLISHED) {
                        $twitter = new Twitter($postContent->account);
                        $twitter->post($postContent);
                    }
                    break;
            }
        }

        // determine post status
        $totalContents = $this->post->postContents->count();
        $publishedCount = $this->post->postContents
        ->where('status', PostContentStatus::PUBLISHED)
        ->count();

        if ($publishedCount === $totalContents) {
            $this->post->update([
                'status' => Status::PUBLISHED,
            ]);
        } elseif ($publishedCount > 0) {
            $this->post->update([
                'status' => Status::PARTIALLY_PUBLISHED,
            ]);
        } else {
            $this->post->update([
                'status' => Status::FAILED,
            ]);
        }
    }
}
