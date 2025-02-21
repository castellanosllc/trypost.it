<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Post;
use App\Models\PostStat;
use App\Models\Account;

use App\Enums\Post\Status as PostStatus;
use App\Enums\PostStat\Status as PostStatStatus;

use App\Services\LinkedinPage;

class PublishAtLinkedinPage implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Post $post,
        public PostStat $postStat,
        public Account $account
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $linkedin = new LinkedinPage($this->account);
        $data = $linkedin->post($this->post);

        // update post
        $this->post->status = PostStatus::PUBLISHED;
        $this->post->save();

        // update post stat
        $this->postStat->update([
            'status' => PostStatStatus::PUBLISHED,
            'published_at' => now(),
            'url' => $data['url'],
            'platform_id' => $data['id'],
        ]);
    }
}
