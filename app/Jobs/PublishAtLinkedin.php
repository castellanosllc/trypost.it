<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Post;
use App\Models\PostStat;
use App\Models\SocialAccount;

use App\Enums\Post\Status as PostStatus;
use App\Enums\PostStat\Status as PostStatStatus;

use App\Services\Linkedin;

class PublishAtLinkedin implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Post $post,
        public PostStat $postStat,
        public SocialAccount $account
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $linkedin = new Linkedin($this->account);
        $data = $linkedin->post($this->post);

        // update post
        $this->post->status = PostStatus::PUBLISHED;
        $this->post->save();

        // update post stat
        $this->postStat->update([
            'published_at' => now(),
            'status' => PostStatStatus::PUBLISHED,
            'url' => $data['url'],
            'platform_id' => $data['id'],
        ]);
    }
}
