<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Post;
use App\Models\PostContent;
use App\Models\Account;

use App\Enums\Post\Status as PostStatus;
use App\Enums\PostContent\Status as PostContentStatus;

use App\Services\Linkedin;

class PublishAtLinkedin implements ShouldQueue
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
        $linkedin = new Linkedin($this->account);
        $data = $linkedin->post($this->post);

        // update post
        $this->post->status = PostStatus::PUBLISHED;
        $this->post->save();

        // update post stat
        $this->postContent->update([
            'published_at' => now(),
            'status' => PostContentStatus::PUBLISHED,
            'url' => $data['url'],
            'platform_id' => $data['id'],
        ]);
    }
}
