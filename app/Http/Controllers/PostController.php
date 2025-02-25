<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Post\CreateRequest;
use App\Http\Requests\Post\UpdateRequest;

use App\Enums\Post\Status;
use App\Enums\PostContent\Status as PostContentStatus;
use App\Enums\PostContent\Type as PostContentType;
use App\Models\Account;
use App\Models\Post;
use App\Models\PostContent;

use Inertia\Inertia;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $space = Auth::user()->currentSpace;

        if ($request->start && $request->end) {
            $query = Post::where('space_id', $space->id)->latest();
            $query->whereBetween('scheduled_at', [$request->start, $request->end]);
            $query->with('postContents.account');
            $query->where('status', '!=', Status::GHOST);
            $posts = $query->get();

            return response()->json($posts);
        }

        return Inertia::render('Post/Index');
    }


    public function store(CreateRequest $request)
    {
        $user = Auth::user();
        $accounts = Account::where('space_id', $user->currentSpace->id)->get();

        $post = Post::create([
            'workspace_id' => $user->workspace_id,
            'space_id' => $user->currentSpace->id,
            'content' => '',
            'status' => Status::DRAFT,
            'scheduled_at' => $request->scheduled_at,
        ]);

        // load post stats
        foreach ($accounts as $account) {
            PostContent::create([
                'post_id' => $post->id,
                'account_id' => $account->id,
                'status' => PostContentStatus::DRAFT,
                'type' => PostContentType::TEXT,
                'platform' => $account->platform,
            ]);
        }

        return redirect(route('posts.edit', $post->id));
    }

    public function edit($id)
    {

        $space = Auth::user()->currentSpace;

        $post = Post::where('space_id', $space->id)
            ->with('postContents.account')
            ->where('id', $id)
            ->firstOrFail();

        return Inertia::render('Post/Edit/Index', [
            'post' => $post,
            'accounts' => Account::where('space_id', $space->id)->get(),
        ]);
    }
    public function update($id, UpdateRequest $request)
    {
        $workspace = Auth::user()->workspace;

        $post = Post::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();

        $post->update([
            'status' => $request->status,
            'scheduled_at' => $request->scheduled_at,
            'auto_sync' => $request->auto_sync,
        ]);

        return back();
    }

    public function clone($id)
    {
        $space = Auth::user()->currentSpace;

        $post = Post::where('space_id', $space->id)->where('id', $id)->firstOrFail();

        // Clone the post
        $newPost = $post->replicate()->fill([
            'status' => Status::DRAFT,
        ]);
        $newPost->save();

        // Clone the related postContents
        foreach ($post->postContents as $content) {
            $newContent = $content->replicate();
            $newContent->post_id = $newPost->id;
            $newContent->save();

            // clone medias
            foreach ($content->getMedia('medias') as $media) {
                $media->copy(
                    $newContent,
                    'medias'
                );
            }
        }

        session()->flash('flash.banner', 'Post cloned successfully');
        session()->flash('flash.bannerStyle', 'success');

        return Inertia::location(route('posts.edit', $newPost->id));
    }

    public function destroy($id)
    {
        $workspace = Auth::user()->workspace;

        $post = Post::where('workspace_id', $workspace->id)->where('id', $id)->firstOrFail();
        $post->delete();

        session()->flash('flash.banner', 'Post deleted successfully');
        session()->flash('flash.bannerStyle', 'success');

        return redirect(route('posts.index'));
    }
}
