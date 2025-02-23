<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Post;
use App\Models\PostContent;
use App\Models\Account;

use App\Enums\Post\Status as PostStatus;
use App\Enums\PostContent\Status as PostContentStatus;

use App\Services\Twitter;

class PublishAtTwitter implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Post $post,
        public PostContent $postContent,
        public Account $account
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $twitter = new Twitter($this->account);
        $data = $twitter->post($this->postContent);

        // update post
        $this->post->status = PostStatus::PUBLISHED;
        $this->post->save();

        // update post content
        $this->postContent->update([
            'status' => PostContentStatus::PUBLISHED,
            'published_at' => now(),
            'url' => "https://x.com/{$this->account->username}/status/{$data->data->id}",
            'platform_id' => $data->data->id,
        ]);
    }
}
