<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\Publish;

use App\Models\Post;


class ProcessPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Post::scheduled()
            ->withWhereHas('postContents', function ($query) {
                $query->with('account');
            });

        $query->chunk(100, function ($posts) {
            foreach ($posts as $post) {
                Publish::dispatch($post);
            }
        });
    }
}
