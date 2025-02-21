<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\PublishAtTwitter;

use App\Models\Post;
use App\Enums\Platform;


class ProcessTwitterPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process-twitter-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Twitter Posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Post::scheduled()
            ->withWhereHas('postContents', function ($query) {
                $query->whereNull('status');
                $query->where('platform', Platform::TWITTER);
                $query->with('account');
            });

        $query->chunk(100, function ($posts) {
            foreach ($posts as $post) {
                PublishAtTwitter::dispatch($post, $post->postContents->first(), $post->postContents->first()->account);
            }
        });
    }
}
