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
use App\Models\Account;
use App\Models\Post;
use App\Models\PostContent;

use Inertia\Inertia;

class PostController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $workspace = Auth::user()->workspace;

        if ($request->start && $request->end) {
            $query = Post::where('workspace_id', $workspace->id)->latest();
            $query->whereBetween('scheduled_at', [$request->start, $request->end]);
            $query->with('postContents.account');
            $query->where('status', '!=', Status::GHOST);
            $posts = $query->get();

            return response()->json($posts);
        }

        return Inertia::render('Post/Index', [
            'accounts' => Account::where('workspace_id', $workspace->id)->get(),
            'post' => $id ? Post::where('workspace_id', $workspace->id)
                ->with('postContents.account')
                ->where('id', $id)
                ->firstOrFail() : null
        ]);
    }

    public function edit($id)
    {
        $workspace = Auth::user()->workspace;

        $post = Post::where('workspace_id', $workspace->id)
            ->where('id', $id)
            ->with('postContents')
            ->firstOrFail();

        return response()->json($post);
    }
    public function store(CreateRequest $request)
    {
        $user = Auth::user();

        $post = Post::create([
            'workspace_id' => $user->workspace_id,
            'space_id' => $user->currentSpace->id,
            'content' => '',
            'status' => Status::GHOST,
            'scheduled_at' => $request->scheduled_at,
        ]);

        // load post stats
        $post->load('postContents');

        return redirect(route('posts.index', $post->id));
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

        // delete all post stats
        PostContent::where('post_id', $post->id)->delete();

        foreach ($request->accounts as $account) {

            $account = Account::where('workspace_id', $workspace->id)->where('id', $account['id'])->firstOrFail();

            // create or update
            PostContent::create([
                'content' => $account['content'],
                'status' => $request->status,
                'account_id' => $account->id,
                'post_id' => $post->id,
                'platform' => $account->platform,
            ]);
        }

        return redirect(route('posts.index'));
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
