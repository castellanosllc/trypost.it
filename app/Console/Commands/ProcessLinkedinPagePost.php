<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\PublishAtLinkedinPage;

use App\Models\Post;
use App\Enums\Platform;

class ProcessLinkedinPagePost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process-linkedin-page-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Linkedin Page Posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Post::scheduled()
            ->withWhereHas('postStats', function ($query) {
                $query->whereNull('status');
                $query->where('platform', Platform::LINKEDIN_PAGE);
                $query->with('account');
            });

        $query->chunk(100, function ($posts) {
            foreach ($posts as $post) {
                PublishAtLinkedinPage::dispatch($post, $post->postStats->first(), $post->postStats->first()->account);
            }
        });
    }
}
